@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Demander un boost pour toutes vos annonces</h2>

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

        <div style="margin-top:10px;">
            <strong>Prix total :</strong>
            <span id="totalPrice">5000 GNF par annonce</span>
        </div>

        <div style="margin-top:10px;">
            <strong>Référence unique :</strong>
            <span id="reference">{{ $reference }}</span>
        </div>

        <div style="margin-top:10px;">
            <p><strong>Paiement :</strong> 5000 GNF par annonce par jour. Déposez via Orange Money sur <strong>622704058</strong> et téléversez la capture ci-dessous.</p>
             <div>
            <label for="payment_proof">Capture Orange Money :</label>
            <input type="file" name="payment_proof" id="payment_proof" accept="image/*" required>
        </div>
            <p>Toutes vos annonces seront visibles en haut pendant 24h. Pour prolonger, réabonnez-vous.</p>
        </div>

        <div style="margin-top:10px;">
    <strong>Vous avez :</strong> {{ $adsCount }} annonce(s)<br>
    <strong>Prix par annonce :</strong> {{ number_format($pricePerAd, 0, ',', ' ') }} GNF<br>
    <strong>Total à payer :</strong> <span id="totalPrice">{{ number_format($totalPrice, 0, ',', ' ') }} GNF</span>
</div>


       

        <!-- Preview de l'image -->
        <div style="margin-top:10px;">
            <img id="preview" src="#" alt="Capture sélectionnée" style="max-width:200px; display:none;">
        </div>

        <button type="submit">Booster toutes mes annonces</button>
    </form>
</div>

<!-- Script pour prévisualiser l'image -->
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
