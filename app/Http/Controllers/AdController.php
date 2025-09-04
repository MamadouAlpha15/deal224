<?php

namespace App\Http\Controllers; // Namespace du contrôleur (organisation des fichiers Laravel)

use Illuminate\Http\Request; // Pour gérer les requêtes HTTP
use App\Models\Ad; // Modèle "Ad" (annonce)
use Illuminate\Support\Facades\Auth; // Pour l'authentification de l'utilisateur connecté
use Illuminate\Support\Facades\Storage; // Pour gérer le stockage (images)
use App\Models\AdImage; // Modèle "AdImage" (image d'une annonce)

class AdController extends Controller
{
    // 🔹 Affiche toutes les annonces de l'utilisateur connecté
 public function index()
{
    $perPage = 20;
    $user = Auth::user();

    if (config('boost.auto_boost')) {
        // Boost automatique gratuit actif : toutes les annonces boostées apparaissent en haut avec rotation
        $ads = $user->ads()
            ->with('images')
            ->orderByDesc('boosted_until') // boostées en haut
            ->orderBy('last_seen_at')      // rotation équitable
            ->orderByDesc('created_at')
            ->paginate($perPage);
    } else {
        // Boost automatique désactivé : seules les annonces payantes sont boostées
        $ads = Ad::with('images')
            ->where('boosted_until', '>', now()) // seulement boost payant encore actif
            ->orderBy('last_seen_at')            // rotation entre payants
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    // --- ROTATION : met à jour la dernière fois vue ---
    foreach ($ads as $ad) {
        $ad->last_seen_at = now();
        $ad->save();
    }

    // Récupère le dernier paiement boost pour le bouton Chat
    $lastPayment = $user->boostPayments()->latest()->first();

    return view('ads.index', compact('ads', 'lastPayment'));
}




    // 🔹 Affiche le formulaire de création d'une annonce
    public function create()
    {
        return view('ads.create'); // Retourne la vue de création
    }

    // 🔹 Enregistre une nouvelle annonce
    public function store(Request $request)
    {
        // ✅ Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255', // Titre obligatoire, chaîne de max 255 caractères
            'description' => 'required|string', // Description obligatoire
            'price' => 'required|integer', // Prix obligatoire (nombre entier)
            'phone' => 'nullable|string|max:20', // Numéro de téléphone optionnel, max 20 caractères
            'whatsapp' => 'required|string|max:20', // Numéro WhatsApp obligatoire, max 20 caractères
            'location' => 'nullable|string|max:255', // Localisation optionnelle, max 255 caractères
            'images.*' => 'image|mimes:jpeg,png,jpg|max:51200', // 50 Mo max par image
        ]);

        // Ajoute l'ID de l'utilisateur connecté à l'annonce
        $validated['user_id'] = Auth::id();

        // Création de l'annonce
        $ad = Ad::create($validated);

        // 📸 Sauvegarde des images si envoyées
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public'); // Stocke dans storage/app/public/images
                $ad->images()->create(['path' => $path]); // Crée l'entrée en base
            }
        }

        // Redirection avec message de succès
        return redirect()->route('annonces.index')->with('success', 'Annonce ajoutée avec succès.');
    }

    // 🔹 Affiche le formulaire de modification d'une annonce
    public function edit($id)
    {
        $ad = Ad::findOrFail($id); // Trouve l'annonce ou échoue

        // Vérifie que l'utilisateur est le propriétaire
        if ($ad->user_id !== Auth::id()) {
            return redirect()->route('annonces.index')->with('error', 'Vous ne pouvez pas modifier cette annonce.');
        }

        // Affiche la vue de modification
        return view('ads.edit', compact('ad'));
    }

    // 🔹 Met à jour une annonce existante
    public function update(Request $request, $id)
    {
        // ✅ Validation des données
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'location' => 'nullable|string|max:255',
            // Les images sont optionnelles, mais si fournies, doivent être valides
            'images.*' => 'image|mimes:jpeg,png,jpg|max:51200', // 50 Mo max par image
        ]);

        $ad = Ad::findOrFail($id); // Récupère l'annonce

        // Vérifie que l'utilisateur est le propriétaire
        if ($ad->user_id !== Auth::id()) {
            return redirect()->route('annonces.index')->with('error', 'Vous ne pouvez pas modifier cette annonce.');
        }

        // 🔄 Met à jour les infos de l'annonce
        $ad->update($validated);

        // 🔥 Supprime les images décochées (non gardées)
        $existingImageIds = $request->input('existing_images', []); // IDs des images à garder

        // Récupère les images à supprimer
        $imagesToDelete = $ad->images()->whereNotIn('id', $existingImageIds)->get();

        foreach ($imagesToDelete as $image) {
            Storage::disk('public')->delete($image->path); // Supprime le fichier
            $image->delete(); // Supprime l'entrée en base
        }

        // 📥 Ajoute les nouvelles images si envoyées
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $ad->images()->create(['path' => $path]);
            }
        }

        // Redirige avec message de succès
        return redirect()->route('annonces.index')->with('success', 'Annonce modifiée avec succès.');
    }

    // 🔹 Supprime une annonce
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id); // Trouve l'annonce

        // Supprime toutes les images liées à l'annonce
        foreach ($ad->images as $image) {
            Storage::disk('public')->delete($image->path); // Supprime le fichier
            $image->delete(); // Supprime l'entrée en base
        }

        // Supprime l'annonce
        $ad->delete();

        // Redirection avec message de succès
        return redirect()->route('annonces.index')->with('success', 'Annonce supprimée avec succès.');
    }

    // 🔹 Affiche les annonces sur la page d'accueil, avec recherche
  public function acceuil(Request $request)
{
    $query = $request->input('q');
    $category = $request->input('category'); // récupère la catégorie sélectionnée

    // Détecte le driver DB
    $nowFunction = \DB::getDriverName() === 'sqlite' ? 'CURRENT_TIMESTAMP' : 'NOW()';

    $ads = Ad::when($query, function ($q) use ($query) {
                return $q->where(function($sub) use ($query) {
                    $sub->where('title', 'like', "%$query%")
                        ->orWhere('description', 'like', "%$query%");
                });
            })
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category); // filtre par catégorie si choisi
            })
            ->with('images')
            ->orderByRaw("CASE WHEN boosted_until >= $nowFunction THEN 1 ELSE 0 END DESC") // annonces boostées en premier
            ->orderByDesc('boosted_until') // puis les plus récentes boostées
            ->orderByDesc('created_at') // ensuite annonces normales par date
            ->paginate(30)
            ->withQueryString(); // conserve les filtres dans la pagination

    return view('welcome', compact('ads', 'query', 'category'));
}


    // 🔹 Affiche les détails d'une annonce
    public function show(Ad $ad)
    {
        $ad->load('images'); // Charge les images de l'annonce
        return view('ads.show', compact('ad')); // Affiche la vue détail
    }
}
