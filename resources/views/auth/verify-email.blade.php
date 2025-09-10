<x-guest-layout>
    <div class="card shadow-sm border-0 mx-auto my-4" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title mb-3">Vérification email</h5>
            <p class="text-muted small">
                Merci pour votre inscription ! Avant de commencer, confirmez votre adresse email
                en cliquant sur le lien que nous venons d’envoyer.  
                Si vous n’avez rien reçu, vous pouvez demander un nouvel envoi.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success small" role="alert">
                    Un nouveau lien de vérification a été envoyé à votre adresse email.
                </div>
            @endif

            <div class="d-flex justify-content-between mt-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Renvoyer le lien</button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-link text-danger">Se déconnecter</button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
