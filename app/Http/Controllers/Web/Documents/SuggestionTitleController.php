<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SuggestionTitleController extends Controller
{
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query
        $query = SuggestionTitle::withTrashed();

        // Apply search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query = $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('responsible_worker', 'like', "%{$searchTerm}%");
            });
        }

        // Apply sorting
        $sortKey = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query = $query->orderBy($sortKey, $sortDirection);
        }

        // Paginate
        $suggestions = $query->paginate($perPage)->withQueryString();

        // Transform data similar to SuggestionTitleResource
        $suggestions->getCollection()->transform(function ($suggestion) {
            return [
                'id' => $suggestion->id,
                'code' => convertIdToCode($suggestion->id),
                'name' => $suggestion->name,
                'responsible_worker' => $suggestion->responsible_worker,
                'date' => date_format($suggestion->created_at, 'd.m.Y H:i'),
                'deleted_at' => $suggestion->deleted_at,
            ];
        });

        return Inertia::render('documents/suggestion_titles', [
            'suggestions' => $suggestions,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'responsible_worker' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Murojaat sababi nomi kiritilmagan',
            'name.string' => 'Murojaat sababi nomi matn bo\'lishi kerak',
            'name.max' => 'Murojaat sababi nomi 255 belgidan oshmasligi kerak',
        ]);

        SuggestionTitle::create([
            'name' => $request->name,
            'responsible_worker' => "Super Admin"
        ]);

        return redirect()->route('documents.suggestion-titles')->with('success', 'Murojaat sababi muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, SuggestionTitle $suggestion)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'responsible_worker' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Murojaat sababi nomi kiritilmagan',
            'name.string' => 'Murojaat sababi nomi matn bo\'lishi kerak',
            'name.max' => 'Murojaat sababi nomi 255 belgidan oshmasligi kerak',
        ]);

        $suggestion->update([
            'name' => $request->name,
            'responsible_worker' => "Super Admin"
        ]);

        return redirect()->route('documents.suggestion-titles')->with('success', 'Murojaat sababi muvaffaqiyatli yangilandi');
    }

    public function destroy(SuggestionTitle $suggestion)
    {
        // Check if can be deleted (created today)
        if (!$suggestion->created_at->isToday()) {
            return redirect()->route('documents.suggestion-titles')->with('error', 'Faqat bugun yaratilgan yozuvlarni o\'chirish mumkin');
        }

        $suggestion->delete();

        return redirect()->route('documents.suggestion-titles')->with('success', 'Murojaat sababi muvaffaqiyatli o\'chirildi');
    }

    public function restore($id)
    {
        $suggestion = SuggestionTitle::withTrashed()->findOrFail($id);

        // Check if can be restored (created today)
        if (!$suggestion->created_at->isToday()) {
            return redirect()->route('documents.suggestion-titles')->with('error', 'Faqat bugun yaratilgan yozuvlarni tiklash mumkin');
        }

        $suggestion->restore();

        return redirect()->route('documents.suggestion-titles')->with('success', 'Murojaat sababi muvaffaqiyatli tiklandi');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:suggestion_titles,id',
        ], [
            'ids.required' => 'O\'chirish uchun murojaat sababi tanlanmagan',
            'ids.array' => 'Ma\'lumot formati noto\'g\'ri',
            'ids.*.integer' => 'Murojaat sababi ID raqam bo\'lishi kerak',
            'ids.*.exists' => 'Tanlangan murojaat sababi topilmadi',
        ]);

        $suggestions = SuggestionTitle::whereIn('id', $request->ids)->get();

        // Check if all can be deleted (created today)
        foreach ($suggestions as $suggestion) {
            if (!$suggestion->created_at->isToday()) {
                return redirect()->route('documents.suggestion-titles')->with('error', 'Faqat bugun yaratilgan yozuvlarni o\'chirish mumkin');
            }
        }

        SuggestionTitle::whereIn('id', $request->ids)->delete();

        return redirect()->route('documents.suggestion-titles')->with('success', count($request->ids) . ' ta murojaat sababi muvaffaqiyatli o\'chirildi');
    }

    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer',
        ], [
            'ids.required' => 'Tiklash uchun murojaat sababi tanlanmagan',
            'ids.array' => 'Ma\'lumot formati noto\'g\'ri',
            'ids.*.integer' => 'Murojaat sababi ID raqam bo\'lishi kerak',
        ]);

        $suggestions = SuggestionTitle::withTrashed()->whereIn('id', $request->ids)->get();

        // Check if all can be restored (created today)
        foreach ($suggestions as $suggestion) {
            if (!$suggestion->created_at->isToday()) {
                return redirect()->route('documents.suggestion-titles')->with('error', 'Faqat bugun yaratilgan yozuvlarni tiklash mumkin');
            }
        }

        SuggestionTitle::withTrashed()->whereIn('id', $request->ids)->restore();

        return redirect()->route('documents.suggestion-titles')->with('success', count($request->ids) . ' ta murojaat sababi muvaffaqiyatli tiklandi');
    }
}
