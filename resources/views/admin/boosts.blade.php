@extends('layouts.app')

@section('content')
<form action="{{ route('superadmin.boost') }}" method="GET" class="mb-3 d-flex gap-2">
    <input type="text" name="q" class="form-control" placeholder="Tapez le numéro de dépôt" value="{{ request('q') }}">
    <button class="btn btn-primary" type="submit">Rechercher</button>
</form>

<div class="container">
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-3">
            <h5>👀 Visites</h5>
            <p class="fs-4 fw-bold">{{ $visitsCount }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-3">
            <h5>📝 Annonces créées</h5>
            <p class="fs-4 fw-bold">{{ $adsCount }}</p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 text-center p-3">
            <h5>🚀 Boosts confirmés</h5>
            <p class="fs-4 fw-bold">{{ $boostsCount }}</p>
        </div>
    </div>
</div>

    <!-- ======================= -->
    <!-- Titre de la page -->
    <!-- ======================= -->
    <h2>Gestion des demandes de boost</h2>

    <!-- Message de succès après action -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- ======================= -->
    <!-- Onglets pour filtrer : Pending / Paid -->
    <!-- ======================= -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#pending">En attente</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#paid">Boost confirmé</a>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ======================= -->
        <!-- PENDING PAYMENTS TAB -->
        <!-- ======================= -->
        <div class="tab-pane fade show active" id="pending">
            <table id="pendingTable" class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Nombre d'annonces</th>
                        <th>Montant (GNF)</th>
                        <th>Capture + Référence</th>
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
                                <!-- Capture de paiement -->
                                @if($payment->payment_proof)
                                    <a href="{{ asset('storage/'.$payment->payment_proof) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$payment->payment_proof) }}"
                                             style="max-width:80px; cursor:pointer; border:1px solid #ccc; border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">Aucune capture</span>
                                @endif

                                      <div>
                                      <strong>Numéro de dépôt :</strong>
                                        <span class="depot" id="depot-{{ $payment->id }}">
                                               {{ $payment->depot ?? '—' }}
                                         </span>
                                  </div>


                                <!-- Bouton pour afficher/cacher le chat -->
                                <a href="{{ route('superadmin.boost.showChat', $payment->id) }}" class="btn btn-sm btn-info mt-1">Chat</a>

                                <!-- Box de chat cachée par défaut -->
                                <div class="chat-box" id="chat-box-{{ $payment->id }}" 
                                     style="display:none; border:1px solid #ccc; padding:5px; margin-top:5px; max-height:200px; overflow-y:auto;">
                                    <!-- Conteneur des messages -->
                                    <div class="messages" id="messages-{{ $payment->id }}"></div>

                                    <!-- Input pour envoyer un message -->
                                    <input type="text" class="form-control mt-1" placeholder="Écrire un message..." data-payment="{{ $payment->id }}">
                                </div>
                            </td>

                            <!-- Actions : Valider / Supprimer -->
                            <td class="d-flex gap-1">
                                <form action="{{ route('superadmin.boost.approve', $payment->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-primary">Valider</button>
                                </form>

                                <form action="{{ route('superadmin.boost.delete', $payment->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer cette demande ?');">
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

        <!-- ======================= -->
        <!-- PAID PAYMENTS TAB -->
        <!-- ======================= -->
        <div class="tab-pane fade" id="paid">
            <table id="paidTable" class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Nombre d'annonces</th>
                        <th>Montant (GNF)</th>
                        <th>Capture + Référence</th>
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
                                             style="max-width:80px; cursor:pointer; border:1px solid #ccc; border-radius:4px;">
                                    </a>
                                @else
                                    <span class="text-muted">Aucune capture</span>
                                @endif

                                <div>
                                      <strong>Numéro de dépôt :</strong>
                                        <span class="depot" id="depot-{{ $payment->id }}">
                                               {{ $payment->depot ?? '—' }}
                                         </span>
                                  </div>

                            </td>
                            <td>
                                <form action="{{ route('superadmin.boost.all', $payment->user->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Booster toutes les annonces</button>
                                </form>
                            </td>

                              
                        </tr>
                    @endforeach
                </tbody>
               <!-- Bouton global pour supprimer tous les boosts confirmés -->
<form action="{{ route('superadmin.boost.deleteAllConfirmed') }}" method="POST" class="mb-3">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger"
        onclick="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer tous les boosts confirmés ?')">
        Supprimer tous les boosts confirmés
    </button>
</form>

            </table>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function(){

    // Initialisation DataTables
    $('#pendingTable, #paidTable').DataTable({
        "order": [[0,"desc"]],
        "pageLength": 25
    });

    // =======================
    // CHAT : ouverture/caché + envoi
    // =======================

    // Toggle chat
    $('.chat-btn').click(function(){
        let paymentId = $(this).data('payment');
        let box = $('#chat-box-' + paymentId);
        box.toggle();
        if(box.is(':visible')){
            loadMessages(paymentId);
        }
    });

    // Envoi message avec Enter
    $('.chat-box input').keypress(function(e){
        if(e.which == 13){
            let paymentId = $(this).data('payment');
            let msg = $(this).val();
            if(msg.trim() == '') return;
            $(this).val('');

            fetch(`/superadmin/boost/${paymentId}/message`, {
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN':'{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: msg })
            }).then(res => res.json())
            .then(data => {
                loadMessages(paymentId);

                // Si message contient une référence, mettre à jour à côté de la capture
                let refMatch = data.message.match(/[A-Z0-9]{6,}/);
                if(refMatch){
                    $('#ref-' + paymentId).text(refMatch[0]);
                }
            }).catch(err => console.error(err));
        }
    });

    // Rafraîchissement automatique toutes les 5 secondes
    setInterval(function(){
        @foreach($pendingPayments as $payment)
            loadMessages({{ $payment->id }});
        @endforeach
    }, 5000);

});

// Charger les messages depuis le serveur
function loadMessages(paymentId){
    fetch(`/superadmin/boost/${paymentId}/messages`)
    .then(res => res.json())
    .then(data => {
        let html = '';
        data.forEach(msg=>{
            html += `<div><strong>${msg.user.name}:</strong> ${msg.message}</div>`;
        });
        $('#messages-' + paymentId).html(html);
        $('#messages-' + paymentId).scrollTop($('#messages-' + paymentId)[0].scrollHeight);
    }).catch(err => console.error(err));
}

// Si message contient un numéro de dépôt (6 chiffres ou plus)
let depotMatch = data.message.match(/\d{6,}/);
if(depotMatch){
    $('#depot-' + paymentId).text(depotMatch[0]);
}

</script>
@endsection
