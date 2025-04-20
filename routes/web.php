<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\RegionalPhraseController;

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Translation routes - protected by auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/translate', [TranslationController::class, 'create'])->name('translate');
    Route::post('/translate', [TranslationController::class, 'store'])->name('translate.store');
    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::get('/translations/{translation}', [TranslationController::class, 'show'])->name('translations.show');
    Route::put('/translations/{translation}', [TranslationController::class, 'update'])->name('translations.update');
    Route::delete('/translations/{translation}', [TranslationController::class, 'destroy'])->name('translations.destroy');
    Route::delete('/translations', [TranslationController::class, 'destroyAll'])->name('translations.destroy-all');
    Route::get('/feedback', [TranslationController::class, 'feedback'])->name('translations.feedback');
});

// Regional phrases routes - protected by auth middleware
Route::middleware(['auth'])->group(function () {
    Route::get('/regional-phrases', [RegionalPhraseController::class, 'index'])->name('regional-phrases.index');
    Route::get('/regional-phrases/create', [RegionalPhraseController::class, 'create'])->name('regional-phrases.create');
    Route::post('/regional-phrases', [RegionalPhraseController::class, 'store'])->name('regional-phrases.store');
    Route::get('/regional-phrases/{regionalPhrase}', [RegionalPhraseController::class, 'show'])->name('regional-phrases.show');
    Route::get('/regional-phrases/{regionalPhrase}/edit', [RegionalPhraseController::class, 'edit'])->name('regional-phrases.edit');
    Route::put('/regional-phrases/{regionalPhrase}', [RegionalPhraseController::class, 'update'])->name('regional-phrases.update');
    Route::delete('/regional-phrases/{regionalPhrase}', [RegionalPhraseController::class, 'destroy'])->name('regional-phrases.destroy');
});
