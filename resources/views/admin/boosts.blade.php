@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Demandes de boost en attente</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Nombre d'annonces</th>
                <th>Montant (GNF)</th>
                <th>Capture de paiement</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->user->name }}</td>
                    <td>{{ $payment->ads_count }}</td>
                    <td>{{ number_format($payment->amount, 0, ',', ' ') }}</td>
                    <td>
                        @if($payment->payment_proof)
                            <!-- Image cliquable pour agrandir -->
                            <a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank">
                                <img src="{{ asset('storage/'.$payment->payment_proof) }}" 
                                     alt="Capture de paiement" 
                                     style="max-width:100px; max-height:100px; object-fit:cover; border:1px solid #ccc; border-radius:4px;">
                            </a>
                        @else
                            <span class="text-muted">Aucune capture</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->status == 'pending')
                            <span class="badge bg-warning text-dark">En attente</span>
                        @else
                            <span class="badge bg-success">Boost confirmé</span>
                        @endif
                    </td>
                    <td>
                        @if($payment->status == 'pending')
                            <form action="{{ route('superadmin.boost.approve', $payment->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Valider le paiement</button>
                            </form>
                        @else
                            <span class="text-success">✓</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
