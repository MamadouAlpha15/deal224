@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chat pour la demande #{{ $payment->id }} ({{ $payment->user->name }})</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div style="border:1px solid #ccc; padding:10px; max-height:400px; overflow-y:auto; margin-bottom:15px;">
        @forelse($messages as $msg)
            <div>
                <strong>{{ $msg->user->name }} :</strong> {{ $msg->message }}
                <span style="font-size:0.8em; color:#666;">({{ $msg->created_at->format('d/m H:i') }})</span>
            </div>
        @empty
            <p>Aucun message pour le moment.</p>
        @endforelse
    </div>

    <form action="{{ route('superadmin.boost.message', $payment->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <input type="text" name="message" class="form-control" placeholder="Écrire un message..." required>
        </div>
        <button class="btn btn-primary">Envoyer</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Retour</a>
    </form>
</div>
{{-- Bouton global pour supprimer tous les messages de ce paiement --}}
<form action="{{ route('messages.deleteAll', $payment->id) }}" method="POST" class="mt-3">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger"
        onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer tous les messages de ce paiement ?')">
        Supprimer tous les messages
    </button>
</form>



@endsection
