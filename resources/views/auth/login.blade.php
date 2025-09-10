<x-guest-layout>
    {{-- Logo --}}
   
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 450px;">
        <div class="card-body">
            <h4 class="card-title mb-4 text-center">Connexion</h4>

            {{-- Status de session (ex: “Mot de passe réinitialisé”) --}}
            @if (session('status'))
                <div class="alert alert-success small" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email"
                           value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember me + Mot de passe oublié --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
                        <label for="remember_me" class="form-check-label">Se souvenir de moi</label>
                    </div>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small">Mot de passe oublié ?</a>
                    @endif
                </div>

                {{-- Bouton --}}
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>

                <p class="text-center small text-muted mt-3 mb-0">
                    Pas encore de compte ?
                    <a href="{{ route('register') }}">Créer un compte</a>
                </p>
            </form>
        </div>
    </div>
</x-guest-layout>
