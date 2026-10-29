
<?php

use App\Http\Controllers\CalendrierController;
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
});

require __DIR__.'/auth.php';
