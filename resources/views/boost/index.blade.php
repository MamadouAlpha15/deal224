@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Demander un boost pour vos annonces</h2>

    @if(session('success'))
        <div style="color:green;">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('boost.pay') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="ads_count">Nombre d'annonces à booster :</label>
            <input type="number" name="ads_count" id="ads_count" min="1" max="10" required>
        </div>

        <div>
            <label for="payment_proof">Capture Orange Money :</label>
            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required>
        </div>

        <!-- Preview de l'image -->
        <div style="margin-top:10px;">
            <img id="preview" src="#" alt="Capture sélectionnée" style="max-width:200px; display:none;">
        </div>

        <div>
            <p>Envoyez le paiement au numéro : <strong>622704058</strong> et téléversez une capture ci-dessus.</p>
        </div>

        <button type="submit">Demander le boost</button>
    </form>
</div>

<!-- Script pour afficher l'image sélectionnée -->
<script>
document.getElementById('payment_proof').addEventListener('change', function(event){
    const [file] = this.files;
    if (file) {
        const preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    }
});
</script>
@endsection
