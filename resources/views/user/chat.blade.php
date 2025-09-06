@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">💬 Chat avec l'administration</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Messages -->
    <div style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto; margin-bottom:15px;">
        @forelse($messages as $msg)
            <div class="mb-2 p-2 {{ $msg->user_id == auth()->id() ? 'bg-light text-end' : 'bg-white' }}"
                 style="border-radius:6px;">
                <strong>
                    {{ $msg->user_id == auth()->id() ? 'Moi' : $msg->user->name }}
                </strong> :
                {{ $msg->message }}
                <div style="font-size:0.8em; color:#666;">
                    {{ $msg->created_at->format('d/m H:i') }}
                </div>
            </div>
        @empty
            <p class="text-muted">Aucun message pour l’instant.</p>
        @endforelse
    </div>

    <!-- Bouton répondre (UNIQUEMENT si dernier msg = admin) -->
    @php
        $dernierMessage = $messages->last();
        $peutRepondre = $dernierMessage && $dernierMessage->user->role === 'superadmin';
    @endphp

    @if($peutRepondre)
        <form action="{{ route('user.chat.reply', $payment->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="text" name="message" class="form-control" placeholder="Votre réponse..." required>
            </div>
            <button class="btn btn-primary">Envoyer ma réponse</button>
        </form>
    @else
        <p class="text-muted">Vous ne pouvez pas répondre pour l’instant.</p>
    @endif
</div>
<a href="{{ route('dashboard') }}" class="btn btn-warning">retrour a mon interface</a>





@endsection
