@extends('layouts.app')

@section('content')
<div class="container">
    <h2>✏️ Modifier l'annonce</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="edit-form" action="{{ route('annonces.update', $ad) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

    

        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control" value="{{ $ad->title }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="5" required>{{ $ad->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Prix (GNF)</label>
            <input type="number" name="price" class="form-control" value="{{ $ad->price }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Teléphone</label>
            <input type="text" name="phone" class="form-control" value="{{ $ad->phone }}" required>
        </div>

           {{--champ whatsapp --}}
        <div clas="mb-3"> 
        <label for="whatssap" class="form-label">WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ $ad->whatsapp }}" required>
        
        </div>

        <div class="mb-3">
            <label class="form-label">Lieu</label>
            <input type="adresse" name="location" class="form-control" value="{{ $ad->location }}" required>
        </div>

        {{-- IMAGES ACTUELLES --}}
        <div class="mb-3">
            <label class="form-label">Images actuelles</label>
            <div id="existing-preview" class="d-flex flex-wrap gap-2">
                @foreach ($ad->images as $image)
                    <div class="position-relative" data-id="{{ $image->id }}">
                        <img src="{{ asset('storage/' . $image->path) }}" 
                             class="rounded" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-existing" style="border-radius: 50%; padding: 0.2rem 0.4rem;">❌</button>
                        <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- NOUVELLES IMAGES --}}
        <div class="mb-3">
            <label class="form-label">Nouvelles images</label>
            <input type="file" id="image-input" name="images[]" class="form-control" multiple accept="image/*">
            <div id="new-preview" class="mt-3 d-flex flex-wrap gap-2"></div>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </form>
</div>

<script>
    const existingPreview = document.getElementById('existing-preview');
    const imageInput = document.getElementById('image-input');
    const newPreview = document.getElementById('new-preview');
    let newImages = [];

    // Supprimer image existante visuellement et retirer le champ hidden
    existingPreview.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-existing')) {
            const container = e.target.closest('[data-id]');
            container.remove();
        }
    });

    // Aperçu des nouvelles images sélectionnées avec croix rouge
    imageInput.addEventListener('change', function (event) {
        const files = Array.from(event.target.files);
        newImages = [...newImages, ...files];
        renderNewImages();
    });

    function renderNewImages() {
        newPreview.innerHTML = '';
        newImages.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function (e) {
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
                removeBtn.type = 'button';
                removeBtn.classList.add('btn', 'btn-danger', 'btn-sm');
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '0';
                removeBtn.style.borderRadius = '50%';
                removeBtn.style.padding = '2px 6px';
                removeBtn.onclick = () => {
                    newImages.splice(index, 1);
                    renderNewImages();
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                newPreview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });
    }

    // Soumettre le bon fichier final
    document.getElementById('edit-form').addEventListener('submit', function (e) {
        const input = document.getElementById('image-input');
        input.remove(); // Supprime l'ancien input

        const dataTransfer = new DataTransfer();
        newImages.forEach(file => dataTransfer.items.add(file));

        const newInput = document.createElement('input');
        newInput.type = 'file';
        newInput.name = 'images[]';
        newInput.multiple = true;
        newInput.style.display = 'none';
        newInput.files = dataTransfer.files;

        e.target.appendChild(newInput);
    });


    // Aperçu pour la photo de profil
    const profileInput = document.querySelector('input[name="profile_photo"]');
    const profileImg = document.getElementById('profile-img');

    profileInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            profileImg.src = e.target.result;
            profileImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
