@extends('layouts.app') 
{{-- 🧱 Hérite du layout principal (header, footer, etc.) --}}

@section('content')
<div class="container py-4">
    {{-- 🧭 Titre + bouton d’ajout d’annonce --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📋 Mes annonces</h2>
        <a href="{{ route('annonces.create') }}" class="btn btn-success">➕ Nouvelle annonce</a>
    </div>

    {{-- ✅ Message de succès après modification ou suppression --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- 🎯 Grille des annonces --}}
    <div class="row g-4">
        @forelse ($ads as $ad)
            <div class="col-sm-6 col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    
                    {{-- 🖼️ Carrousel des images si l’annonce a des images --}}
                    @if($ad->images->count())
                        <div id="carousel-{{ $ad->id }}" class="carousel slide" data-bs-ride="carousel">
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

                            {{-- ⬅️➡️ Boutons précédent / suivant si plusieurs images --}}
                            @if($ad->images->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-{{ $ad->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Précédent</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-{{ $ad->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Suivant</span>
                                </button>
                            @endif
                        </div>
                    @else
                        {{-- 🖼️ Image par défaut si aucune image --}}
                        <img src="{{ asset('storage/placeholder.jpg') }}" 
                             class="card-img-top rounded-top" 
                             alt="Aucune image" 
                             style="height: 200px; object-fit: cover;">
                    @endif

                    {{-- 📦 Contenu de la carte : titre, description, prix, actions --}}
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $ad->title }}</h5>

                        {{-- ✏️ Aperçu de la description --}}
                        <p class="card-text text-muted" style="min-height: 80px;">
                            {{ Str::limit($ad->description, 100) }}
                        </p>

                        {{-- 💰 Prix de l’annonce --}}
                        <p class="fw-bold text-primary mt-auto">
                            {{ number_format($ad->price, 0, ',', ' ') }} GNF
                        </p>
                        
                      <p class="text-muted">📞 {{ $ad->phone ?? 'Numéro non disponible' }}</p>
                      <p class="text-muted">📍 {{ $ad->location ?? 'Lieu non précisé' }}</p>

                                      {{-- 📲 WhatsApp (si dispo) --}}
                       @php
                             if (!empty($ad->whatsapp)) {
                                 $message = urlencode("Bonjour, je suis intéressé par votre annonce : {$ad->title}. Voici le lien : " . route('annonces.show', $ad->id));
                                 $whatsappUrl = "https://wa.me/{$ad->whatsapp}?text={$message}";
                                 }
                         @endphp

                   @if(!empty($ad->whatsapp))
                     <a href="{{ $whatsappUrl }}" target="_blank" 
                         class="btn btn-sm btn-success d-flex align-items-center gap-2 mt-1">
                       <i class="bi bi-whatsapp"></i> Contacter via WhatsApp
                     </a>
                 @endif



                        {{-- 🧭 Boutons modifier / voir / supprimer --}}
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('annonces.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary">
                                ✏️ Modifier
                            </a>
                            <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-sm btn-outline-info">
                                Voir
                            </a>
                            <form action="{{ route('annonces.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            {{-- 💤 Message si aucune annonce --}}
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Vous n'avez pas encore publié d'annonces.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
<div class="mt-4 d-flex justify-content-center">
    {{ $ads->links() }}
</div>
</div>
@push('styles')
<style>
    /* 🎯 Règles générales pour toutes les images du carrousel */
    .carousel-item img {
        width: 100%;                /* 🧱 L'image prend toute la largeur disponible du conteneur */
        height: auto;               /* 🧩 Hauteur auto pour garder les proportions naturelles */
        object-fit: cover;          /* 📸 Couvre l’espace sans déformer (comme un zoom recadré) */
        border-top-left-radius: 0.25rem;  /* 🎨 Coins arrondis en haut à gauche */
        border-top-right-radius: 0.25rem; /* 🎨 Coins arrondis en haut à droite */
    }

    /* 💻 Règles pour les écrans de taille moyenne et plus (≥ 768px) */
    @media (min-width: 768px) {
        .carousel-item img {
            max-height: 300px;      /* 📏 Limite la hauteur max à 300px sur desktop/tablette */
        }
    }

    /* 📱 Règles pour les petits écrans (≤ 767.98px) */
    @media (max-width: 767.98px) {
        .carousel-item img {
            max-height: 200px;      /* 📏 Hauteur plus petite sur mobile pour gain de place */
        }

        .carousel-control-prev-icon,
.carousel-control-next-icon {
    background-color: rgba(0, 0, 0, 0.6); /* fond noir semi-transparent */
    border-radius: 50%; /* rond autour des flèches */
    padding: 15px; /* agrandit le cercle */
}

.carousel-control-prev,
.carousel-control-next {
    width: 8%; /* augmente la zone cliquable */
}
    }
</style>
@endpush
@endsection