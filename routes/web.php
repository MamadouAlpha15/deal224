<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Models\Ad;  
use App\Http\Controllers\BoostController;

Route::get('/dashboard', function () {
    // Récupère uniquement les annonces de l'utilisateur connecté
    $ads = Ad::where('user_id', Auth::id())
              ->latest()
              ->get();

    return view('dashboard', compact('ads'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
   Route::resource('annonces', AdController::class)
        ->except(['show']) // On exclut la route publique
        ->parameters(['annonces' => 'ad']);([      
    
]);


Route::get('/admin/boosts', [BoostController::class, 'adminDashboard'])->name('superadmin.boost');

// Formulaire boost (invisible pour le moment)
    Route::get('/boost', [BoostController::class, 'index'])->name('boost');

    // Soumission du formulaire
    Route::post('/boost-pay', [BoostController::class, 'boostPay'])->name('boost.pay');

    //Route pour approuver le payement

    Route::post('/admin/boosts/{id}/approve', [BoostController::class, 'approvePayment'])->name('superadmin.boost.approve');
    Route::delete('/admin/boosts/{boostPayment}', [BoostController::class, 'deletePayment'])
    ->name('superadmin.boost.delete');

    // Route pour booster toutes les annonces d'un utilisateur
Route::post('/admin/boosts/all/{user}', [BoostController::class, 'boostAllUserAds'])
    ->name('superadmin.boost.all');

});


require __DIR__.'/auth.php';

//ROUTE PUBLIC

Route::get('/', [AdController::class, 'acceuil'])->name('home'); // Page d'accueil avec les annonces
Route::get('annonces/{ad}', [AdController::class, 'show'])->name('annonces.show');





