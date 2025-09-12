<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoostMessage extends Model
{
    /**
    * Modèle BoostMessage
    *
    * Représente un message boost associé à un utilisateur et à un paiement boost.
    *
    * @property int $boost_payment_id  L'identifiant du paiement boost associé.
    * @property int $user_id           L'identifiant de l'utilisateur ayant envoyé le message boost.
    * @property string $message        Le contenu du message boost.
    *
    * Relations :
    * @method User user()              Récupère l'utilisateur ayant envoyé le message boost.
    * @method BoostPayment boostPayment() Récupère le paiement boost associé au message.
     */
    protected $fillable = ['boost_payment_id', 'user_id', 'message', 'read'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function boostPayment() {
        return $this->belongsTo(BoostPayment::class);
    }
}
