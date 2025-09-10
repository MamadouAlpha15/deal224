@extends('layouts.app')

@section('content')
<div class="container">

    {{-- ===================== RECHERCHE ===================== --}}
    {{-- ===================== RECHERCHE ===================== --}}
<form method="GET" action="{{ route('home') }}" class="mb-4">
    <div class="row g-2">
        {{-- Champ de recherche --}}
        <div class="col-md-4">
            <input type="text" name="q" class="form-control" 
                   placeholder="Rechercher une annonce..." 
                   value="{{ $query ?? '' }}">
        </div>
        

        {{-- Filtre par catégorie --}}
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">-- Toutes les catégories --</option>
                <option value="voiture" {{ ($category ?? '') === 'voiture' ? 'selected' : '' }}>🚗 Voitures</option>
                <option value="moto" {{ ($category ?? '') === 'moto' ? 'selected' : '' }}>🏍️ Motos</option>
                <option value="immobilier" {{ ($category ?? '') === 'immobilier' ? 'selected' : '' }}>🏠 Immobilier</option>
                <option value="cosmétique" {{ ($category ?? '') === 'cosmétique' ? 'selected' : '' }}>💄 Cosmétiques</option>
                <option value="électronique" {{ ($category ?? '') === 'électronique' ? 'selected' : '' }}>📱 Électronique</option>
                <option value="vélo" {{ ($category ?? '') === 'vélo' ? 'selected' : '' }}>🚲 Vélos</option>
                <option value="vêtement" {{ ($category ?? '') === 'vêtement' ? 'selected' : '' }}>👗 Vêtements</option>
                <option value="meuble" {{ ($category ?? '') === 'meuble' ? 'selected' : '' }}>🛋️ Meubles</option>
                <option value="divers" {{ ($category ?? '') === 'divers' ? 'selected' : '' }}>📦 Divers</option>
            </select>
        </div>

        {{-- Bouton recherche --}}
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">
                🔎 Rechercher
            </button>
        </div>
    </div>
</form>
{{-- ===================== RAPPEL RAPIDE POUR LES VENDEURS ===================== --}}
<div class="alert alert-info mb-4">
    ⚡ <strong>Règles rapides pour publier :</strong>
    <ul class="mb-0">
        <li>🛍️ <strong>1 produit = 1 annonce</strong> (exemple : un parfum Dior = 1 annonce, un parfum Chanel = une autre).</li>
        <li>📸 <strong>Ajoutez plusieurs photos du même produit</strong> pour montrer tous ses détails (voiture, maison, téléphone…).</li>
        <li>❌ <strong>Ne mélangez pas plusieurs produits</strong> dans une seule annonce (exemple : vêtements + chaussures ensemble).</li>
    </ul>
</div>



    {{-- ===================== HEADER ===================== --}}
    <div class="d-flex justify-content-between align-items-center py-3">
        <h2>🛒 Dernières annonces</h2>
        <div>
            {{-- Si connecté --}}
            @auth
                <a href="{{ auth()->user()->role === 'superadmin' ? route('superadmin.boost') : route('dashboard') }}"
                   class="btn btn-primary">
                    Dashboard
                </a>
            @endauth

            {{-- Si invité --}}
            @guest
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Se connecter</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a>
                </div>
            @endguest
        </div>
    </div>

    <hr>

    {{-- Affiche le texte "Résultats pour" si recherche --}}
    @if(isset($query))
        <h5 class="mb-4">Résultats pour : <strong>{{ $query }}</strong></h5>
    @endif

    {{-- ===================== LISTE DES ANNONCES ===================== --}}
    @if($ads->count())
        <div class="row">
            @foreach ($ads as $ad)
                <div class="col-md-4">
                    {{-- Card annonce --}}
                    <div class="card mb-4 border position-relative">

                        {{-- ===================== BADGE BOOSTÉ ===================== --}}
                        @if($ad->boosted_until && $ad->boosted_until->isFuture())
                            <span class="badge bg-warning text-dark position-absolute" 
                                  style="top: 10px; left: 10px; z-index: 10; font-weight: bold;">
                                🔥 Boosté
                            </span>
                        @endif

                       {{-- ===================== CARROUSEL D'IMAGES ===================== --}}
@if($ad->images->count())
    <div id="carousel-{{ $ad->id }}" class="carousel slide" data-bs-ride="false">   <!--ici false pour ne pas démarrer automatiquement et carousol pour automatique --> 

        {{-- Indicateurs (les petits points en bas) --}}
        <div class="carousel-indicators">
            @foreach($ad->images as $index => $image)
                <button type="button" 
                        data-bs-target="#carousel-{{ $ad->id }}" 
                        data-bs-slide-to="{{ $index }}" 
                        class="{{ $index === 0 ? 'active' : '' }}" 
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                        aria-label="Slide {{ $index + 1 }}">
                </button>
            @endforeach
        </div>

        {{-- Images --}}
        <div class="carousel-inner">
            @foreach($ad->images as $index => $image)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset('storage/' . $image->path) }}" 
                         class="d-block w-100 img-fluid" 
                         alt="Image {{ $index + 1 }}" 
                         style="max-height: 300px; object-fit: cover;">
                </div>
            @endforeach
        </div>

        {{-- Contrôles du carrousel --}}
        <button class="carousel-control-prev" type="button" 
                data-bs-target="#carousel-{{ $ad->id }}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" 
                data-bs-target="#carousel-{{ $ad->id }}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
@else
    {{-- Image par défaut si aucune image --}}
    <img src="{{ asset('storage/placeholder.jpg') }}" 
         class="card-img-top" 
         style="height: 200px; object-fit: cover;">
@endif

                        {{-- ===================== CONTENU DE LA CARD ===================== --}}
                        <div class="card-body d-flex flex-column">

                            {{-- PHOTO DE PROFIL --}}
                            <a href="{{ route('user.show', $ad->user->id) }}">
    <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
        @if($ad->user->profile_photo)
            <img src="{{ asset('storage/' . $ad->user->profile_photo) }}" 
                 alt="Profil" 
                 class="profile-clickable rounded-circle border border-white" 
                 style="width:60px; height:60px; object-fit:cover; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
        @else
            <img src="{{ asset('storage/profile_placeholder.png') }}" 
                 alt="Profil" 
                 class="profile-clickable rounded-circle border border-white" 
                 style="width:60px; height:60px; object-fit:cover; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
        @endif
    </div>
</a>


                            {{-- TITRE DE L'ANNONCE --}}
                            <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 1.25rem;">
                                {{ $ad->title }}
                            </h5>

                            {{-- DESCRIPTION --}}
                            <p class="card-text text-secondary mb-2" style="min-height: 80px; line-height: 1.4;">
                                {{ Str::limit($ad->description, 100) }}
                            </p>
                            {{-- PRIX --}}
                           <p class="fw-bold text-success mb-1" style="font-size: 1.1rem;">
                              💰 {{ number_format($ad->price, 0, ',', ' ') }} {{ $ad->currency }}
                           </p>

                            {{-- TÉLÉPHONE --}}
                            <p class="text-primary mb-1">
                                📞 {{ $ad->phone ?? 'Numéro non disponible' }}
                            </p>

                            {{-- LOCALISATION --}}
                            <p class="text-muted mb-0">
                                📍 {{ $ad->location ?? 'Lieu non précisé' }}
                            </p>

                            {{-- LIEN WHATSAPP --}}
                            @if(!empty($ad->whatsapp))
                                @php
                                    $message = urlencode("Bonjour, je suis intéressé par votre annonce : {$ad->title}. Voici le lien : " . route('annonces.show', $ad->id));
                                    $whatsappUrl = "https://wa.me/{$ad->whatsapp}?text={$message}";
                                @endphp
                                <a href="{{ $whatsappUrl }}" target="_blank" 
                                   class="btn btn-sm btn-success d-flex align-items-center gap-2 mt-1">
                                    <i class="bi bi-whatsapp"></i> Contacter via WhatsApp
                                </a>
                            @endif

                            {{-- BOUTON VOIR PLUS --}}
                            <a href="{{ route('annonces.show', $ad->id) }}" 
                               class="btn btn-sm btn-outline-primary mt-2">
                                Voir plus
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="d-flex justify-content-center">
            {{ $ads->withQueryString()->links() }}
        </div>

    @else
        {{-- MESSAGE SI AUCUNE ANNONCE --}}
        <div class="alert alert-warning text-center">
            @if($query)
                😕 Aucune annonce ne correspond à votre recherche : <strong>{{ $query }}</strong>
            @else
                😕 Aucune annonce disponible pour le moment.
            @endif
        </div>
    @endif
</div>

{{-- ===================== SCRIPTS BOOTSTRAP ===================== --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@push('styles')
<style>
    /* Images du carrousel */
    .carousel-item img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    /* Max-height pour grands écrans */
    @media (min-width: 768px) {
        .carousel-item img { max-height: 300px; }
    }

    /* Max-height pour petits écrans et style des flèches */
    @media (max-width: 767.98px) {
        .carousel-item img { max-height: 200px; }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.6); 
            border-radius: 50%; 
            padding: 15px; 
        }

        .carousel-control-prev,
        .carousel-control-next { width: 8%; }
    }

    /* Style pour photo de profil cliquable */
.profile-clickable {
    cursor: pointer; /* Changement du curseur en main */
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.profile-clickable:hover {
    transform: scale(1.1); /* Zoom léger au survol */
    box-shadow: 0 0 12px rgba(0,0,0,0.4); /* Ombre plus forte */
}

</style>
@endpush

@endsection
