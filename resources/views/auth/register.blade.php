<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

      <!-- Photo de profil -->
<div class="mt-4">
    <x-input-label for="profile_photo" :value="__('Photo de profil')" />

    <input id="profile_photo"
           type="file"
           name="profile_photo"
           class="block mt-1 w-full"
           accept="image/*"
           onchange="previewProfilePhoto(event)">

    <!-- Zone d'aperçu -->
    <div class="mt-3">
        <img id="profile_preview"
             src="{{ asset('storage/profile_placeholder.png') }}" 
             alt="Aperçu photo"
             class="rounded-full border"
             style="width: 100px; height: 100px; object-fit: cover;">
    </div>

    <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
</div>

<!-- Script aperçu  de la phto -->
<script>
    function previewProfilePhoto(event) {
        const input = event.target;
        const preview = document.getElementById('profile_preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>


        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('avez vous un compte?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __("s'enregistré") }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
