@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">📋 Mes annonces</h2>
        <a href="{{ route('annonces.create') }}" class="btn btn-success">➕ Nouvelle annonce</a>
    </div>
<!--
    {{-- Boutons Booster / Chat --}}
    <div class="d-flex justify-content-center my-4 gap-2">
        <a href="{{ route('boost') }}" class="btn btn-info btn-lg btn-responsive">
            🚀 Booster mes annonces
        </a>

        @if($lastPayment ?? false)
            <a href="{{ route('user.chat', $lastPayment->id) }}" class="btn btn-info btn-lg btn-responsive">
                💬 Chat
            </a>
        @endif
    </div>
!-->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        @forelse($ads as $ad)
            <div class="col-sm-6 col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    {{-- Images --}}
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

                    {{-- Contenu --}}
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $ad->title }}</h5>
                        <p class="card-text text-muted" style="min-height: 80px;">
                            {{ Str::limit($ad->description, 100) }}
                        </p>
                        <p class="fw-bold text-primary mt-auto">
                            {{ number_format($ad->price, 0, ',', ' ') }} GNF
                        </p>

                        <p class="text-muted">📞 {{ $ad->phone ?? 'Numéro non disponible' }}</p>

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

                        <p class="text-muted mt-2">📍 {{ $ad->location ?? 'Lieu non précisé' }}</p>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-between mt-3">
                            <a href="{{ route('annonces.edit', $ad->id) }}" class="btn btn-sm btn-outline-primary">
                                ✏️ Modifier
                            </a>
                            <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-sm btn-outline-info">
                                👁️ Voir
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
            <div class="col-12">
                <div class="alert alert-info text-center">
                    Vous n'avez pas encore publié d'annonces.
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $ads->links() }}
    </div>
</div>

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
            background-color: rgba(0, 0, 0, 0.6);
            border-radius: 50%;
            padding: 15px;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 8%;
        }
    }

    .btn-responsive {
        min-width: 200px;
    }
    @media (max-width: 576px) {
        .btn-responsive {
            width: 90%;
            font-size: 1rem;
            padding: 0.75rem 1rem;
        }
    }
</style>
@endpush
@endsection
