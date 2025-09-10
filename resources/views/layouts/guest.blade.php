<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- ✅ Mon logo favicon -->
    <link rel="icon" type="image/png" href="{{ asset('storage/images/deal.png') }}">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Scripts --}}
    @vite(['resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column justify-content-center align-items-center">

        {{-- Barre top --}}
        <div class="container mb-4 d-flex justify-content-end">
            @if (!in_array(Route::currentRouteName(), ['register']))
                <a href="{{ route('register') }}" class="btn btn-primary">
                    S'inscrire
                </a>
            @endif
        </div>

        {{-- Card centrale --}}
        <div class="card shadow-sm border-0 p-4" style="max-width: 450px; width: 100%;">
            <div class="text-center mb-3">
                <img src="{{ asset('storage/images/deal.png') }}" alt="Deal224" width="80">
            </div>

            {{ $slot }}
        </div>

        <p class="mt-3 text-muted small">© {{ date('Y') }} Deal224 — Tous droits réservés</p>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
