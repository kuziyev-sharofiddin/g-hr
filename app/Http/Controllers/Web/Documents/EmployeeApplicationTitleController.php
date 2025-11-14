<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeApplicationTitleController extends Controller
{
    /**
     * Get titles as JSON for dropdown/select
     */
    public function getList()
    {
        $titles = EmployeeApplicationTitle::where('deleted_at', null)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $titles]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query with soft deletes
        $query = EmployeeApplicationTitle::withTrashed();

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
        $titles = $query->paginate($perPage)->withQueryString();

        return Inertia::render('documents/application-titles', [
            'titles' => $titles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeApplicationTitleRequest $request)
    {
        EmployeeApplicationTitle::create([
            'name' => $request->name,
            'responsible_worker' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Ariza mavzusi muvaffaqiyatli yaratildi!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeApplicationTitleRequest $request, EmployeeApplicationTitle $applicationTitle)
    {
        $applicationTitle->update([
            'name' => $request->name,
            'responsible_worker' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Ariza mavzusi muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmployeeApplicationTitle $applicationTitle)
    {
        $applicationTitle->delete(); // Soft delete

        return redirect()->back()->with('success', 'Ariza mavzusi muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Bulk delete titles
     */
    public function bulkDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday mavzu tanlanmagan!');
            }

            $count = EmployeeApplicationTitle::whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', "{$count} ta mavzu muvaffaqiyatli o'chirildi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Bulk restore titles
     */
    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return redirect()->back()->with('error', 'Hech qanday mavzu tanlanmagan!');
            }

            $count = EmployeeApplicationTitle::onlyTrashed()->whereIn('id', $ids)->restore();

            return redirect()->back()->with('success', "{$count} ta mavzu muvaffaqiyatli tiklandi!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore deleted title
     */
    public function restore($id)
    {
        try {
            $title = EmployeeApplicationTitle::withTrashed()->findOrFail($id);

            if (!$title->trashed()) {
                return redirect()->back()->with('error', 'Bu mavzu allaqachon aktiv!');
            }

            $title->restore();

            return redirect()->back()->with('success', 'Ariza mavzusi muvaffaqiyatli tiklandi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
