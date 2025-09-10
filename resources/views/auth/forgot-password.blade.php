<x-guest-layout>
    <div class="card shadow-sm border-0 mx-auto my-4" style="max-width: 450px;">
        <div class="card-body">
            <h5 class="card-title mb-3">Mot de passe oublié</h5>
            <p class="text-muted small">
                Pas de problème. Indiquez votre adresse email et nous vous enverrons un lien de réinitialisation.
            </p>

            @if (session('status'))
                <div class="alert alert-success small" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Envoyer le lien de réinitialisation
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
