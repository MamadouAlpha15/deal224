@extends('layouts.app')

@section('content')
<div class="container py-5">

    {{-- ===================== SECTION HERO ===================== --}}
    <div class="row align-items-center mb-5">
        <div class="col-lg-6 text-center text-lg-start mb-4 mb-lg-0">
            <h1 class="fw-bold display-5 text-primary">
                Bienvenue sur <span class="text-warning">{{ config('app.name', 'Deal224') }}</span>
            </h1>
            <p class="lead text-muted">
                Achetez et vendez facilement vos produits en Guinée 🚀.<br>
                Publiez vos annonces gratuitement et trouvez des clients rapidement !
            </p>
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-lg-start mt-3">
                <a href="{{ route('annonces.create') }}" class="btn btn-warning btn-lg fw-bold">
                    ➕ Publier une annonce
                </a>

                @auth
                    <a href="{{ auth()->user()->role === 'superadmin' ? route('superadmin.boost') : route('dashboard') }}"
                       class="btn btn-primary">
                        Mon interface
                    </a>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Se connecter</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a>
                @endguest
            </div>
        </div>

        {{-- ✅ IMAGE MARKETPLACE --}}
        <div class="col-lg-6 d-flex justify-content-center">
            <img src="{{ asset('storage/images/deal.png') }}" alt="Marketplace"
                 class="img-fluid rounded shadow-sm" style="max-height: 350px;">
        </div>
    </div>

    {{-- ===================== FORMULAIRE DE RECHERCHE ===================== --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('home') }}" class="row g-2">
                <div class="col-12 col-md-6">
                    <input type="text" name="q" class="form-control form-control-lg"
                           placeholder="🔎 Rechercher une annonce..."
                           value="{{ $query ?? '' }}">
                </div>
                <div class="col-12 col-md-4">
                    <select name="category" class="form-select form-select-lg">
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
                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">Rechercher</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== RAPPEL RAPIDE POUR LES VENDEURS ===================== --}}
    <div class="alert alert-info mb-5">
        ⚡ <strong>Règles rapides pour publier :</strong>
        <ul class="mb-0 ps-3">
            <li>🛍️ <strong>1 produit = 1 annonce</strong></li>
            <li>📸 <strong>Ajoutez plusieurs photos</strong> du même produit</li>
            <li>❌ <strong>Ne mélangez pas plusieurs produits</strong> dans une seule annonce</li>
        </ul>
    </div>

    {{-- ===================== DERNIÈRES ANNONCES ===================== --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">🛒 Dernières annonces</h2>
    </div>

    @if($ads->count())
        <div class="row g-4">
            @foreach ($ads as $ad)
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 position-relative">

                        {{-- Badge Boosté --}}
                        @if($ad->boosted_until && $ad->boosted_until->isFuture())
                            <span class="badge bg-warning text-dark position-absolute"
                                  style="top: 10px; left: 10px; z-index: 10; font-weight: bold;">
                                🔥 Boosté
                            </span>
                        @endif

                        {{-- Carrousel d'images --}}
                        @if($ad->images->count())
                            <div id="carousel-{{ $ad->id }}" class="carousel slide" data-bs-ride="false">

                                {{-- ✅ Indicateurs --}}
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

                                <div class="carousel-inner">
                                    @foreach($ad->images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $image->path) }}"
                                                 class="d-block w-100"
                                                 style="height: auto; max-height: 280px; object-fit: cover;"
                                                 alt="Image {{ $index + 1 }}">
                                        </div>
                                    @endforeach
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-{{ $ad->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-{{ $ad->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        @else
                            <img src="{{ asset('storage/placeholder.jpg') }}" class="card-img-top"
                                 style="height:220px; object-fit:cover;" alt="Aucune image">
                        @endif

                        {{-- Contenu de la card --}}
                        <div class="card-body d-flex flex-column">
                            <a href="{{ route('user.show', $ad->user->id) }}">
                                <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                                    @if($ad->user->profile_photo)
                                        <img src="{{ asset('storage/' . $ad->user->profile_photo) }}"
                                             class="rounded-circle border border-white shadow"
                                             style="width:55px; height:55px; object-fit:cover;">
                                    @else
                                        <img src="{{ asset('storage/profile_placeholder.png') }}"
                                             class="rounded-circle border border-white shadow"
                                             style="width:55px; height:55px; object-fit:cover;">
                                    @endif
                                </div>
                            </a>

                            <h5 class="fw-bold text-truncate">{{ $ad->title }}</h5>
                            <p class="text-muted small">{{ Str::limit($ad->description, 100) }}</p>
                            <p class="fw-bold text-success">💰 {{ number_format($ad->price, 0, ',', ' ') }} {{ $ad->currency }}</p>
                            <p class="text-primary">📞 {{ $ad->phone ?? 'Numéro non disponible' }}</p>
                            <p class="text-muted">📍 {{ $ad->location ?? 'Lieu non précisé' }}</p>
                            {{-- 📲 Bouton WhatsApp qui ouvre la discussion --}}
                            @if(!empty($ad->whatsapp))
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $ad->whatsapp) }}" 
                                   target="_blank"
                                   class="btn btn-success btn-sm d-flex align-items-center gap-2 mt-2">
                                    <i class="bi bi-whatsapp fs-5"></i>
                                    Contactez-moi sur WhatsApp
                                </a>
                            @endif

                            <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-sm btn-outline-primary mt-2">Voir plus</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-4 d-flex justify-content-center">
            {{ $ads->withQueryString()->links() }}
        </div>
    @else
        <div class="alert alert-warning text-center">
            😕 Aucune annonce disponible.
        </div>
    @endif
</div>

@push('styles')
<style>
    .carousel-item img { width: 100%; object-fit: cover; }
    @media (min-width: 768px) { .carousel-item img { max-height: 300px; } }
    @media (max-width: 767.98px) { .carousel-item img { max-height: 200px; } }
</style>
@endpush

{{-- ✅ Script pour fermer le menu après clic sur lien --}}
@push('scripts')
<script>
document.querySelectorAll('.navbar-nav a').forEach(function(link) {
    link.addEventListener('click', function () {
        let navbar = document.getElementById('navbarContent');
        let bsCollapse = bootstrap.Collapse.getInstance(navbar);
        if (bsCollapse) { bsCollapse.hide(); }
    });
});
</script>
@endpush

@endsection
