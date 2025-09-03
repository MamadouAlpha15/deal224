@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Gestion des demandes de boost</h2>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Onglets : Pending / Paid -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#pending">En attente</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#paid">Boost confirmé</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Pending -->
        <div class="tab-pane fade show active" id="pending">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Nombre d'annonces</th>
                        <th>Montant (GNF)</th>
                        <th>Capture de paiement</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->reference }}</td>
                            <td>{{ $payment->user->name }}</td>
                            <td>{{ $payment->user->email }}</td>
                            <td>{{ $payment->ads_count }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }}</td>
                            <td>
                                @if($payment->payment_proof)
                                    <a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$payment->payment_proof) }}" 
                                             style="max-width:100px; max-height:100px; object-fit:cover; border:1px solid #ccc; border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">Aucune capture</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <form action="{{ route('superadmin.boost.approve', $payment->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Valider paiement</button>
                                </form>

                                <form action="{{ route('superadmin.boost.delete', $payment->id) }}" method="POST" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Paid -->
        <div class="tab-pane fade" id="paid">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Nombre d'annonces</th>
                        <th>Montant (GNF)</th>
                        <th>Capture de paiement</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paidPayments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>{{ $payment->reference }}</td>
                            <td>{{ $payment->user->name }}</td>
                            <td>{{ $payment->user->email }}</td>
                            <td>{{ $payment->ads_count }}</td>
                            <td>{{ number_format($payment->amount, 0, ',', ' ') }}</td>
                            <td>
                                @if($payment->payment_proof)
                                    <a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$payment->payment_proof) }}" 
                                             style="max-width:100px; max-height:100px; object-fit:cover; border:1px solid #ccc; border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">Aucune capture</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('superadmin.boost.all', $payment->user->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Booster toutes ses annonces</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
