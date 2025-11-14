<?php

namespace App\Http\Controllers\Web\Documents;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QuestionsForWorkerCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        // Get per page value (default: 20)
        $perPage = $request->input('per_page', 20);

        // Build query
        $query = QuestionForWorkerCategory::query();

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
        $categories = $query->paginate($perPage)->withQueryString();

        // Transform data similar to Resource
        $categories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'code' => convertIdToCode($category->id),
                'name' => $category->name,
                'responsible_worker' => $category->responsible_worker,
                'date' => date_format($category->created_at, 'd.m.Y'),
            ];
        });

        return Inertia::render('documents/question_for_worker_categories', [
            'categories' => $categories,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:question_for_worker_categories,name',
        ], [
            'name.required' => 'Kategoriya nomi kiritilmagan',
            'name.string' => 'Kategoriya nomi matn bo\'lishi kerak',
            'name.max' => 'Kategoriya nomi 255 belgidan oshmasligi kerak',
            'name.unique' => 'Bunday kategoriya nomi allaqachon mavjud',
        ]);

        QuestionForWorkerCategory::create([
            'name' => $request->name,
            'responsible_worker' => 'Super Admin',
        ]);

        return redirect()->route('documents.question-for-worker-categories')->with('success', 'Kategoriya muvaffaqiyatli qo\'shildi');
    }

    public function update(Request $request, QuestionForWorkerCategory $questionForWorkerCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:question_for_worker_categories,name,' . $questionForWorkerCategory->id,
        ], [
            'name.required' => 'Kategoriya nomi kiritilmagan',
            'name.string' => 'Kategoriya nomi matn bo\'lishi kerak',
            'name.max' => 'Kategoriya nomi 255 belgidan oshmasligi kerak',
            'name.unique' => 'Bunday kategoriya nomi allaqachon mavjud',
        ]);

        try {
            $questionForWorkerCategory->update([
                'name' => $request->name,
                'responsible_worker' => 'Super Admin',
            ]);

            return back()->with('success', 'Kategoriya muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            \Log::error('Update category error: ' . $e->getMessage());
            return back()->with('error', 'Yangilashda xatolik: ' . $e->getMessage());
        }
    }

    public function destroy(QuestionForWorkerCategory $questionForWorkerCategory)
    {
        try {
            $questionForWorkerCategory->delete();

            return back()->with('success', 'Kategoriya muvaffaqiyatli o\'chirildi');
        } catch (\Exception $e) {
            \Log::error('Delete category error: ' . $e->getMessage());
            return back()->with('error', 'O\'chirishda xatolik: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:question_for_worker_categories,id',
        ], [
            'ids.required' => 'O\'chirish uchun kategoriya tanlanmagan',
            'ids.array' => 'Ma\'lumot formati noto\'g\'ri',
            'ids.*.integer' => 'Kategoriya ID raqam bo\'lishi kerak',
            'ids.*.exists' => 'Tanlangan kategoriya topilmadi',
        ]);

        QuestionForWorkerCategory::whereIn('id', $request->ids)->delete();

        return redirect()->route('documents.question-for-worker-categories')->with('success', count($request->ids) . ' ta kategoriya muvaffaqiyatli o\'chirildi');
    }
}
