<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoostPayment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;


class BoostController extends Controller
{
    /**
     * Affiche le formulaire pour demander un boost
     */
  public function index()
{
    $user = Auth::user(); // Récupère l'utilisateur connecté

    // Nombre d'annonces de l'utilisateur
    $adsCount = $user->ads()->count(); 

    $pricePerAd = 5000; // Prix par annonce
    $totalPrice = $adsCount * $pricePerAd; // Calcul du montant total

    // Génération d'une référence unique
    $reference = strtoupper(Str::random(10));

    // Envoie tout à la vue
    return view('boost.index', compact('adsCount', 'pricePerAd', 'totalPrice', 'reference'));
}

    /**
     * Soumission de la demande de boost
     */
    public function boostPay(Request $request)
    {
        $user = Auth::user();
        $adsCount = $user->ads()->count(); // récupère le nombre total d'annonces

        $request->validate([
            
           'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5 Mo
        ]);

        // Stockage de la capture
        $proofPath = $request->file('payment_proof')->store('boost_proofs', 'public');

        // Génération d'une référence unique
        $reference = strtoupper(uniqid('BOOST_'));

        // Création du paiement
       // Création du paiement
  BoostPayment::create([
    'user_id' => $user->id,
    'ads_count' => $adsCount,           // toutes les annonces
    'amount' => $adsCount * 5000,       // prix total
    'status' => 'pending',
    'payment_proof' => $proofPath,
    'depot' => $request->input('depot'),
    'start_date' => now(),
    'end_date' => now()->addDay(),
    'reference' => $reference,
]);

        return back()->with('success', "Votre demande de boost a été enregistrée. Référence : $reference. L’administrateur validera votre boost dès que possible.");
    }

    /**
     * Tableau de bord pour l'admin
     */
    public function adminDashboard()
    {
        $pendingPayments = BoostPayment::with('user')->where('status', 'pending')->latest()->get();
        $paidPayments = BoostPayment::with('user')->where('status', 'paid')->latest()->get();

        return view('admin.boosts', compact('pendingPayments', 'paidPayments'));
    }

    /**
     * Valider un paiement et booster les annonces correspondantes
     */
    public function approvePayment($id)
    {
        $payment = BoostPayment::findOrFail($id);
        $payment->status = 'paid';
        $payment->save();

        $user = $payment->user;
        $adsToBoost = $user->ads()->orderByDesc('created_at')->take($payment->ads_count)->get();

        foreach ($adsToBoost as $ad) {
            $ad->boosted_until = now()->addDay();
            $ad->save();
        }

        return back()->with('success', 'Paiement confirmé et annonces boostées.');
    }

    /**
     * Booster toutes les annonces d'un utilisateur
     */
    public function boostAllUserAds($userId)
    {
        $user = User::findOrFail($userId);
        $ads = $user->ads()->get();

        foreach ($ads as $ad) {
            $ad->boosted_until = now()->addDay();
            $ad->save();
        }

        return back()->with('success', 'Toutes les annonces de cet utilisateur ont été boostées.');
    }

    /**
     * Supprimer un paiement
     */
    public function deletePayment(BoostPayment $boostPayment)
    {
        $boostPayment->delete();

        return back()->with('success', 'Demande de boost supprimée.');
    }


    /**
 * Valider tous les paiements en attente
 */
public function approveAllPending()
{
    $payments = BoostPayment::where('status','pending')->get();
    foreach($payments as $payment){
        $payment->status = 'paid';
        $payment->save();

        $user = $payment->user;
        $adsToBoost = $user->ads()->orderByDesc('created_at')->take($payment->ads_count)->get();
        foreach ($adsToBoost as $ad) {
            $ad->boosted_until = now()->addDay();
            $ad->save();
        }
    }
    return back()->with('success','Tous les paiements en attente ont été validés.');
}

}
