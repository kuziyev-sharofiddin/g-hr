<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DismissedWorkerReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with soft deletes
        $query = DismissedWorkerReason::withTrashed();

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
        $reasons = $query->paginate($perPage)->withQueryString();

        return Inertia::render('documents/dismissed-worker-reasons', [
            'reasons' => $reasons,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDismissedWorkerReasonRequest $request)
    {
        try {
            DismissedWorkerReason::create([
                'name' => $request->name,
                'responsible_worker' => auth()->user()->name,
            ]);
            return redirect()->back()->with('success', 'Ishdan chiqarish sababi muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ishdan chiqarish sababi yaratishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDismissedWorkerReasonRequest $request, DismissedWorkerReason $dismissedWorkerReason)
    {
        try {

            $dismissedWorkerReason->update([
                'name' => $request->name,
                'responsible_worker' => auth()->user()->name,
            ]);
            return redirect()->back()->with('success', 'Ishdan chiqarish sababi muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ishdan chiqarish sababi yangilashda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DismissedWorkerReason $dismissedWorkerReason)
    {
        try {
            // Check if reason was created today
            if (!$dismissedWorkerReason->created_at->isToday()) {
                throw new \Exception("Ishdan chiqarish sababi faqat yaratilgan kuni o'chirish mumkin.");
            }

            $dismissedWorkerReason->delete(); // Soft delete

            return redirect()->back()->with('success', 'Ishdan chiqarish sababi muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk delete dismissed worker reasons
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday ishdan chiqarish sababi tanlanmagan!');
            }

            // Get reasons and check if all were created today
            $reasons = DismissedWorkerReason::whereIn('id', $ids)->get();
            $notToday = $reasons->filter(fn($r) => !$r->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi sabab bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan sabablarni o'chirish mumkin.");
            }

            $count = DismissedWorkerReason::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', "{$count} ta ishdan chiqarish sababi muvaffaqiyatli o'chirildi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk restore dismissed worker reasons
     */
    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday ishdan chiqarish sababi tanlanmagan!');
            }

            // Get deleted reasons and check if all were created today
            $reasons = DismissedWorkerReason::onlyTrashed()->whereIn('id', $ids)->get();
            $notToday = $reasons->filter(fn($r) => !$r->created_at->isToday());

            if ($notToday->isNotEmpty()) {
                $names = $notToday->pluck('name')->take(3)->implode(', ');
                throw new \Exception("Ba'zi sabab bugun yaratilmagan ({$names}...). Faqat bugun yaratilgan sabablarni tiklash mumkin.");
            }

            $count = DismissedWorkerReason::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()->back()->with('success', "{$count} ta ishdan chiqarish sababi muvaffaqiyatli tiklandi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore deleted dismissed worker reason
     */
    public function restore($id)
    {
        try {
            $reason = DismissedWorkerReason::withTrashed()->findOrFail($id);

            if (!$reason->trashed()) {
                return redirect()->back()->with('error', 'Bu ishdan chiqarish sababi allaqachon aktiv!');
            }

            // Check if reason was created today
            if (!$reason->created_at->isToday()) {
                throw new \Exception("Ishdan chiqarish sababi faqat yaratilgan kuni tiklash mumkin.");
            }

            $reason->restore();

            return redirect()->back()->with('success', 'Ishdan chiqarish sababi muvaffaqiyatli tiklandi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
