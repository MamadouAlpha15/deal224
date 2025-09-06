<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Affiche la page profil avec toutes les annonces de l'utilisateur
    public function show($id)

    {
       
        $user = User::with(['ads.images'])->findOrFail($id);

        return view('users.show', compact('user'));
    }
}
