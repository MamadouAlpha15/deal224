@extends('layouts.app') 
{{-- 🧱 Hérite du layout principal "app.blade.php" pour utiliser le header/footer global --}}

@section('content') 
<div class="container">
    <h2>➕ Nouvelle annonce</h2>

    {{-- ⚠️ Affiche les erreurs de validation s’il y en a --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li> {{-- 🔁 Affiche chaque erreur --}}
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 📝 Formulaire pour publier une nouvelle annonce --}}
    <form id="ad-form" 
          action="{{ route('annonces.store') }}" {{-- 🧭 Envoie le formulaire vers la méthode `store` --}}
          method="POST" 
          enctype="multipart/form-data"> {{-- 📎 Permet l’envoi de fichiers --}}
          
        @csrf {{-- 🔐 Protection CSRF obligatoire dans tous les formulaires --}}

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

        {{-- Champ prix --}}
        <div class="mb-3">
            <label class="form-label">Prix (en GNF)</label>
            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
        </div>
        {{--champ telephone --}}
         <div class="mb-3">
            <label class="form-label">Teléphone</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
        </div>
        
        {{--champ whatsapp --}}
        <div clas="mb-3"> 
        <label for="whatssap" class="form-label">WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}" required>
        
        </div>

        
        {{--champ localisation --}}
         <div class="mb-3">
            <label class="form-label">Lieu</label>
            <input type="adresse" name="location" class="form-control" value="{{ old('location') }}" required>
        </div> 

        {{-- Champ pour ajouter plusieurs images --}}
        <div class="mb-3">
            <label class="form-label">Images (vous pouvez en ajouter plusieurs)</label>
            <input type="file" 
                   id="image-input" 
                   name="images[]" 
                   class="form-control" 
                   multiple {{-- 📷 Permet de sélectionner plusieurs fichiers --}}
                   accept="image/*"> {{-- ✅ N'accepte que des images --}}

            {{-- 🖼️ Zone d’aperçu des images sélectionnées --}}
            <div id="preview" class="mt-3 d-flex flex-wrap gap-2"></div>
        </div>

        <button type="submit" class="btn btn-primary">Publier</button>
    </form>
</div>
<script>
    const input = document.getElementById('image-input');  // 🎯 Champ d’input des images
    const preview = document.getElementById('preview');     // 📍 Conteneur pour les aperçus
    let filesArray = []; // 🧰 Tableau pour stocker les fichiers sélectionnés

    // 📦 Quand on sélectionne des fichiers
    input.addEventListener('change', (event) => {
        const files = Array.from(event.target.files); // 🔁 Convertit FileList en tableau

        files.forEach(file => {
            filesArray.push(file); // 📌 Ajoute au tableau temporaire
        });

        renderPreviews(); // 🔄 Met à jour les aperçus
    });

    // 🖼️ Fonction qui affiche les images dans la zone preview
    function renderPreviews() {
        preview.innerHTML = ''; // 🧼 Vide l’aperçu avant de le recréer

        filesArray.forEach((file, index) => {
            const reader = new FileReader(); // 📖 Pour lire le fichier image

            reader.onload = (e) => {
                const wrapper = document.createElement('div'); // 📦 Conteneur pour image + bouton
                wrapper.style.position = 'relative';

                const img = document.createElement('img'); // 🖼️ Image à afficher
                img.src = e.target.result; // 🔗 Affiche l’image chargée
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.classList.add('rounded');

                const removeBtn = document.createElement('button'); // ❌ Bouton de suppression
                removeBtn.innerHTML = '❌';
                removeBtn.classList.add('btn', 'btn-sm', 'btn-danger');
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '0';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.padding = '2px 6px';
                removeBtn.style.fontSize = '12px';

                // 🧹 Supprime l’image du tableau quand on clique sur la croix
                removeBtn.onclick = () => {
                    filesArray.splice(index, 1); // 🔻 Enlève l’image à cet index
                    renderPreviews(); // 🔁 Re-render
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper); // ➕ Ajoute à la zone preview
            };

            reader.readAsDataURL(file); // 🔍 Lit l’image comme une URL
        });
    }

    // 📤 Avant l’envoi du formulaire : injecter les fichiers dans un input dynamique
    document.getElementById('ad-form').addEventListener('submit', (e) => {
        const oldInput = document.getElementById('image-input');
        if (oldInput) oldInput.remove(); // ❌ Supprime l’input initial

        const dataTransfer = new DataTransfer(); // 📦 Simule un input file
        filesArray.forEach(file => {
            dataTransfer.items.add(file); // ➕ Ajoute les fichiers restants
        });

        const newInput = document.createElement('input'); // 🆕 Crée un nouvel input invisible
        newInput.type = 'file';
        newInput.name = 'images[]';
        newInput.multiple = true;
        newInput.style.display = 'none';
        newInput.files = dataTransfer.files; // 📎 Y attache les fichiers

        e.target.appendChild(newInput); // 📌 Ajoute au formulaire
    });
</script>
@endsection
