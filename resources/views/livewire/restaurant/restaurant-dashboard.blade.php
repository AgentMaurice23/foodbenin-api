<div class="space-y-6">

    <div>

        <h1 class="text-3xl font-bold">

            {{ $restaurant->name }}

        </h1>

        <p class="text-gray-500">

            Dashboard restaurant

        </p>

    </div>

    <div class="grid grid-cols-5 gap-6">

        <div class="bg-white p-6 rounded-xl">

            <h3>Total commandes</h3>

            <div class="text-3xl font-bold">

                {{ $totalOrders }}

            </div>

        </div>

        <div class="bg-white p-6 rounded-xl">

            <h3>En attente</h3>

            <div class="text-3xl font-bold">

                {{ $pendingOrders }}

            </div>

        </div>

        <div class="bg-white p-6 rounded-xl">

            <h3>Produits</h3>

            <div class="text-3xl font-bold">

                {{ $productsCount }}

            </div>

        </div>

        <div class="bg-white p-6 rounded-xl">

            <h3>Avis</h3>

            <div class="text-3xl font-bold">

                {{ $reviewsCount }}

            </div>

        </div>

        <div class="bg-white p-6 rounded-xl">

            <h3>Revenus</h3>

            <div class="text-3xl font-bold">

                {{ number_format($revenue) }}

                FCFA

            </div>

        </div>

    </div>

</div>