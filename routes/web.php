<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    // Workers
    Route::get('worker/worker-hr-a', [\App\Http\Controllers\Web\WorkerController::class, 'workerHrA'])->name('worker-hr-a');

});

Route::middleware(['auth', 'verified'])->group(function () {
   Route::get('for-filter-branch', [\App\Http\Controllers\Filter\FilterController::class, 'filterBranch'])->name('for-filter-branch');
   Route::get('for-filter-position', [\App\Http\Controllers\Filter\FilterController::class, 'filterPosition'])->name('for-filter-position');
});

// Documents routes
Route::middleware(['auth', 'verified'])->prefix('documents')->group(function () {
    // Documents - Hujjatlar
    Route::get('sections', [\App\Http\Controllers\Web\Documents\SectionController::class, 'index'])->name('documents.sections');
    Route::post('sections', [\App\Http\Controllers\Web\Documents\SectionController::class, 'store'])->name('documents.sections.store');
    Route::put('sections/{section}', [\App\Http\Controllers\Web\Documents\SectionController::class, 'update'])->name('documents.sections.update');
    Route::delete('sections/{section}', [\App\Http\Controllers\Web\Documents\SectionController::class, 'destroy'])->name('documents.sections.destroy');

    Route::get('branches', [\App\Http\Controllers\Web\Documents\BranchController::class, 'index'])->name('documents.branches');
    Route::post('branches', [\App\Http\Controllers\Web\Documents\BranchController::class, 'store'])->name('documents.branches.store');
    Route::put('branches/{branch}', [\App\Http\Controllers\Web\Documents\BranchController::class, 'update'])->name('documents.branches.update');
    Route::delete('branches/{branch}', [\App\Http\Controllers\Web\Documents\BranchController::class, 'destroy'])->name('documents.branches.destroy');

    Route::get('positions', [\App\Http\Controllers\Web\Documents\PositionController::class, 'index'])->name('documents.positions');
    Route::post('positions', [\App\Http\Controllers\Web\Documents\PositionController::class, 'store'])->name('documents.positions.store');
    Route::put('positions/{position}', [\App\Http\Controllers\Web\Documents\PositionController::class, 'update'])->name('documents.positions.update');
    Route::delete('positions/{position}', [\App\Http\Controllers\Web\Documents\PositionController::class, 'destroy'])->name('documents.positions.destroy');

    // Books
    Route::get('books', [\App\Http\Controllers\Web\Book\BookController::class, 'index'])->name('documents.books');

    // Book Authors
    Route::get('book-authors', [\App\Http\Controllers\Web\Book\BookAuthorController::class, 'index'])->name('book-authors.index');
    Route::post('book-authors', [\App\Http\Controllers\Web\Book\BookAuthorController::class, 'store'])->name('book-authors.store');
    Route::put('book-authors/{bookAuthor}', [\App\Http\Controllers\Web\Book\BookAuthorController::class, 'update'])->name('book-authors.update');
    Route::delete('book-authors/{bookAuthor}', [\App\Http\Controllers\Web\Book\BookAuthorController::class, 'destroy'])->name('book-authors.destroy');

    // Book Genres
    Route::get('book-genres', [\App\Http\Controllers\Web\Book\BookGenreController::class, 'index'])->name('book-genres.index');
    Route::post('book-genres', [\App\Http\Controllers\Web\Book\BookGenreController::class, 'store'])->name('book-genres.store');
    Route::put('book-genres/{bookGenre}', [\App\Http\Controllers\Web\Book\BookGenreController::class, 'update'])->name('book-genres.update');
    Route::delete('book-genres/{bookGenre}', [\App\Http\Controllers\Web\Book\BookGenreController::class, 'destroy'])->name('book-genres.destroy');

    // Book Genre Categories
    Route::get('book-genre-categories', [\App\Http\Controllers\Web\Book\BookGenreCategoryController::class, 'index'])->name('book-genre-categories.index');
    Route::post('book-genre-categories', [\App\Http\Controllers\Web\Book\BookGenreCategoryController::class, 'store'])->name('book-genre-categories.store');
    Route::put('book-genre-categories/{bookGenreCategory}', [\App\Http\Controllers\Web\Book\BookGenreCategoryController::class, 'update'])->name('book-genre-categories.update');
    Route::delete('book-genre-categories/{bookGenreCategory}', [\App\Http\Controllers\Web\Book\BookGenreCategoryController::class, 'destroy'])->name('book-genre-categories.destroy');

    // Book Languages
    Route::get('book-languages', [\App\Http\Controllers\Web\Book\BookLanguageController::class, 'index'])->name('book-languages.index');
    Route::post('book-languages', [\App\Http\Controllers\Web\Book\BookLanguageController::class, 'store'])->name('book-languages.store');
    Route::put('book-languages/{bookLanguage}', [\App\Http\Controllers\Web\Book\BookLanguageController::class, 'update'])->name('book-languages.update');
    Route::delete('book-languages/{bookLanguage}', [\App\Http\Controllers\Web\Book\BookLanguageController::class, 'destroy'])->name('book-languages.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
