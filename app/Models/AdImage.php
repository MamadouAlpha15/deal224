<?php

namespace App\Models; // 🔹 Définit l’espace de noms (namespace) du modèle. Ici : App\Models

use Illuminate\Database\Eloquent\Model; // 🔹 Importe la classe de base Model d’Eloquent (ORM de Laravel)

class AdImage extends Model // 🔹 Déclaration de la classe AdImage qui hérite de Model (ce qui en fait un modèle Eloquent)
{
    /**
     * 🔒 Champs autorisés à être remplis automatiquement (mass assignment)
     * Cela évite d’avoir à les assigner manuellement un par un.
     */
    protected $fillable = [
        'path',   // 📷 Le chemin de l’image enregistrée dans le stockage (storage)
        'ad_id',  // 🔗 L’identifiant de l’annonce à laquelle cette image est liée
    ];

    /**
     * 🔗 Relation inverse : une image appartient à une seule annonce
     * Cela permet d’accéder à l’annonce à partir de l’image avec $image->ad
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
        // belongsTo indique que chaque image est liée à UNE SEULE annonce (relation N:1)
    }
}
