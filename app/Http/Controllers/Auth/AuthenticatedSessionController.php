<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request): RedirectResponse
{
    // Authentifie l'utilisateur
    $request->authenticate();

    // Régénère la session
    $request->session()->regenerate();

  //verifie d'abord lutilisateur connecte

 
  $user = Auth::user();
  //verifie le role de l'utilisateur

  if(auth()->user()->isSuperAdmin()){
    return redirect()->route('superadmin.boost');
  }

  return redirect()->route('annonces.index');
}
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}


