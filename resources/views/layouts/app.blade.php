<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
     @stack('styles')
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
        <img src="{{ asset('storage/images/deal.png') }}" alt="mon logo" style="height:60px; width:60px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-right:10px;">
            <div class="d-flex align-items-center ms-3">
                @if (!in_array(Route::currentRouteName(), ['home']))
                    <a href="{{ route('home') }}" class="btn btn-warning">
                        Accueil
                    </a>
                @endif
            </div>

            
            <!-- Menu burger pour petits écrans -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Contenu du menu -->
            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item me-3 d-flex align-items-center">
    {{-- Si l’utilisateur a une photo --}}
    @if(Auth::user()->profile_photo)
        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" 
             alt="Profil" 
             style="width:35px; height:35px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-right:8px;">
    @else
        {{-- Image par défaut si pas de photo --}}
        <img src="{{ asset('images/profile_placeholder.png') }}" 
             alt="Profil" 
             style="width:35px; height:35px; object-fit:cover; border-radius:50%; border:2px solid #fff; margin-right:8px;">
    @endif

    <span class="nav-link text-white">{{ Auth::user()->name }}</span>
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

    <!-- Espace pour ne pas cacher le contenu sous la navbar -->
    <div style="padding-top: 80px;"></div>

    <!-- Contenu principal -->
    <main class="container">
        @yield('content')
    </main>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>