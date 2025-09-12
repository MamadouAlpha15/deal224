@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- ⚠️ Message important --}}
    <div class="alert alert-warning text-center fw-bold mb-4">
        ⚠️ Une fois que vous avez <span class="text-success">vendu un produit</span>, 
        <span class="text-danger">supprimez-le</span> pour qu’il disparait de l’accueil.
    </div>

    {{-- Titre + bouton Nouvelle annonce --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
        <h2 class="mb-0 text-center text-md-start">📋 Mes annonces</h2>
        <a href="{{ route('annonces.create') }}" class="btn btn-success btn-lg w-100 w-md-auto">
            ➕ Nouvelle annonce
        </a>
    </div>
</div>

   <!-- {{-- Boutons Booster / Chat --}}
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

!-->


    @if(session('success'))
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Succès 🎉',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            });
        </script>
    @endpush
@endif

    {{-- Grille des annonces --}}
    <div class="row g-4">
        @forelse ($ads as $ad)
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">

                    {{-- Carrousel images --}}
                    @if($ad->images->count())
                        <div id="carousel-{{ $ad->id }}" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($ad->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ asset('storage/' . $image->path) }}" 
                                             class="d-block w-100 img-fluid"
                                             style="height:250px; object-fit:cover;"
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
                        <img src="{{ asset('storage/placeholder.jpg') }}" 
                             class="card-img-top"
                             style="height:250px; object-fit:cover;"
                             alt="Aucune image">
                    @endif

                    {{-- Contenu --}}
                    <div class="card-body d-flex flex-column">

                        {{-- PHOTO DE PROFIL --}}
                        <a href="{{ route('user.show', $ad->user->id) }}">
                            <div style="position:absolute; top:10px; right:10px; z-index:10;">
                                @if($ad->user->profile_photo)
                                    <img src="{{ asset('storage/' . $ad->user->profile_photo) }}" 
                                         alt="Profil"
                                         class="rounded-circle border border-white shadow"
                                         style="width:55px; height:55px; object-fit:cover;">
                                @else
                                    <img src="{{ asset('storage/profile_placeholder.png') }}" 
                                         alt="Profil"
                                         class="rounded-circle border border-white shadow"
                                         style="width:55px; height:55px; object-fit:cover;">
                                @endif
                            </div>
                        </a>

                        <h5 class="fw-bold mt-2 text-truncate">{{ $ad->title }}</h5>
                        <p class="text-muted small mb-2">{{ Str::limit($ad->description, 100) }}</p>

                        <p class="fw-bold text-success mb-1">
                            💰 {{ number_format($ad->price, 0, ',', ' ') }} {{ $ad->currency }}
                        </p>
                        <p class="text-primary mb-1">📞 {{ $ad->phone ?? 'Numéro non disponible' }}</p>
                        <p class="text-muted mb-2">📍 {{ $ad->location ?? 'Lieu non précisé' }}</p>

                        {{-- Bouton WhatsApp --}}
                        @if(!empty($ad->whatsapp))
                            <a href="https://wa.me/{{ preg_replace('/\D/', '', $ad->whatsapp) }}" 
                               target="_blank"
                               class="btn btn-success btn-sm d-flex align-items-center gap-2 mt-2">
                                <i class="bi bi-whatsapp fs-5"></i>
                                Contactez-moi sur WhatsApp
                            </a>
                        @endif

                        {{-- Boutons actions --}}
                        <div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
                            <a href="{{ route('annonces.edit', $ad->id) }}" class="btn btn-outline-primary btn-sm flex-fill">✏️ Modifier</a>
                            <a href="{{ route('annonces.show', $ad->id) }}" class="btn btn-outline-info btn-sm flex-fill">👁️ Voir</a>
                            <form action="{{ route('annonces.destroy', $ad->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Confirmer la suppression ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">🗑️ Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">Vous n'avez pas encore publié d'annonces.</div>
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
    .carousel-item img { border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem; }
    @media (max-width: 576px) {
        .btn { font-size: 0.9rem; padding: 0.6rem 0.8rem; }
        h2 { font-size: 1.3rem; }
    }
</style>
@endpush

@push('scripts')
<script>
    function checkUnreadMessages() {
        fetch("{{ route('user.unread-messages') }}")
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('message-badge');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Vérifie toutes les 10 secondes
    setInterval(checkUnreadMessages, 10000);

    // Vérifie au chargement
    checkUnreadMessages();
</script>
@endpush

@endsection
