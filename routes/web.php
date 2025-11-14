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
    // Documents - Hujjatlar (Sections, Branches, Positions)
    Route::get('sections', [\App\Http\Controllers\Web\Documents\SectionController::class, 'index'])->name('documents.sections');
    Route::post('sections', [\App\Http\Controllers\Web\Documents\SectionController::class, 'store'])->name('documents.sections.store');
    Route::put('sections/{section}', [\App\Http\Controllers\Web\Documents\SectionController::class, 'update'])->name('documents.sections.update');
    Route::delete('sections/{section}', [\App\Http\Controllers\Web\Documents\SectionController::class, 'destroy'])->name('documents.sections.destroy');
    Route::post('sections/bulk-delete', [\App\Http\Controllers\Web\Documents\SectionController::class, 'bulkDelete'])->name('documents.sections.bulk-delete');
    Route::post('sections/bulk-restore', [\App\Http\Controllers\Web\Documents\SectionController::class, 'bulkRestore'])->name('documents.sections.bulk-restore');
    Route::post('sections/{id}/restore', [\App\Http\Controllers\Web\Documents\SectionController::class, 'restore'])->name('documents.sections.restore');

    Route::get('branches', [\App\Http\Controllers\Web\Documents\BranchController::class, 'index'])->name('documents.branches');
    Route::post('branches', [\App\Http\Controllers\Web\Documents\BranchController::class, 'store'])->name('documents.branches.store');
    Route::put('branches/{branch}', [\App\Http\Controllers\Web\Documents\BranchController::class, 'update'])->name('documents.branches.update');
    Route::delete('branches/{branch}', [\App\Http\Controllers\Web\Documents\BranchController::class, 'destroy'])->name('documents.branches.destroy');
    Route::post('branches/bulk-delete', [\App\Http\Controllers\Web\Documents\BranchController::class, 'bulkDelete'])->name('documents.branches.bulk-delete');
    Route::post('branches/bulk-restore', [\App\Http\Controllers\Web\Documents\BranchController::class, 'bulkRestore'])->name('documents.branches.bulk-restore');
    Route::post('branches/{id}/restore', [\App\Http\Controllers\Web\Documents\BranchController::class, 'restore'])->name('documents.branches.restore');

    Route::get('positions', [\App\Http\Controllers\Web\Documents\PositionController::class, 'index'])->name('documents.positions');
    Route::post('positions', [\App\Http\Controllers\Web\Documents\PositionController::class, 'store'])->name('documents.positions.store');
    Route::put('positions/{position}', [\App\Http\Controllers\Web\Documents\PositionController::class, 'update'])->name('documents.positions.update');
    Route::delete('positions/{position}', [\App\Http\Controllers\Web\Documents\PositionController::class, 'destroy'])->name('documents.positions.destroy');
    Route::post('positions/bulk-delete', [\App\Http\Controllers\Web\Documents\PositionController::class, 'bulkDelete'])->name('documents.positions.bulk-delete');
    Route::post('positions/bulk-restore', [\App\Http\Controllers\Web\Documents\PositionController::class, 'bulkRestore'])->name('documents.positions.bulk-restore');
    Route::post('positions/{id}/restore', [\App\Http\Controllers\Web\Documents\PositionController::class, 'restore'])->name('documents.positions.restore');

    // Shtatka
    Route::get('shtatka', [\App\Http\Controllers\Web\Documents\ShtatkaController::class, 'index'])->name('documents.shtatka');
    Route::post('shtatka', [\App\Http\Controllers\Web\Documents\ShtatkaController::class, 'store'])->name('documents.shtatka.store');
    Route::put('shtatka/{shtatka}', [\App\Http\Controllers\Web\Documents\ShtatkaController::class, 'update'])->name('documents.shtatka.update');
    Route::delete('shtatka/{shtatka}', [\App\Http\Controllers\Web\Documents\ShtatkaController::class, 'destroy'])->name('documents.shtatka.destroy');

    // Employee Application Titles
    Route::get('application-titles', [\App\Http\Controllers\Web\Documents\EmployeeApplicationTitleController::class, 'index'])->name('documents.application-titles');
    Route::post('application-titles', [\App\Http\Controllers\Web\Documents\EmployeeApplicationTitleController::class, 'store'])->name('documents.application-titles.store');
    Route::put('application-titles/{title}', [\App\Http\Controllers\Web\Documents\EmployeeApplicationTitleController::class, 'update'])->name('documents.application-titles.update');
    Route::delete('application-titles/{title}', [\App\Http\Controllers\Web\Documents\EmployeeApplicationTitleController::class, 'destroy'])->name('documents.application-titles.destroy');

    // Dismissed Worker Reasons
    Route::get('dismissed-worker-reasons', [\App\Http\Controllers\Web\Documents\DismissedWorkerReasonController::class, 'index'])->name('documents.dismissed-worker-reasons');
    Route::post('dismissed-worker-reasons', [\App\Http\Controllers\Web\Documents\DismissedWorkerReasonController::class, 'store'])->name('documents.dismissed-worker-reasons.store');
    Route::put('dismissed-worker-reasons/{reason}', [\App\Http\Controllers\Web\Documents\DismissedWorkerReasonController::class, 'update'])->name('documents.dismissed-worker-reasons.update');
    Route::delete('dismissed-worker-reasons/{reason}', [\App\Http\Controllers\Web\Documents\DismissedWorkerReasonController::class, 'destroy'])->name('documents.dismissed-worker-reasons.destroy');

    // Questions For Worker Categories
    Route::get('question-for-worker-categories', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerCategoryController::class, 'index'])->name('documents.question-for-worker-categories');
    Route::post('question-for-worker-categories', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerCategoryController::class, 'store'])->name('documents.question-for-worker-categories.store');
    Route::put('question-for-worker-categories/{category}', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerCategoryController::class, 'update'])->name('documents.question-for-worker-categories.update');
    Route::delete('question-for-worker-categories/{category}', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerCategoryController::class, 'destroy'])->name('documents.question-for-worker-categories.destroy');

    // Questions For Worker Levels
    Route::get('question-for-worker-levels', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerLevelController::class, 'index'])->name('documents.question-for-worker-levels');
    Route::post('question-for-worker-levels', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerLevelController::class, 'store'])->name('documents.question-for-worker-levels.store');
    Route::put('question-for-worker-levels/{level}', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerLevelController::class, 'update'])->name('documents.question-for-worker-levels.update');
    Route::delete('question-for-worker-levels/{level}', [\App\Http\Controllers\Web\Documents\QuestionsForWorkerLevelController::class, 'destroy'])->name('documents.question-for-worker-levels.destroy');

    // Suggestion Titles
    Route::get('suggestion-titles', [\App\Http\Controllers\Web\Documents\SuggestionTitleController::class, 'index'])->name('documents.suggestion-titles');
    Route::post('suggestion-titles', [\App\Http\Controllers\Web\Documents\SuggestionTitleController::class, 'store'])->name('documents.suggestion-titles.store');
    Route::put('suggestion-titles/{title}', [\App\Http\Controllers\Web\Documents\SuggestionTitleController::class, 'update'])->name('documents.suggestion-titles.update');
    Route::delete('suggestion-titles/{title}', [\App\Http\Controllers\Web\Documents\SuggestionTitleController::class, 'destroy'])->name('documents.suggestion-titles.destroy');

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
