<?php

namespace App\Http\Controllers\Web\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookLanguageRequest;
use App\Http\Requests\Book\UpdateBookLanguageRequest;
use App\Models\Book\BookLanguage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookLanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = BookLanguage::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortKey = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortKey, $sortDirection);
        }

        // Pagination
        $languages = $query->paginate(10)->withQueryString();

        // Transform data for frontend
        $languages->getCollection()->transform(function ($language) {
            return [
                'id' => $language->id,
                'name' => $language->name,
                'date' => $language->created_at->format('d.m.Y'),
            ];
        });

        return Inertia::render('books/book_languages', [
            'languages' => $languages,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookLanguageRequest $request)
    {
        try {
            BookLanguage::create($request->validated());

            return redirect()->back()->with('success', 'Kitob tili muvaffaqiyatli qo\'shildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookLanguageRequest $request, BookLanguage $bookLanguage)
    {
        try {
            $bookLanguage->update($request->validated());

            return redirect()->back()->with('success', 'Kitob tili muvaffaqiyatli yangilandi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookLanguage $bookLanguage)
    {
        try {
            $bookLanguage->delete();

            return redirect()->back()->with('success', 'Kitob tili muvaffaqiyatli o\'chirildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
