@extends('layouts.app') 
{{-- 🧱 Hérite du layout principal "app.blade.php" --}}

@section('content') 
<div class="container">
    <h2>➕ Nouvelle annonce</h2>

    {{-- ⚠️ Affiche les erreurs de validation --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 📝 Infos pour l’utilisateur --}}
<div class="alert alert-info">
    ℹ️ <strong>Important :</strong>  
    <ul class="mb-0">
        <li>Chaque annonce correspond à <strong>un seul produit avec un seul prix</strong>.  
            👉 Exemple : un parfum Dior = 1 annonce avec son prix.  
            👉 Un autre parfum Chanel = une autre annonce avec son prix.  
            👉 Un pantalon et une paire de lunettes doivent avoir <strong>2 annonces séparées</strong>.
        </li>
        <li>Vous pouvez ajouter <strong>plusieurs photos du même produit</strong> pour montrer tous ses détails.  
            👉 Exemple : voiture (extérieur, intérieur, moteur), maison (salon, chambres, façade), téléphone (avant, arrière).  
        </li>
        <li><span class="text-success">Plus vos photos sont variées et claires, plus vos clients auront confiance et contacteront rapidement ✅</span></li>
        <li><span class="text-danger">⚠️ Ne mélangez pas plusieurs produits différents dans une seule annonce</span> (exemple : vêtements + chaussures + lunettes dans la même annonce).  
            👉 Créez une annonce pour <strong>chaque produit</strong>.
        </li>
    </ul>
</div>


    {{-- 📝 Formulaire --}}
    <form id="ad-form" action="{{ route('annonces.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Champ titre --}}
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        {{-- Champ description --}}
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
        </div>

        {{-- Champ prix + monnaie --}}
        <div class="mb-3">
            <label class="form-label">Prix</label>
            <div class="input-group">
                <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                <select name="currency" class="form-select" required>
                    <option value="GNF" {{ old('currency') == 'GNF' ? 'selected' : '' }}>GNF</option>
                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>€ Euro</option>
                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>$ Dollar</option>
                </select>
            </div>
        </div>

        {{-- Téléphone --}}
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
        </div>

        {{-- WhatsApp --}}
        <div class="mb-3"> 
            <label for="whatsapp" class="form-label">WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" required>
        </div>

        {{-- Localisation --}}
        <div class="mb-3">
            <label class="form-label">Lieu</label>
            <input type="text" name="location" class="form-control" value="{{ old('location') }}" required>
        </div> 

        {{-- Images --}}
        <div class="mb-3">
            <label class="form-label">Images du produit (plusieurs possibles)</label>
            <input type="file" 
                   id="image-input" 
                   name="images[]" 
                   class="form-control" 
                   multiple 
                   accept="image/*">
            <small class="text-muted">Ajoutez plusieurs photos pour mieux présenter votre produit.</small>

            {{-- 🖼️ Zone d’aperçu --}}
            <div id="preview" class="mt-3 d-flex flex-wrap gap-2"></div>
        </div>

        <button type="submit" class="btn btn-primary">Publier</button>
    </form>
</div>

{{-- Script d’aperçu --}}
<script>
    const input = document.getElementById('image-input');
    const preview = document.getElementById('preview');
    let filesArray = [];

    input.addEventListener('change', (event) => {
        const files = Array.from(event.target.files);
        files.forEach(file => filesArray.push(file));
        renderPreviews();
    });

    function renderPreviews() {
        preview.innerHTML = '';
        filesArray.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.classList.add('rounded');

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '❌';
                removeBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '0';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.padding = '2px 6px';
                removeBtn.style.fontSize = '12px';

                removeBtn.onclick = () => {
                    filesArray.splice(index, 1);
                    renderPreviews();
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    document.getElementById('ad-form').addEventListener('submit', (e) => {
        const oldInput = document.getElementById('image-input');
        if (oldInput) oldInput.remove();

        const dataTransfer = new DataTransfer();
        filesArray.forEach(file => dataTransfer.items.add(file));

        const newInput = document.createElement('input');
        newInput.type = 'file';
        newInput.name = 'images[]';
        newInput.multiple = true;
        newInput.style.display = 'none';
        newInput.files = dataTransfer.files;

        e.target.appendChild(newInput);
    });
</script>
@endsection
