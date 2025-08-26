<?php

namespace App\Models; // 🔹 Déclare le namespace du modèle (ici : App\Models)

use Illuminate\Database\Eloquent\Factories\HasFactory;  // 🔹 Permet d’utiliser les factories pour générer des utilisateurs en test
use Illuminate\Foundation\Auth\User as Authenticatable; // 🔐 Fournit les fonctionnalités d’authentification (login, mot de passe…)
use Illuminate\Notifications\Notifiable;               // 🔔 Permet d’envoyer des notifications à l’utilisateur (mail, etc.)

class User extends Authenticatable // 🔹 La classe User hérite d’Authenticatable pour la gestion des utilisateurs connectés
{
    /** 
     * 🔧 Utilise les traits HasFactory et Notifiable
     * - HasFactory : pour générer des utilisateurs factices
     * - Notifiable : pour envoyer des notifications (email, etc.)
     */
    use HasFactory, Notifiable;

    /**
     * 📥 Liste des champs qui peuvent être remplis automatiquement via un formulaire
     * - Cela protège des failles de type "mass assignment"
     */
    protected $fillable = [
        'name',     // Nom de l'utilisateur
        'email',    // Email de l'utilisateur
        'password', // Mot de passe (sera hashé)
        'phone',
        'role',     // Rôle de l'utilisateur (ex : superadmin, admin, user)
    ];

    /**
     * 🔒 Champs qui seront masqués lors de la conversion en JSON (ex : API ou retour de données)
     * - On cache ici le mot de passe et le token de session
     */
    protected $hidden = [
        'password',        // 🔐 Ne jamais exposer le mot de passe
        'remember_token',  // 🔐 Jeton pour se rappeler de l'utilisateur (optionnel)
    ];

    /**
     * 🔄 Déclare les champs qui doivent être convertis automatiquement (casting)
     * - `email_verified_at` devient un objet DateTime
     * - `password` sera automatiquement hashé quand on l’assigne
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // ✅ pour savoir quand l’email a été vérifié
            'password' => 'hashed',            // 🔐 le mot de passe sera hashé automatiquement
        ];
    }

    /**
     * 🔗 Déclaration de la relation entre User et Ad
     * - Un utilisateur peut avoir plusieurs annonces (relation 1:N)
     * - Grâce à ça, on peut écrire $user->ads pour obtenir toutes ses annonces
     */
    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function isSuperAdmin(){
        return $this->role === 'superadmin';
    }

    public function Admin(){
        return $this->role === 'admin';
    }
}
