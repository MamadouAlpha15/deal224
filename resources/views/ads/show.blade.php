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

            <p><strong>Prix :</strong> 
                <span class="text-primary fw-bold">{{ number_format($ad->price, 0, ',', ' ') }} GNF</span>
            </p>

            <hr>

            <h5>Description</h5>
            <div style="white-space: pre-wrap;">{{ $ad->description }}</div>

            <hr>

            <p><strong>Publié le :</strong> {{ $ad->created_at->format('d/m/Y à H:i') }}</p>
            <p><strong>Par :</strong> {{ $ad->user->name }}</p>

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
