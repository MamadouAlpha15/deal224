<x-guest-layout>
   

    <div class="card shadow-sm border-0 mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="card-title mb-4 text-center">Créer un compte</h4>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf

                {{-- Aperçu photo --}}
                <div class="mb-3 text-center">
                    <img id="profile_preview"
                         src="{{ asset('storage/profile_placeholder.png') }}"
                         alt="Aperçu photo"
                         class="rounded-circle border mb-2"
                         style="width: 100px; height: 100px; object-fit: cover;">
                </div>

                {{-- Upload photo --}}
                <div class="mb-3">
                    <label for="profile_photo" class="form-label">Photo de profil</label>
                    <input id="profile_photo" type="file" name="profile_photo"
                           class="form-control @error('profile_photo') is-invalid @enderror"
                           accept="image/*"
                           onchange="previewProfilePhoto(event)">
                    @error('profile_photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Script preview photo --}}
                <script>
                    function previewProfilePhoto(event) {
                        const input = event.target;
                        const preview = document.getElementById('profile_preview');
                        if (input.files && input.files[0]) {
                            const reader = new FileReader();
                            reader.onload = e => preview.src = e.target.result;
                            reader.readAsDataURL(input.files[0]);
                        }
                    }
                </script>

                {{-- Nom --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Nom complet</label>
                    <input id="name" type="text" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           required>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Boutons --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('login') }}" class="small">Déjà un compte ?</a>
                    <button type="submit" class="btn btn-primary">
                        S’enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
