<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    /**
     * Get sections as JSON for dropdown/select
     */
    public function getList()
    {
        $sections = Section::where('deleted_at', null)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $sections]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with soft deletes
        $query = Section::withTrashed();

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
        $sections = $query->paginate($perPage)->withQueryString();

        return Inertia::render('documents/sections', [
            'sections' => $sections,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSectionRequest $request)
    {
        try {
            DB::beginTransaction();
            $section = Section::create([
                'name' => $request->name,
                'responsible_worker' => auth()->user()->name,
            ]);
            $data = [
                'ticket' => Integration1C::getTicket(),
                'id' => $section->id,
                'name' => $section->name,
            ];
            $url = env('SERVER_M1_HOST') . 'CreateBolimlar';
            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, $url);
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type:application/json', env('1C_API_TOKEN')]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            $result = curl_exec($ch1);
            $guid = json_decode($result)->GUID;

            if ($guid == null) {
                throw new \Exception('1C dan guid qaytmadi va section yaratilmadi');
                $section->forceDelete();
            }

            $section->update(['guid' => $guid]);
            DB::commit();
            return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Filial yaratishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSectionRequest $request, Section $section)
    {
        try {
            DB::beginTransaction();
            $section->update([
                'name' => $request->name,
                'responsible_worker' => auth()->user()->name,
            ]);
            $data = [
                'ticket' => Integration1C::getTicket(),
                'id' => $section->id,
                'name' => $section->name,
                'GUID' => $section->guid,
            ];
            $url = env('SERVER_M1_HOST') . 'UpdateBolimlarByGUID';
            $ch1 = curl_init();
            curl_setopt($ch1, CURLOPT_URL, $url);
            curl_setopt($ch1, CURLOPT_POST, 1);
            curl_setopt($ch1, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch1, CURLOPT_HTTPHEADER, ['Content-Type:application/json', env('1C_API_TOKEN')]);
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch1, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
            $result = curl_exec($ch1);

            if (!$result) {
                throw new \Exception('1C dan guid qaytmadi va section yaratilmadi');
                $section->forceDelete();
            }
            DB::commit();
            return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Bo\'lim muvaffaqiyatli yangilashda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Section $section)
    {
        try {
            // Check if section was created today
            if (!$section->created_at->isToday()) {
                throw new \Exception("Bo'limni faqat yaratilgan kuni o'chirish mumkin.");
            }

            $section->delete(); // Soft delete

            return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete sections
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday bo\'lim tanlanmagan!');
            }

            // Get sections and check if all were created today
            $sections = Section::whereIn('id', $ids)->get();
            $notToday = $sections->filter(fn($s) => !$s->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi bo'limlar bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan bo'limlarni o'chirish mumkin.");
            }

            $count = Section::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', "{$count} ta bo'lim muvaffaqiyatli o'chirildi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk restore sections
     */
    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday bo\'lim tanlanmagan!');
            }

            // Get deleted sections and check if all were created today
            $sections = Section::onlyTrashed()->whereIn('id', $ids)->get();
            $notToday = $sections->filter(fn($s) => !$s->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi bo'limlar bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan bo'limlarni tiklash mumkin.");
            }

            $count = Section::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()->back()->with('success', "{$count} ta bo'lim muvaffaqiyatli tiklandi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore deleted section
     */
    public function restore($id)
    {
        try {
            $section = Section::withTrashed()->findOrFail($id);

            if (!$section->trashed()) {
                return redirect()->back()->with('error', 'Bu bo\'lim allaqachon aktiv!');
            }

            // Check if section was created today
            if (!$section->created_at->isToday()) {
                throw new \Exception("Bo'limni faqat yaratilgan kuni tiklash mumkin.");
            }

            $section->restore();

            return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli tiklandi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
