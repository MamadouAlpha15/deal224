@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- Profil utilisateur --}}
    <div class="text-center mb-4">
        {{-- Photo de profil --}}
        @if($user->profile_photo)
            <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                 alt="Profil" 
                 class="rounded-circle border border-white mb-3 shadow-sm" 
                 style="width:120px; height:120px; object-fit:cover;">
        @else
            <img src="{{ asset('storage/profile_placeholder.png') }}" 
                 alt="Profil" 
                 class="rounded-circle border border-white mb-3 shadow-sm" 
                 style="width:120px; height:120px; object-fit:cover;">
        @endif

        {{-- Nom et info --}}
        <h2 class="mt-2">{{ $user->name }}</h2>
        <p class="text-muted">Email : {{ $user->email }}</p>
        <p><strong>{{ $user->ads->count() }}</strong> annonces publiées</p>
    </div>

    {{-- Liste des annonces --}}
    <div class="row g-4">
        @foreach($user->ads as $ad)
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
                                             style="max-height: 250px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <img src="{{ asset('storage/placeholder.jpg') }}"
                             class="card-img-top rounded-top"
                             alt="Aucune image"
                             style="height: 250px; object-fit: cover;">
                    @endif

                    <div class="card-body">
                        <h5 class="card-title">{{ $ad->title }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($ad->description, 80) }}</p>
                          {{-- PRIX --}}
                           <p class="fw-bold text-success mb-1" style="font-size: 1.1rem;">
                              💰 {{ number_format($ad->price, 0, ',', ' ') }} {{ $ad->currency }}
                           </p>
                        <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-outline-info btn-sm">Voir</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
