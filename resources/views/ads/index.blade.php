@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Titre + bouton Nouvelle annonce --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📋 Mes annonces</h2>
        <a href="{{ route('annonces.create') }}" class="btn btn-success">➕ Nouvelle annonce</a>
    </div>
 {{-- Boutons Booster / Chat --}}
    <div class="d-flex justify-content-center my-4 gap-2">
        <a href="{{ route('boost') }}" class="btn btn-info btn-lg btn-responsive">
            🚀 Booster mes annonces
        </a>
@if($lastPayment ?? false)
    <a href="{{ route('user.chat', $lastPayment->id) }}" class="btn btn-info btn-lg btn-responsive">
        💬 Chat
        <span id="message-badge" class="badge bg-danger" style="display:none;"></span>
    </a>
@endif

    </div>

    {{-- Message de succès --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Grille des annonces --}}
    <div class="row g-4">
        @forelse ($ads as $ad)
        
        
            <div class="col-sm-6 col-md-4">
                <div class="card h-100 shadow-sm border-0">


                    {{-- Carrousel images --}}
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
                        <img src="{{ asset('storage/placeholder.jpg') }}" 
                             class="card-img-top rounded-top" 
                             alt="Aucune image" 
                             style="height: 200px; object-fit: cover;">
                    @endif

                   

                  {{-- Contenu de la carte --}}
<div class="card-body d-flex flex-column ">

    {{-- PHOTO DE PROFIL en rond --}}
    <a href="{{route('user.show',$ad->user->id)}}">
        <div style="position: absolute; top: 10px; right: 10px; z-index: 10;">
            @if($ad->user->profile_photo)
                <img src="{{ asset('storage/' . $ad->user->profile_photo) }}" 
                     alt="Profil" 
                     class="rounded-circle border border-white" 
                     style="width:60px; height:60px; object-fit:cover; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
            @else
                <img src="{{ asset('storage/profile_placeholder.png') }}" 
                     alt="Profil" 
                     class="rounded-circle border border-white" 
                     style="width:60px; height:60px; object-fit:cover; box-shadow: 0 0 8px rgba(0,0,0,0.2);">
            @endif
        </div>
    </a>

    {{-- Titre --}}
    <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 1.25rem;">
        {{ $ad->title }}
    </h5>

    {{-- Description --}}
    <p class="card-text text-secondary mb-2" style="min-height: 80px; line-height: 1.4;">
        {{ Str::limit($ad->description, 100) }}
    </p>

    <p class="fw-bold text-success mb-1" style="font-size: 1.1rem;">
    💰 {{ number_format($ad->price, 0, ',', ' ') }} {{ $ad->currency }}
</p>

    {{-- Téléphone --}}
    <p class="text-primary mb-1">
        📞 {{ $ad->phone ?? 'Numéro non disponible' }}
    </p>

    {{-- Localisation --}}
    <p class="text-muted mb-0">
        📍 {{ $ad->location ?? 'Lieu non précisé' }}
    </p>

    {{-- 📲 WhatsApp (si dispo) --}}
                         {{-- WhatsApp --}}
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


                        {{-- Boutons modifier / voir / supprimer --}}
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('annonces.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary">✏️ Modifier</a>
                            <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-sm btn-outline-info">Voir</a>
                            <form action="{{ route('annonces.destroy', $ad->id) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑️ Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
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
    .carousel-item img { width: 100%; height: auto; object-fit: cover; border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; }
    @media (min-width: 768px) { .carousel-item img { max-height: 300px; } }
    @media (max-width: 767.98px) { .carousel-item img { max-height: 200px; }
        .carousel-control-prev-icon,.carousel-control-next-icon { background-color: rgba(0,0,0,0.6); border-radius:50%; padding:15px; }
        .carousel-control-prev,.carousel-control-next { width:8%; }
    }
    .btn-responsive { min-width: 200px; }
    @media (max-width: 576px) { .btn-responsive { width: 90%; font-size:1rem; padding:0.75rem 1rem; } }
</style>
@endpush

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function checkMessages() {
        axios.get("{{ route('user.unread-messages') }}")
             .then(response => {
                 const count = response.data.count;
                 const badge = document.getElementById('message-badge');

                 if(count > 0) {
                     badge.innerText = count;
                     badge.style.display = 'inline';
                 } else {
                     badge.style.display = 'none';
                 }
             });
    }

    // Vérifie toutes les 15 secondes
    setInterval(checkMessages, 15000);
    // Vérifie immédiatement au chargement
    checkMessages();
</script>

@endsection
