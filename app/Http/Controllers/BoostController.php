<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BoostPayment;
use Illuminate\Support\Facades\Auth;

class BoostController extends Controller
{
    /**
     * Affiche le formulaire pour demander un boost
     * Cette vue est accessible à l'utilisateur connecté
     */
    public function index()
    {
        return view('boost.index'); // retourne la vue boost.index (formulaire)
    }

    /**
     * Soumission de la demande de boost
     * Ici l'utilisateur remplit le formulaire et upload la capture de paiement
     */
    public function boostPay(Request $request)
    {
        $user = Auth::user(); // récupère l'utilisateur connecté

        // Validation des données du formulaire
        $request->validate([
            'ads_count' => 'required|integer|min:1|max:10', // nombre d'annonces à booster (max 10)
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', // capture de paiement
        ]);

        // Stockage de la capture de paiement dans le dossier public/storage/boost_proofs
        $proofPath = $request->file('payment_proof')->store('boost_proofs', 'public');

        // Création de la demande de boost dans la base de données
        BoostPayment::create([
            'user_id' => $user->id, // relie la demande à l'utilisateur
            'ads_count' => $request->ads_count, // nombre d'annonces à booster
            'amount' => $request->ads_count * 5000, // montant total (5000 GNF par annonce)
            'start_date' => now(), // date de création
            'end_date' => now()->addDay(), // boost valable 1 jour
            'status' => 'pending', // statut initial : paiement non vérifié
            'payment_proof' => $proofPath, // chemin de la capture de paiement
        ]);

        // Retour à la page précédente avec message de succès
        return back()->with('success', 'Demande de boost enregistrée. Veuillez effectuer le paiement via Orange Money et informer l’admin.');
    }

    /**
     * Tableau de bord pour l'admin
     * L'admin peut voir toutes les demandes de boost avec l'utilisateur
     */
    public function adminDashboard()
    {
        // Récupère toutes les demandes de boost, les plus récentes en premier
        $payments = BoostPayment::with('user')->latest()->get();

        // Retourne la vue admin.boosts avec les données
        return view('admin.boosts', compact('payments'));
    }

    /**
     * Action pour l'admin de valider le paiement
     * Après vérification manuelle de l'Orange Money, les annonces sont boostées
     */
    public function approvePayment($id)
    {
        // Ici tu peux ajouter une vérification que l'utilisateur est admin
        // $this->authorize('isAdmin'); // si tu utilises une policy Laravel

        // Récupère la demande de boost par son id
        $payment = BoostPayment::findOrFail($id);

        // Met à jour le statut du paiement en 'paid'
        $payment->status = 'paid';
        $payment->save(); // sauvegarde la modification

        // Récupère l'utilisateur qui a fait la demande
        $user = $payment->user;

        // Récupère les annonces les plus récentes de l'utilisateur à booster
        $adsToBoost = $user->ads() // relation avec le modèle Ads
            ->orderByDesc('created_at')
            ->take($payment->ads_count)
            ->get();

        // Pour chaque annonce, on définit la date de fin du boost
        foreach ($adsToBoost as $ad) {
            $ad->boosted_until = now()->addDay(); // boost valable 1 jour
            $ad->save(); // sauvegarde l'annonce
        }

        // Retour à la page précédente avec message de succès
        return back()->with('success', 'Paiement confirmé et annonces boostées.');
    }
}
