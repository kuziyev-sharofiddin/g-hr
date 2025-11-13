<?php

namespace App\Http\Controllers\Web\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookGenreRequest;
use App\Http\Requests\Book\UpdateBookGenreRequest;
use App\Models\Book\Genre;
use App\Models\Book\GenreCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookGenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Genre::query()->with('genreCategories');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortKey = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortKey, $sortDirection);
        }

        // Pagination
        $genres = $query->paginate(10)->withQueryString();

        // Transform data for frontend
        $genres->getCollection()->transform(function ($genre) {
            return [
                'id' => $genre->id,
                'name' => $genre->name,
                'name_ru' => $genre->name_ru,
                'responsible_worker' => $genre->responsible_worker,
                'category' => $genre->genreCategories->first()?->name_uz ?? '-',
                'date' => $genre->created_at->format('d.m.Y'),
            ];
        });

        // Get all categories for dropdown
        $categories = GenreCategory::all(['id', 'name_uz as name']);

        return Inertia::render('books/book_genres', [
            'genres' => $genres,
            'categories' => $categories,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookGenreRequest $request)
    {
        try {
            $genre = Genre::create($request->validated());

            return redirect()->back()->with('success', 'Kitob janri muvaffaqiyatli qo\'shildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookGenreRequest $request, Genre $bookGenre)
    {
        try {
            $bookGenre->update($request->validated());

            return redirect()->back()->with('success', 'Kitob janri muvaffaqiyatli yangilandi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $bookGenre)
    {
        try {
            $bookGenre->delete();

            return redirect()->back()->with('success', 'Kitob janri muvaffaqiyatli o\'chirildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
