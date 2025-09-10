<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;             // Pour gérer les requêtes HTTP
use App\Models\Ad;                        // Modèle des annonces
use Illuminate\Support\Facades\Auth;      // Pour récupérer l'utilisateur connecté
use Illuminate\Support\Facades\Storage;   // Pour gérer le stockage des fichiers
use App\Models\AdImage;                   // Modèle des images des annonces

class AdController extends Controller
{
    // ======================================
    // 🔹 Liste des annonces de l'utilisateur connecté
    // ======================================
    public function index()
    {
        $perPage = 20;                     // Nombre d'annonces par page
        $user = Auth::user();              // Récupère l'utilisateur connecté

        // Récupère toutes les annonces de l'utilisateur, avec leurs images
        $adsQuery = $user->ads()->with('images')
            ->orderBy('boosted_until', 'desc') // Boostées d'abord
            ->orderByDesc('created_at');       // Puis les plus récentes

        $ads = $adsQuery->paginate($perPage); // Pagination

        // Met à jour last_shown_at pour les annonces boostées encore valides
        $boostedIds = $ads->filter(fn($ad) => $ad->boosted_until && $ad->boosted_until >= now())
                           ->pluck('id')
                           ->toArray();

        if (!empty($boostedIds)) {
            Ad::whereIn('id', $boostedIds)->update(['last_shown_at' => now()]);
        }

        // Récupère le dernier paiement boost (pour le chat ou affichage)
        $lastPayment = $user->boostPayments()->latest()->first();

        // Retourne la vue avec les annonces et le dernier paiement
        return view('ads.index', compact('ads', 'lastPayment'));
    }

    // ======================================
    // 🔹 Formulaire de création d'une annonce
    // ======================================
    public function create()
    {
        return view('ads.create');
    }

    // ======================================
    // 🔹 Stocke une nouvelle annonce
    // ======================================
    public function store(Request $request)
    {
        // ✅ Validation des champs
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|integer',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'location' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:51200',
            'profile_photo' => 'image|mimes:jpeg,png,jpg|max:51200',
            'currency' => 'required|in:GNF,EUR,USD',
        ]);

        $validated['user_id'] = Auth::id(); // Associe l'annonce à l'utilisateur

        // 🔹 Stocke la photo de profil si présente
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $validated['profile_photo'] = $path;
        }

        // 🔹 Création de l'annonce
        $ad = Ad::create($validated);

        // 🔹 Stocke toutes les images de l'annonce
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $ad->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('annonces.index')
                         ->with('success', 'Annonce ajoutée avec succès.');
    }

    // ======================================
    // 🔹 Formulaire d'édition d'une annonce
    // ======================================
    public function edit($id)
    {
        $ad = Ad::findOrFail($id);

        // ⚠️ Vérifie que l'utilisateur est bien propriétaire
        if ($ad->user_id !== Auth::id()) {
            return redirect()->route('annonces.index')
                             ->with('error', 'Vous ne pouvez pas modifier cette annonce.');
        }

        return view('ads.edit', compact('ad'));
    }

    // ======================================
    // 🔹 Met à jour une annonce existante
    // ======================================
    public function update(Request $request, $id)
    {
        // ✅ Validation des champs
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'phone' => 'nullable|string|max:20',
            'whatsapp' => 'required|string|max:20',
            'location' => 'nullable|string|max:255',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:51200',
            'profile_photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            'currency' => 'required|in:GNF,EUR,USD',
        ]);

        $ad = Ad::findOrFail($id);

        // ⚠️ Vérifie que l'utilisateur est bien propriétaire
        if ($ad->user_id !== Auth::id()) {
            return redirect()->route('annonces.index')
                             ->with('error', 'Vous ne pouvez pas modifier cette annonce.');
        }

        // 🔹 Stocke la photo de profil si présente
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $validated['profile_photo'] = $path;
        }

        // 🔹 Met à jour les informations
        $ad->update($validated);

        // 🔹 Supprime les images décochées
        $existingImageIds = $request->input('existing_images', []);
        $imagesToDelete = $ad->images()->whereNotIn('id', $existingImageIds)->get();

        foreach ($imagesToDelete as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        // 🔹 Ajoute les nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('images', 'public');
                $ad->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('annonces.index')
                         ->with('success', 'Annonce modifiée avec succès.');
    }

    // ======================================
    // 🔹 Supprime une annonce
    // ======================================
    public function destroy($id)
    {
        $ad = Ad::findOrFail($id);

        // 🔹 Supprime toutes les images liées
        foreach ($ad->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        // 🔹 Supprime l'annonce
        $ad->delete();

        return redirect()->route('annonces.index')
                         ->with('success', 'Annonce supprimée avec succès.');
    }

    // ======================================
    // 🔹 Page d'accueil avec recherche et filtrage
    // ======================================
    public function acceuil(Request $request)
    {
        $query = $request->input('q');
        $category = $request->input('category');

        // 📌 Synonymes pour améliorer la recherche
        $synonyms = [
            'voiture' => ['voiture', 'car', 'auto', 'véhicule', 'cars'],
            'moto' => ['moto', 'motorcycle', 'bike', 'motobike'],
            'immobilier' => ['maison', 'villa', 'appartement', 'logement', 'immeuble'],
            'cosmétique' => ['cosmétique', 'maquillage', 'beauty', 'crème', 'parfum'],
            'électronique' => ['électronique', 'telephone', 'téléphone', 'smartphone', 'pc', 'ordinateur', 'laptop', 'tv', 'écran', 'console'],
            'vélo' => ['vélo', 'bicyclette'],
            'vêtement' => ['vêtement', 'vetement', 'habit', 'clothes', 'tenue'],
            'meuble' => ['meuble', 'furniture', 'table', 'chaise', 'armoire', 'canapé', 'lit'],
            'divers' => ['divers', 'autre', 'other', 'miscellaneous'],
        ];

        // Détecte la fonction date pour la DB (SQLite ou MySQL)
        $nowFunction = \DB::getDriverName() === 'sqlite' ? 'CURRENT_TIMESTAMP' : 'NOW()';

        $ads = Ad::query();

        // 🔎 Filtre par texte et synonymes
        if ($query) {
            $ads->where(function($q) use ($query, $synonyms) {
                $q->where('title', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%");
                foreach ($synonyms as $group) {
                    if (in_array(strtolower($query), $group)) {
                        $q->orWhere(function($sub) use ($group) {
                            foreach ($group as $word) {
                                $sub->orWhere('title', 'like', "%$word%")
                                    ->orWhere('description', 'like', "%$word%");
                            }
                        });
                    }
                }
            });
        }

        // 🔎 Filtre par catégorie
        if ($category) {
            $ads->where(function($q) use ($category, $synonyms) {
                $q->where('category', $category);
                if (isset($synonyms[$category])) {
                    foreach ($synonyms[$category] as $word) {
                        $q->orWhere('title', 'like', "%$word%")
                          ->orWhere('description', 'like', "%$word%");
                    }
                }
            });
        }

        // 🔹 Tri : boostées d'abord, puis date de création
        $ads = $ads->with('images')
            ->orderByRaw("CASE WHEN boosted_until >= $nowFunction THEN 1 ELSE 0 END DESC")
            ->orderByDesc('boosted_until')
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('welcome', compact('ads', 'query', 'category'));
    }

    // ======================================
    // 🔹 Détails d'une annonce
    // ======================================
    public function show(Ad $ad)
    {
        $ad->load('images'); // Charge les images associées
        return view('ads.show', compact('ad'));
    }
}
