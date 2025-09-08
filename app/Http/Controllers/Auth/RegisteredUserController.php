<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'profile_photo' => ['nullable', 'image', 'max:5041'], // ✅ validation image
    ]);

    // ✅ Stockage photo si envoyée
    $path = null;
    if ($request->hasFile('profile_photo')) {
        $path = $request->file('profile_photo')->store('profiles', 'public');
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'profile_photo' => $path, // ✅ sauvegarde du chemin
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect()->route('annonces.index')
                     ->with('success', 'Inscription réussie. Bienvenue !');
}

}
