<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->search;

        $query = Section::query();

        // Apply search
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('responsible_worker', 'like', '%' . $search . '%');
        }

        // Paginate
        $sections = $query->latest()->paginate(20)->withQueryString();

        return Inertia::render('documents/sections', [
            'sections' => $sections,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'responsible_worker' => 'nullable|string|max:255',
        ]);

        Section::create($validated);

        return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, Section $section)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'responsible_worker' => 'nullable|string|max:255',
        ]);

        $section->update($validated);

        return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli yangilandi');
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->back()->with('success', 'Bo\'lim muvaffaqiyatli o\'chirildi');
    }
}
