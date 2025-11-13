<?php

namespace App\Http\Controllers\Web\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\StoreBookAuthorRequest;
use App\Http\Requests\Book\UpdateBookAuthorRequest;
use App\Models\Book\BookAuthor;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Inertia\Inertia;
use Inertia\Response;

class BookAuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = BookAuthor::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortKey = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'asc');

        if (in_array($sortKey, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortKey, $sortDirection);
        }

        // Pagination
        $authors = $query->paginate(10)->withQueryString();

        // Transform data for frontend
        $authors->getCollection()->transform(function ($author) {
            return [
                'id' => $author->id,
                'name' => $author->name,
                'description' => $author->description,
                'date' => $author->created_at->format('d.m.Y'),
            ];
        });

        return Inertia::render('books/book_authors', [
            'authors' => $authors,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookAuthorRequest $request)
    {
        try {
            $data = $request->validated();
            // $data['worker_id'] = Auth::id();
            BookAuthor::create($data);

            return redirect()->back()->with('success', 'Kitob muallifi muvaffaqiyatli qo\'shildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookAuthorRequest $request, BookAuthor $bookAuthor)
    {
        try {
            $bookAuthor->update($request->validated());

            return redirect()->back()->with('success', 'Kitob muallifи muvaffaqiyatli yangilandi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookAuthor $bookAuthor)
    {
        try {
            $bookAuthor->delete();

            return redirect()->back()->with('success', 'Kitob muallifи muvaffaqiyatli o\'chirildi.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
