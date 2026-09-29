<div class="grid grid-cols-1 md:grid-cols-4 gap-6">

    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-gray-500">
            Restaurants
        </div>

        <div class="text-3xl font-bold">
            {{ $restaurants }}
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-gray-500">
            Clients
        </div>

        <div class="text-3xl font-bold">
            {{ $clients }}
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-gray-500">
            Commandes
        </div>

        <div class="text-3xl font-bold">
            {{ $orders }}
        </div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow">
        <div class="text-gray-500">
            Revenus
        </div>

        <div class="text-3xl font-bold">
            {{ number_format($revenue) }}
            FCFA
        </div>
    </div>

</div>