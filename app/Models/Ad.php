<?php

namespace App\Models; // Déclare le namespace du modèle (organisation des classes dans Laravel)

use Illuminate\Database\Eloquent\Model; // Importe la classe de base des modèles Eloquent

class Ad extends Model // Déclare le modèle Ad qui hérite de la classe Eloquent Model
{
    /**
     * 🔒 Liste des attributs qui peuvent être remplis en masse (mass assignment).
     * Cela empêche de remplir des champs non autorisés via $request->all() par exemple.
     */
    protected $fillable = [
        'title',       // Titre de l’annonce
        'category',    // Catégorie de l’annonce
        'description', // Description de l’annonce
        'price',       // Prix de l’annonce
        'phone',       // Numéro de téléphone de l'annonceur
        'whatsapp',  // Numéro WhatsApp de l'annonceur
        'location',    // Localisation de l’annonce
        'user_id',     // Identifiant de l’utilisateur qui a créé l’annonce
        'profile_photo',
        'currency' , // Devise du prix (ex : GNF, USD, EUR)
    ];

    /**
     * 🔗 Relation : Une annonce appartient à un utilisateur (1:N)
     * Cela permet d’accéder à l’auteur de l’annonce avec $ad->user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
        // 'belongsTo' signifie que chaque annonce appartient à un seul utilisateur
    }

    /**
     * 🔗 Relation : Une annonce a plusieurs images (1:N)
     * Cela permet d’accéder aux images liées à une annonce avec $ad->images
     */
    public function images()
    {
        return $this->hasMany(AdImage::class);
        // 'hasMany' signifie qu’une annonce peut avoir plusieurs images
    }


    /**
     * 🔗 Relation : Une annonce peut avoir plusieurs paiements de boost (1:N)
     * Cela permet d’accéder aux paiements de boost liés à une annonce avec $ad->boostPayments
     */
public function boostPayments()
{
    return $this->hasMany(BoostPayment::class, 'user_id', 'user_id');
}

protected $casts = [
    'last_shown_at' => 'datetime',
    'boosted_until' => 'datetime',
];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    // Compte les messages non lus venant des clients
    public function unreadMessagesCount()
    {
        return $this->conversations()
                    ->with('messages')
                    ->get()
                    ->sum(function($conversation){
                        return $conversation->messages
                            ->where('user_id', '<>', $conversation->seller_id) // venant du client
                            ->where('read', false) // non lus
                            ->count();
                    });
    }
    

}
