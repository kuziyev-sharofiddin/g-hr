<?php

namespace App\Http\Controllers\Web\Book;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book\Book;
use App\Models\Book\BookAuthor;
use App\Models\Book\Genre;
use App\Models\Book\BookLanguage;
use App\Enums\BookStatus;
use App\Enums\FeatureBookStatus;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BookController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->status ?? 'all';
        $feature = $request->feature;
        $search = $request->search;
        $filters = $request->filters; //  { "authors": "Pushkin", "genres": "ilmiy", "languages": "rus" }

        $query = Book::query()->with([
            'bookAuthor',
            'bookGenre',
            'booksLanguages',
            'booksLanguages.language',
            'featuredBooks',
            'comments'
        ]);

        // Apply filters based on status
        if ($status !== 'all') {
            $query->where('book_status', $status);
        }

        // Apply filters
        if ($filters) {
            if (!empty($filters['authors'])) {
                $query->whereHas('bookAuthor', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['authors'] . '%');
                });
            }
            if (!empty($filters['genres'])) {
                $query->whereHas('bookGenre', function ($q) use ($filters) {
                    $q->where('name', 'like', '%' . $filters['genres'] . '%');
                });
            }
        }

        // Apply search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('bookAuthor', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply sorting/features
        if ($feature) {
            $query->when($feature === 'rating_desc', function ($query) {
                $query->withAvg('featuredBooks as average_rating', 'rating')
                    ->orderByDesc('average_rating');
            })
                ->when($feature === 'date_asc', function ($query) {
                    $query->orderBy('created_at');
                })
                ->when($feature === 'date_desc', function ($query) {
                    $query->orderByDesc('created_at');
                })
                ->when($feature === 'rating_asc', function ($query) {
                    $query->withAvg('featuredBooks as average_rating', 'rating')
                        ->orderBy('average_rating');
                })
                ->when($feature === 'liked_desc', function ($query) {
                    $query->withCount(['featuredBooks as like_count' => function ($query) {
                        $query->where('status', FeatureBookStatus::LIKED->value);
                    }])->orderByDesc('like_count');
                })
                ->when($feature === 'liked_asc', function ($query) {
                    $query->withCount(['featuredBooks as like_count' => function ($query) {
                        $query->where('status', FeatureBookStatus::LIKED->value);
                    }])->orderBy('like_count');
                })
                ->when($feature === 'read_asc', function ($query) {
                    $query->withCount(['featuredBooks as read_count' => function ($query) {
                        $query->where('status', FeatureBookStatus::FINISH_READ->value);
                    }])->orderBy('read_count');
                })
                ->when($feature === 'comment_desc', function ($query) {
                    $query->withCount('comments')->orderBy('comments_count', 'desc');
                })
                ->when($feature === 'comment_asc', function ($query) {
                    $query->withCount('comments')->orderBy('comments_count');
                })
                ->when($feature === 'read_desc', function ($query) {
                    $query->withCount(['featuredBooks as read_count' => function ($query) {
                        $query->where('status', FeatureBookStatus::FINISH_READ->value);
                    }])->orderByDesc('read_count');
                });
        }

        // Paginate
        $books = $query->latest()->paginate(20)->withQueryString();

        // Get filter options
        $filterOptions = [
            'authors' => BookAuthor::select('id', 'name')->orderBy('name')->get(),
            'genres' => Genre::select('id', 'name', 'name_ru')->orderBy('name')->get(),
            'languages' => BookLanguage::select('id', 'name')->orderBy('name')->get(),
        ];

        return Inertia::render('books/books', [
            'books' => $books,
            'filters' => [
                'status' => $status,
                'feature' => $feature,
                'search' => $search,
                'filters' => $filters,
            ],
            'filterOptions' => $filterOptions,
        ]);
    }
}
