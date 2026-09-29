<div class="bg-white rounded-xl shadow p-6">

    <h2 class="font-bold mb-4">
        Alertes système
    </h2>

    <ul class="space-y-3">

        <li>
            Paiements échoués :
            {{ $failedPayments }}
        </li>

        <li>
            Abonnements expirés :
            {{ $expiredSubscriptions }}
        </li>

        <li>
            Restaurants en attente :
            {{ $pendingRestaurants }}
        </li>

    </ul>

</div>