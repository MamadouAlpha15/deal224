<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoostPayment;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Visit;
use App\Models\Ad;


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
  public function adminDashboard(Request $request)
{
    $depotNumber = $request->input('q'); // récupère le numéro de dépôt tapé dans la barre de recherche

    // Paiements en attente (pending)
    $pendingPayments = BoostPayment::with('user')
        ->where('status', 'pending')
        ->when($depotNumber, function($query) use ($depotNumber) {
            $query->where('depot', $depotNumber); // filtre par numéro de dépôt si fourni
        })
        ->latest()
        ->get();

    // Paiements confirmés (paid)
    $paidPayments = BoostPayment::with('user')
        ->where('status', 'paid')
        ->when($depotNumber, function($query) use ($depotNumber) {
            $query->where('depot', $depotNumber); // filtre par numéro de dépôt si fourni
        })
        ->latest()
        ->get();

    // =======================
    // STATISTIQUES GLOBALES
    // =======================

    // Nombre total de visites du site
    $visitsCount = Visit::count();

    // Nombre total d’annonces créées
    $adsCount = Ad::count();

    // Nombre total de boosts confirmés
    $boostsCount = BoostPayment::where('status', 'paid')->count();

    // Passe les résultats à la vue
    return view('admin.boosts', compact(
        'pendingPayments',
        'paidPayments',
        'visitsCount',
        'adsCount',
        'boostsCount'
    ));
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


/**
 * Supprimer tous les boosts confirmés (paiements avec status 'paid')
 */
public function deleteAllConfirmedBoosts(): \Illuminate\Http\RedirectResponse
{
    $confirmedPayments = BoostPayment::where('status', 'paid')->get();

    foreach ($confirmedPayments as $payment) {
        $payment->delete(); // supprime uniquement les boosts confirmés
    }

    return back()->with('success', 'Tous les boosts confirmés ont été supprimés.');
}

}
/**
 * Pour modifier le prix du boostage, il faut changer la variable $pricePerAd.
 * Elle se trouve dans la méthode index() :
 *   $pricePerAd = 5000;
 * Modifiez cette valeur pour ajuster le prix du boost par annonce.
 * Assurez-vous aussi de mettre à jour la multiplication dans boostPay() :
 *   'amount' => $adsCount * 5000,
 * Si vous souhaitez centraliser ce prix, créez une constante ou une méthode statique dans un modèle ou config.
 */