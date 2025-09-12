<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @stack('styles')

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <img src="{{ asset('storage/images/deal.png') }}" alt="mon logo"
                 style="height:60px; width:60px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-right:10px;">

            <div class="d-flex align-items-center ms-3">
                @if (!in_array(Route::currentRouteName(), ['home']))
                    <a href="{{ route('home') }}" class="btn btn-warning">Accueil</a>
                @endif
            </div>

            <!-- Menu burger -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenu du menu -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item me-3 d-flex align-items-center">
                            {{-- Photo de profil --}}
                            @if(Auth::check() && Auth::user()->profile_photo)
                                <img id="navbar-photo"
                                     src="{{ asset('storage/' . Auth::user()->profile_photo) }}" 
                                     alt="Profil"
                                     style="width:35px; height:35px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-right:8px; cursor:pointer;"
                                     data-bs-toggle="modal" data-bs-target="#editPhotoModal">
                            @else
                                <img id="navbar-photo"
                                     src="{{ asset('images/profile_placeholder.png') }}" 
                                     alt="Profil"
                                     style="width:35px; height:35px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-right:8px; cursor:pointer;"
                                     data-bs-toggle="modal" data-bs-target="#editPhotoModal">
                            @endif
                            <span class="nav-link text-white">{{ Auth::check() ? Auth::user()->name : '' }}</span>
                        </li>

                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="btn btn-outline-light btn-sm" type="submit">Déconnexion</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Espace pour éviter le chevauchement -->
    <div style="padding-top: 80px;"></div>

    <!-- Contenu principal -->
    <main class="container">
        @yield('content')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <!-- Modal Modifier la photo -->
    @auth
    <div class="modal fade" id="editPhotoModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <form action="{{ route('profile.update.photo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="modal-header">
              <h5 class="modal-title">Modifier ma photo de profil</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
              {{-- Photo actuelle --}}
              <img id="current-photo" 
                   src="{{ Auth::user()->profile_photo ? asset('storage/' . Auth::user()->profile_photo) : asset('images/profile_placeholder.png') }}" 
                   alt="Photo actuelle" 
                   class="rounded-circle mb-3" 
                   style="width:120px; height:120px; object-fit:cover; border:2px solid #ddd;">

              {{-- Champ upload --}}
              <input type="file" name="profile_photo" id="profile-photo-input" 
                     class="form-control" accept="image/*" required>

              {{-- Aperçu --}}
              <div id="preview-container" class="mt-3" style="display:none;">
                <p class="fw-bold">Nouvelle photo :</p>
                <img id="preview-photo" class="rounded-circle"
                     style="width:120px; height:120px; object-fit:cover; border:2px solid #28a745;">
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
              <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    @endauth

    <script>
    document.getElementById('profile-photo-input')?.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e) {
                // ✅ Aperçu dans le modal
                document.getElementById('preview-photo').src = e.target.result;
                document.getElementById('preview-container').style.display = 'block';

                // ✅ Mise à jour instantanée de la navbar
                const navbarPhoto = document.getElementById('navbar-photo');
                if(navbarPhoto) {
                    navbarPhoto.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
    </script>

    <!-- SweetAlert2 pour succès et erreurs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Succès 🎉',
                text: "{{ session('success') }}",
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Erreur ⚠️',
                html: `
                    <ul style="text-align:left;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                `,
                confirmButtonText: 'OK'
            });
        </script>
    @endif
</body>
</html>
