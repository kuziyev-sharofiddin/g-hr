<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->search;

        $query = Position::query()->with('section');

        // Apply search
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('responsible_worker', 'like', '%' . $search . '%');
        }

        // Paginate
        $positions = $query->latest()->paginate(20)->withQueryString();

        // Get sections for dropdown
        $sections = Section::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('documents/positions', [
            'positions' => $positions,
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
            'section_id' => 'nullable|exists:sections,id',
            'responsible_worker' => 'nullable|string|max:255',
        ]);

        Position::create($validated);

        return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section_id' => 'nullable|exists:sections,id',
            'responsible_worker' => 'nullable|string|max:255',
        ]);

        $position->update($validated);

        return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli yangilandi');
    }

    public function destroy(Position $position)
    {
        $position->delete();

        return redirect()->back()->with('success', 'Lavozim muvaffaqiyatli o\'chirildi');
    }
}
