<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->search;

        $query = Branch::query();

        // Apply search
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('address', 'like', '%' . $search . '%')
                ->orWhere('responsible_worker', 'like', '%' . $search . '%');
        }

        // Paginate
        $branches = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('documents/branch', [
            'branches' => $branches,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:50',
            'target' => 'nullable|string',
            'location' => 'nullable|string',
            'responsible_worker' => 'nullable|string|max:255',
        ]);

        Branch::create($validated);

        return redirect()->back()->with('success', 'Filial muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:50',
            'target' => 'nullable|string',
            'location' => 'nullable|string',
            'responsible_worker' => 'nullable|string|max:255',
        ]);

        $branch->update($validated);

        return redirect()->back()->with('success', 'Filial muvaffaqiyatli yangilandi');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();

        return redirect()->back()->with('success', 'Filial muvaffaqiyatli o\'chirildi');
    }
}
