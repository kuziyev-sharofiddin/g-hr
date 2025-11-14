<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    /**
     * Get branches as JSON for dropdown/select
     */
    public function getList()
    {
        $branches = Branch::where('deleted_at', null)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $branches]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with filters
        $query = Branch::withTrashed();

        // Apply ModelFilter
        if ($request->hasAny(['id', 'name', 'address', 'phone_number', 'target'])) {
            $query = $query->filter($request->all());
        }

        // Apply search
        if ($request->filled('search')) {
            $query = $query->search($request->search);
        }

        // Apply sorting
        $sortKey = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query = $query->orderBy($sortKey, $sortDirection);
        }

        // Paginate
        $branches = $query->paginate($perPage)->withQueryString();

        return Inertia::render('documents/branch', [
            'branches' => $branches,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request)
    {
        try {
            DB::beginTransaction();
            // Get ticket from 1C
            $data = [
                "token" => "m4MC0ck4Ku7Ul4L2hHy9Yj3Jx9Xi3IQq6tT7l4Lw"
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, env('GENERAL_BASE_URL') . 'GetTicket/');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: ' . env('GENERAL_AUTHORIZATION')
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                curl_close($ch);
                throw new \Exception('1C Ticket olishda xatolik: ' . curl_error($ch));
            }

            curl_close($ch);

            $ticketResponse = json_decode($result);
            if (!$ticketResponse || !isset($ticketResponse->massage->ticket)) {
                throw new \Exception('1C dan ticket olinmadi. Response: ' . $result);
            }

            $ticket = $ticketResponse->massage->ticket;

            // Generate new code for branch
            $latestBranch = Branch::withTrashed()->latest('id')->first();
            $code = str_pad((int)$latestBranch->code + 1, 9, '0', STR_PAD_LEFT);

            // Send new branch to 1C
            $data = [
                "ticket" => $ticket,
                "Kod" => $code,
                "Filial" => $request->name
            ];

            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, env('BRANCH_1C_API_HOST'));
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch1, CURLOPT_HTTPHEADER, [
                'Content-Type:application/json',
                'Authorization: ' . env('GENERAL_AUTHORIZATION')
            ]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            curl_setopt($ch1, CURLOPT_TIMEOUT, 30);

            $result1 = curl_exec($ch1);
            $httpCode1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);

            if (curl_errno($ch1)) {
                curl_close($ch1);
                throw new \Exception('1C ga filial yuborishda xatolik: ' . curl_error($ch1));
            }

            curl_close($ch1);

            $branchResponse = json_decode($result1);
            if (!$branchResponse || !isset($branchResponse->GUID)) {
                throw new \Exception('1C dan GUID olinmadi. Response: ' . $result1);
            }

            $guid = $branchResponse->GUID;

            // Create branch in database
            Branch::create([
                'name' => $request->name,
                'state_id' => $request->state_id,
                'region_id' => $request->region_id,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'target' => $request->target,
                'location' => $request->location,
                'responsible_worker' => $request->responsible_worker,
                'guid' => $guid,
                'code' => $code,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Filial muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Filial yaratishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, Branch $branch)
    {
        try {
            $branch->update([
                'name' => $request->name,
                'state_id' => $request->state_id,
                'region_id' => $request->region_id,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'target' => $request->target,
                'location' => $request->location,
                'responsible_worker' => $request->responsible_worker,
            ]);

            return redirect()->back()->with('success', 'Filial muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Filial yangilashda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        try {
            // Check if branch was created today
            if (!$branch->created_at->isToday()) {
                throw new \Exception("Filialni faqat yaratilgan kuni o'chirish mumkin.");
            }

            $branch->delete(); // Soft delete

            return redirect()->back()->with('success', 'Filial muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete branches
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday filial tanlanmagan!');
            }

            // Get branches and check if all were created today
            $branches = Branch::whereIn('id', $ids)->get();
            $notToday = $branches->filter(fn($b) => !$b->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi filiallar bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan filiallarni o'chirish mumkin.");
            }

            $count = Branch::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', "{$count} ta filial muvaffaqiyatli o'chirildi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk restore branches
     */
    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday filial tanlanmagan!');
            }

            // Get deleted branches and check if all were created today
            $branches = Branch::onlyTrashed()->whereIn('id', $ids)->get();
            $notToday = $branches->filter(fn($b) => !$b->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi filiallar bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan filiallarni tiklash mumkin.");
            }

            $count = Branch::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()->back()->with('success', "{$count} ta filial muvaffaqiyatli tiklandi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore deleted branch
     */
    public function restore($id)
    {
        try {
            $branch = Branch::withTrashed()->findOrFail($id);

            if (!$branch->trashed()) {
                return redirect()->back()->with('error', 'Bu filial allaqachon aktiv!');
            }

            // Check if branch was created today
            if (!$branch->created_at->isToday()) {
                throw new \Exception("Filialni faqat yaratilgan kuni tiklash mumkin.");
            }

            $branch->restore();

            return redirect()->back()->with('success', 'Filial muvaffaqiyatli tiklandi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
