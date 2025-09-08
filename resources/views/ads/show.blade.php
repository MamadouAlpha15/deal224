@extends('layouts.app')

@section('content')
<div class="container py-4">

    <a href="{{ route('home') }}" class="btn btn-outline-secondary mb-3">← Retour à l'accueil</a>

    <div class="row">
        <div class="col-md-7">

            {{-- Carrousel d'images --}}
            @if($ad->images->count())
               <div id="carouselAd" class="carousel slide mb-3" data-bs-ride="false">

                    <div class="carousel-inner">
                        @foreach($ad->images as $index => $image)
                            <div class="carousel-item @if($index == 0) active @endif">
                                <img src="{{ asset('storage/' . $image->path) }}" 
     class="d-block w-100 img-fluid" 
     alt="Image {{ $index + 1 }}" 
     style="height: auto; max-height: 600px; object-fit: cover;">

                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselAd" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselAd" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            @else
                <img src="{{ asset('images/no-image.png') }}" class="img-fluid mb-3" alt="Aucune image disponible">
            @endif

        </div>

        <div class="col-md-5">
            <h2 class="mb-3">{{ $ad->title }}</h2>

             {{-- PRIX --}}
                           <p class="fw-bold text-success mb-1" style="font-size: 1.1rem;">
                              💰 {{ number_format($ad->price, 0, ',', ' ') }} {{ $ad->currency }}
                           </p>
            <hr>
            <p class="text-muted mb-0">
                📞 Contact : 
                @if($ad->phone)
                    <a href="tel:{{ $ad->phone }}">{{ $ad->phone }}</a>
                @else
                    Non spécifié
                @endif
            <hr>
            </p>
            <p class="text-muted mb-0 text-secondary">
                📍Lieu: <strong class="text-primary">{{ $ad->location ?? 'Lieu non précisé' }}</strong> </p>
            <hr>
            {{-- WhatsApp --}}
                        @if(!empty($ad->whatsapp))
                            @php
                                $message = urlencode("Bonjour, je suis intéressé par votre annonce : {$ad->title}. Voici le lien : " . route('annonces.show', $ad->id));
                                $whatsappUrl = "https://wa.me/{$ad->whatsapp}?text={$message}";
                            @endphp
                            <a href="{{ $whatsappUrl }}" target="_blank" class="btn btn-sm btn-success d-flex align-items-center gap-2 mt-1">
                                <i class="bi bi-whatsapp"></i> Contacter via WhatsApp
                            </a>
                        @endif
            <hr>

            <h5>Description</h5>
            <div style="white-space: pre-wrap;">{{ $ad->description }}</div>

            <hr>

            <p><strong>Publié le :</strong> {{ $ad->created_at->format('d/m/Y à H:i') }}</p>
           <p class="d-flex align-items-center gap-2">
    @if($ad->user->profile_photo)
        <img src="{{ asset('storage/' . $ad->user->profile_photo) }}" 
             alt="Profil" 
             class="rounded-circle border border-white"
             style="width:50px; height:50px; object-fit:cover;">
    @else
        <img src="{{ asset('storage/profile_placeholder.png') }}" 
             alt="Profil" 
             class="rounded-circle border border-white"
             style="width:50px; height:50px; object-fit:cover;">
    @endif
    <strong>Par :</strong> {{ $ad->user->name }}
</p>

            @auth
                @if(auth()->id() === $ad->user_id)
                    <div class="mt-3 d-flex gap-2">
                        <a href="{{ route('annonces.edit', $ad) }}" class="btn btn-outline-primary">✏️ Modifier</a>

                        <form action="{{ route('annonces.destroy', $ad) }}" method="POST" onsubmit="return confirm('Confirmer la suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">🗑️ Supprimer</button>
                        </form>
                    </div>
                @endif
            @endauth
        </div>
    </div>
</div>
@push('styles')
<style>
    .carousel-item img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 0.25rem;
    }

    @media (min-width: 992px) {
        .carousel-item img {
            max-height: 600px;
        }
    }

    @media (max-width: 991.98px) {
        .carousel-item img {
            max-height: 400px;
        }
    }

    @media (max-width: 576px) {
        .carousel-item img {
            max-height: 250px;
        }
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

</style>
@endpush


@endsection
