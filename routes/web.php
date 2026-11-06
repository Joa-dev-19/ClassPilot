
<?php

use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\DevoirController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TacheController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'account.active', 'verified'])->name('dashboard');

Route::middleware(['auth', 'account.active', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');
});

Route::middleware(['auth', 'account.active', 'verified', 'role:etudiant'])->group(function () {
    Route::get('/mes-devoirs', [DevoirController::class, 'studentIndex'])
        ->name('devoirs.etudiants.index');

    Route::get('/mes-devoirs/{devoir}', [DevoirController::class, 'studentShow'])
        ->name('devoirs.etudiants.show');

    Route::get('/calendrier', [CalendrierController::class, 'index'])
        ->name('calendrier.index');

    Route::post('/calendrier/rappels', [CalendrierController::class, 'storeReminder'])
        ->name('calendrier.rappels.store');

    Route::delete('/calendrier/rappels/{rappel}', [CalendrierController::class, 'destroyReminder'])
        ->name('calendrier.rappels.destroy');

    Route::get('/taches', [TacheController::class, 'index'])
        ->name('taches.index');

    Route::get('/taches/creer', [TacheController::class, 'create'])
        ->name('taches.create');

    Route::post('/taches', [TacheController::class, 'store'])
        ->name('taches.store');

    Route::get('/taches/{tache}/modifier', [TacheController::class, 'edit'])
        ->name('taches.edit');

    Route::put('/taches/{tache}', [TacheController::class, 'update'])
        ->name('taches.update');

    Route::delete('/taches/{tache}', [TacheController::class, 'destroy'])
        ->name('taches.destroy');
});

Route::middleware(['auth', 'account.active', 'verified', 'role:enseignant'])->group(function () {
    Route::get('/devoirs', [DevoirController::class, 'index'])
        ->name('devoirs.index');

    Route::get('/devoirs/creer', [DevoirController::class, 'create'])
        ->name('devoirs.create');

    Route::post('/devoirs', [DevoirController::class, 'store'])
        ->name('devoirs.store');

    Route::get('/devoirs/{devoir}/modifier', [DevoirController::class, 'edit'])
        ->name('devoirs.edit');

    Route::put('/devoirs/{devoir}', [DevoirController::class, 'update'])
        ->name('devoirs.update');

    Route::post('/devoirs/{devoir}/publier', [DevoirController::class, 'publish'])
        ->name('devoirs.publish');

    Route::post('/devoirs/{devoir}/archiver', [DevoirController::class, 'archive'])
        ->name('devoirs.archive');

    Route::get('/devoirs/{devoir}', [DevoirController::class, 'show'])
        ->name('devoirs.show');
});

require __DIR__.'/auth.php';
