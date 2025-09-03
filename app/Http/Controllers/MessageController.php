<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoostMessage;
use App\Models\BoostPayment;

class MessageController extends Controller
{
    // Afficher le chat pour un paiement
    public function showChat($paymentId)
    {
        $payment = BoostPayment::with('user')->findOrFail($paymentId);

        $messages = BoostMessage::with('user')
            ->where('boost_payment_id', $paymentId)
            ->orderBy('created_at')
            ->get();

        return view('admin.chat', compact('payment', 'messages'));
    }

    // Envoyer un message
    public function sendMessage(Request $request, $paymentId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        BoostMessage::create([
            'boost_payment_id' => $paymentId,
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return redirect()->route('superadmin.boost.showChat', $paymentId)
                         ->with('success', 'Message envoyé !');
    }


    public function userChat($paymentId)
{
    $payment = BoostPayment::findOrFail($paymentId);

    $messages = BoostMessage::with('user')
        ->where('boost_payment_id', $paymentId)
        ->orderBy('created_at')
        ->get();

    return view('user.chat', compact('payment', 'messages'));
}

public function userReply(Request $request, $paymentId)
{
    $request->validate([
        'message' => 'required|string'
    ]);

    // On vérifie que le dernier message est bien du superadmin
    $dernier = BoostMessage::where('boost_payment_id', $paymentId)->latest()->first();
    if(!$dernier || $dernier->user->role !== 'superadmin'){
        return back()->with('error', 'Vous ne pouvez pas répondre maintenant.');
    }

    BoostMessage::create([
        'boost_payment_id' => $paymentId,
        'user_id' => auth()->id(),
        'message' => $request->message,
        
    ]);
    if(preg_match('/\d{6,}/', $request->message, $matches)) {
    $payment = BoostPayment::find($paymentId);
    $payment->depot = $matches[0]; // met à jour le dépôt dans la DB
    $payment->save();
}

    return back()->with('success', 'Réponse envoyée.');
}
public function deleteMessage(BoostMessage $message)
{
    $message->delete();
 return back()->with('success', 'Message supprimé.');
}

// Supprimer TOUS les messages d’un paiement
public function deleteAllMessages($paymentId)
{
    BoostMessage::where('boost_payment_id', $paymentId)->delete();

    return back()->with('success', 'Tous les messages ont été supprimés.');
}
   
}
