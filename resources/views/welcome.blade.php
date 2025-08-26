@extends('layouts.app')

@section('content')
<div class="container">
    <form method="GET" action="{{ route('home') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Rechercher une annonce..." value="{{ $query ?? '' }}">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center py-3">
        <h2>🛒 Dernières annonces</h2>
        <div>
            @auth
                <a href="{{ auth()->user()->role === 'superadmin' ? route('superadmin.boost') : route('dashboard') }}"
   class="btn btn-primary">
   Dashboard
</a>

            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Se connecter</a>
                <a href="{{ route('register') }}" class="btn btn-primary">S'inscrire</a>
            @endguest
        </div>
    </div>

    <hr>

    @if(isset($query))
        <h5 class="mb-4">Résultats pour : <strong>{{ $query }}</strong></h5>
    @endif

    @if($ads->count())
        <div class="row">
            @foreach ($ads as $ad)
                <div class="col-md-4">
                    <div class="card mb-4 border">
                        <!-- Carrousel pour les images -->
                        @if($ad->images->count())
                            <div id="carousel-welcome-{{ $ad->id }}" class="carousel slide">
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
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-welcome-{{ $ad->id }}" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Précédent</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-welcome-{{ $ad->id }}" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class KB="Suivant"></span>
                                </button>
                            </div>
                        @else
                            <img src="{{ asset('storage/placeholder.jpg') }}" 
                                 class="card-img-top" 
                                 style="height: 200px; object-fit: cover;">
                        @endif

                        <div class="card-body">
                            <h5 class="card-title">{{ $ad->title }}</h5>
                            <p class="card-text" style="min-height: 80px;">{{ Str::limit($ad->description, 100) }}</p>
                            <p class="fw-bold text-primary">{{ number_format($ad->price, 0, ',', ' ') }} GNF</p>
                            <p class="text-muted">📞 {{ $ad->phone ?? 'Numéro non disponible' }}</p>

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
                  <br>

                      <p class="text-muted">📍 {{ $ad->location ?? 'Lieu non précisé' }}</p>
                            <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-sm btn-outline-primary">Voir plus</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-warning text-center">
            @if($query)
                😕 Aucune annonce ne correspond à votre recherche : <strong>{{ $query }}</strong>
            @else
                😕 Aucune annonce disponible pour le moment.
            @endif
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@push('styles')
<style>
    .carousel-item img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
    }

    @media (min-width: 768px) {
        .carousel-item img {
            max-height: 300px;
        }
    }

    @media (max-width: 767.98px) {
        .carousel-item img {
            max-height: 200px;
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