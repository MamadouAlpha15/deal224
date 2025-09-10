<x-guest-layout>
    <div class="card shadow-sm border-0 mx-auto my-4" style="max-width: 450px;">
        <div class="card-body">
            <h5 class="card-title mb-3">Confirmez votre mot de passe</h5>
            <p class="text-muted small">
                Cette zone est sécurisée. Merci de confirmer votre mot de passe avant de continuer.
            </p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" type="password" name="password" required
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Confirmer</button>
            </form>
        </div>
    </div>
</x-guest-layout>
