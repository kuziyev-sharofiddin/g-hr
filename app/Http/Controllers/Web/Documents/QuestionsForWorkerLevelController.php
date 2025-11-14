<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionsForWorkerLevelController extends Controller
{
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query
        $query = QuestionForWorkerLevel::query();

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
        $levels = $query->paginate($perPage)->withQueryString();

        // Transform data similar to Resource
        $levels->getCollection()->transform(function ($level) {
            return [
                'id' => $level->id,
                'code' => convertIdToCode($level->id),
                'name' => $level->name,
                'responsible_worker' => $level->responsible_worker,
                'date' => date_format($level->created_at, 'd.m.Y'),
            ];
        });

        return Inertia::render('documents/question_for_worker_levels', [
            'levels' => $levels,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:question_for_worker_levels,name',
        ], [
            'name.required' => 'Daraja nomi kiritilmagan',
            'name.string' => 'Daraja nomi matn bo\'lishi kerak',
            'name.max' => 'Daraja nomi 255 belgidan oshmasligi kerak',
            'name.unique' => 'Bunday daraja nomi allaqachon mavjud',
        ]);

        QuestionForWorkerLevel::create([
            'name' => $request->name,
            'responsible_worker' => 'Super Admin',
        ]);

        return redirect()->route('documents.question-for-worker-levels')->with('success', 'Daraja muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, QuestionForWorkerLevel $questionForWorkerLevel)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:question_for_worker_levels,name,' . $questionForWorkerLevel->id,
        ], [
            'name.required' => 'Daraja nomi kiritilmagan',
            'name.string' => 'Daraja nomi matn bo\'lishi kerak',
            'name.max' => 'Daraja nomi 255 belgidan oshmasligi kerak',
            'name.unique' => 'Bunday daraja nomi allaqachon mavjud',
        ]);

        try {
            $questionForWorkerLevel->update([
                'name' => $request->name,
                'responsible_worker' => 'Super Admin',
            ]);

            return back()->with('success', 'Daraja muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            \Log::error('Update level error: ' . $e->getMessage());
            return back()->with('error', 'Yangilashda xatolik: ' . $e->getMessage());
        }
    }

    public function destroy(QuestionForWorkerLevel $questionForWorkerLevel)
    {
        try {
            $questionForWorkerLevel->delete();

            return back()->with('success', 'Daraja muvaffaqiyatli o\'chirildi');
        } catch (\Exception $e) {
            \Log::error('Delete level error: ' . $e->getMessage());
            return back()->with('error', 'O\'chirishda xatolik: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:question_for_worker_levels,id',
        ], [
            'ids.required' => 'O\'chirish uchun daraja tanlanmagan',
            'ids.array' => 'Ma\'lumot formati noto\'g\'ri',
            'ids.*.integer' => 'Daraja ID raqam bo\'lishi kerak',
            'ids.*.exists' => 'Tanlangan daraja topilmadi',
        ]);

        QuestionForWorkerLevel::whereIn('id', $request->ids)->delete();

        return redirect()->route('documents.question-for-worker-levels')->with('success', count($request->ids) . ' ta daraja muvaffaqiyatli o\'chirildi');
    }
}
