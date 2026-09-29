<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    <div class="bg-white rounded-xl shadow p-5">
        <p>Commandes aujourd'hui</p>
        <h2 class="text-3xl font-bold">
            {{ $todayOrders }}
        </h2>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <p>En attente</p>
        <h2 class="text-3xl font-bold">
            {{ $pendingOrders }}
        </h2>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <p>Préparation</p>
        <h2 class="text-3xl font-bold">
            {{ $preparingOrders }}
        </h2>
    </div>

    <div class="bg-white rounded-xl shadow p-5">
        <p>Restaurants actifs</p>
        <h2 class="text-3xl font-bold">
            {{ $activeRestaurants }}
        </h2>
    </div>

</div>