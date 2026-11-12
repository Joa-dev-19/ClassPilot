
<?php

use App\Http\Controllers\Admin\ClasseController;
use App\Http\Controllers\Admin\FiliereController;
use App\Http\Controllers\Admin\UtilisateurController;
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

Route::middleware(['auth', 'account.active', 'verified', 'role:administrateur'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/filieres', [FiliereController::class, 'index'])
            ->name('filieres.index');

        Route::post('/filieres', [FiliereController::class, 'store'])
            ->name('filieres.store');

        Route::put('/filieres/{filiere}', [FiliereController::class, 'update'])
            ->name('filieres.update');

        Route::post('/filieres/{filiere}/classes', [ClasseController::class, 'store'])
            ->name('classes.store');

        Route::put('/filieres/{filiere}/classes/{classe}', [ClasseController::class, 'update'])
            ->scopeBindings()
            ->name('classes.update');

        Route::get('/utilisateurs', [UtilisateurController::class, 'index'])
            ->name('utilisateurs.index');

        Route::get('/utilisateurs/{user}', [UtilisateurController::class, 'show'])
            ->name('utilisateurs.show');

        Route::patch('/utilisateurs/{user}/activer', [UtilisateurController::class, 'activate'])
            ->name('utilisateurs.activate');

        Route::patch('/utilisateurs/{user}/desactiver', [UtilisateurController::class, 'deactivate'])
            ->name('utilisateurs.deactivate');

        Route::patch('/utilisateurs/{user}/classe', [UtilisateurController::class, 'updateClasse'])
            ->name('utilisateurs.classe.update');
    });

require __DIR__.'/auth.php';
