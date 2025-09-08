<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;
use App\Models\Ad;  
use App\Http\Controllers\BoostController;
use App\Http\Controllers\MessageController;
use App\Models\BoostPayment;


Route::get('/dashboard', function () {
    $userId = Auth::id();

    // Toutes les annonces de l'utilisateur
    $ads = Ad::where('user_id', $userId)->latest()->paginate(20);

    // Dernier paiement
    $lastPayment = BoostPayment::where('user_id', $userId)->latest()->first();

    return view('dashboard', compact('ads', 'lastPayment'));

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
// Route pour approuver tous les paiements en attente
Route::post('/admin/boosts/approve-all', [BoostController::class, 'approveAllPending'])
    ->name('superadmin.boost.approveAll');

   /// Page du chat pour une demande de boost
Route::get('/superadmin/boost/{payment}/chat', [MessageController::class, 'showChat'])
    ->name('superadmin.boost.showChat');

// Envoyer un message depuis cette page
Route::post('/superadmin/boost/{payment}/message', [MessageController::class, 'sendMessage'])
    ->name('superadmin.boost.message');


    // Chat utilisateur
Route::get('/user/boost/{payment}/chat', [MessageController::class, 'userChat'])
    ->name('user.chat');

Route::post('/user/boost/{payment}/reply', [MessageController::class, 'userReply'])
    ->name('user.chat.reply');

Route::delete('/user/boost/message/{message}', [MessageController::class, 'deleteMessage'])->name('message.delete');

Route::delete('/user/boost/{payment}/messages', [MessageController::class, 'deleteAllMessages'])
    ->name('messages.deleteAll');


    Route::get('/user/unread-messages', [MessageController::class, 'unreadMessages'])
     ->middleware('auth')
     ->name('user.unread-messages');

Route::delete('/superadmin/boost/delete-all-confirmed', [BoostController::class, 'deleteAllConfirmedBoosts'])
    ->name('superadmin.boost.deleteAllConfirmed');
;

});


require __DIR__.'/auth.php';

//ROUTE PUBLIC

Route::get('/', [AdController::class, 'acceuil'])->name('home'); // Page d'accueil avec les annonces
Route::get('annonces/{ad}', [AdController::class, 'show'])->name('annonces.show');
Route::get('/user/{id}', [App\Http\Controllers\UserController::class, 'show'])->name('user.show');





