@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-4">✏️ Modifier l'annonce</h2>

    {{-- ⚠️ Erreurs de validation --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 📝 Formulaire --}}
    <form id="edit-form" action="{{ route('annonces.update', $ad) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Titre --}}
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" name="title" class="form-control form-control-lg" value="{{ $ad->title }}" required>
        </div>

        {{-- Description --}}
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control form-control-lg" rows="4" required>{{ $ad->description }}</textarea>
        </div>

        {{-- Prix + devise --}}
        <div class="mb-3">
            <label class="form-label">Prix</label>
            <div class="input-group input-group-lg">
                <input type="text" name="price" class="form-control" value="{{ $ad->price }}" required>
                <select name="currency" class="form-select" required>
                    <option value="GNF" {{ $ad->currency == 'GNF' ? 'selected' : '' }}>GNF</option>
                    <option value="EUR" {{ $ad->currency == 'EUR' ? 'selected' : '' }}>€ Euro</option>
                    <option value="USD" {{ $ad->currency == 'USD' ? 'selected' : '' }}>$ Dollar</option>
                </select>
            </div>
        </div>

        {{-- Téléphone --}}
        <div class="mb-3">
            <label class="form-label">Téléphone</label>
            <input type="text" name="phone" class="form-control form-control-lg" value="{{ $ad->phone }}" required>
        </div>

        {{-- WhatsApp --}}
        <div class="mb-3"> 
            <label for="whatsapp" class="form-label">WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control form-control-lg" value="{{ $ad->whatsapp }}" required>
        </div>

        {{-- Localisation --}}
        <div class="mb-3">
            <label class="form-label">Lieu</label>
            <input type="text" name="location" class="form-control form-control-lg" value="{{ $ad->location }}" required>
        </div>

        {{-- Images existantes --}}
        <div class="mb-3">
            <label class="form-label">Images actuelles</label>
            <div id="existing-preview" class="d-flex flex-wrap gap-2">
                @foreach ($ad->images as $image)
                    <div class="position-relative" data-id="{{ $image->id }}">
                        <img src="{{ asset('storage/' . $image->path) }}" 
                             class="rounded shadow-sm"
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 remove-existing" 
                                style="border-radius: 50%; padding: 0.2rem 0.4rem;">❌</button>
                        <input type="hidden" name="existing_images[]" value="{{ $image->id }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Nouvelles images --}}
        <div class="mb-3">
            <label class="form-label">Ajouter de nouvelles images</label>
            <input type="file" id="image-input" name="images[]" class="form-control form-control-lg" multiple accept="image/*">
            <div id="new-preview" class="mt-3 d-flex flex-wrap gap-2"></div>
        </div>

        {{-- ✅ Bouton responsive --}}
        <button type="submit" class="btn btn-primary btn-lg w-100">💾 Mettre à jour l'annonce</button>
    </form>
</div>

{{-- Script --}}
<script>
    const existingPreview = document.getElementById('existing-preview');
    const imageInput = document.getElementById('image-input');
    const newPreview = document.getElementById('new-preview');
    let newImages = [];

    // Supprimer image existante
    existingPreview.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-existing')) {
            const container = e.target.closest('[data-id]');
            container.remove();
        }
    });

    // Prévisualisation nouvelles images
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
                wrapper.style.width = '100px';
                wrapper.style.height = '100px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100%';
                img.style.height = '100%';
                img.style.objectFit = 'cover';
                img.classList.add('rounded', 'shadow-sm');

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

    // Soumission du formulaire avec nouvelles images
    document.getElementById('edit-form').addEventListener('submit', function (e) {
        const input = document.getElementById('image-input');
        if (input) input.remove();

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
</script>
@endsection
