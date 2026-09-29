<div class="grid grid-cols-4 gap-6">

    <div class="bg-white rounded-xl shadow p-5">

        <p>Revenu du jour</p>

        <h2 class="text-2xl font-bold">

            {{ number_format($todayRevenue) }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p>Revenu du mois</p>

        <h2 class="text-2xl font-bold">

            {{ number_format($monthRevenue) }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p>Nouveaux utilisateurs</p>

        <h2 class="text-2xl font-bold">

            {{ $newUsers }}

        </h2>

    </div>

    <div class="bg-white rounded-xl shadow p-5">

        <p>Commandes du mois</p>

        <h2 class="text-2xl font-bold">

            {{ $newOrders }}

        </h2>

    </div>

</div>