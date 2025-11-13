<?php

namespace App\Http\Controllers\Web\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookGenreCategoryRequest;
use App\Http\Requests\Book\UpdateBookGenreCategoryRequest;
use App\Models\Book\GenreCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookGenreCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = GenreCategory::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortKey = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortKey, ['id', 'name_uz', 'created_at'])) {
            $query->orderBy($sortKey, $sortDirection);
        }

        // Pagination
        $categories = $query->paginate(10)->withQueryString();

        // Transform data for frontend
        $categories->getCollection()->transform(function ($category) {
            return [
                'id' => $category->id,
                'name_uz' => $category->name_uz,
                'name_ru' => $category->name_ru,
                'responsible_worker' => $category->responsible_worker,
                'date' => $category->created_at->format('d.m.Y'),
            ];
        });

        return Inertia::render('books/book_genre_categories', [
            'categories' => $categories,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookGenreCategoryRequest $request)
    {
        try {
            GenreCategory::create($request->validated());

            return redirect()->back()->with('success', 'Janr kategoriyasi muvaffaqiyatli qo\'shildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookGenreCategoryRequest $request, GenreCategory $bookGenreCategory)
    {
        try {
            $bookGenreCategory->update($request->validated());

            return redirect()->back()->with('success', 'Janr kategoriyasi muvaffaqiyatli yangilandi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GenreCategory $bookGenreCategory)
    {
        try {
            $bookGenreCategory->delete();

            return redirect()->back()->with('success', 'Janr kategoriyasi muvaffaqiyatli o\'chirildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
