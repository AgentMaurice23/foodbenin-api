<div class="space-y-6">

```
{{-- ========================================================= --}}
{{-- HEADER --}}
{{-- ========================================================= --}}

<div class="flex items-center justify-between">

    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Dashboard Delivery
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Vue globale de l'activité des livraisons.
        </p>
    </div>

    <button
        wire:click="refreshStatistics"
        wire:loading.attr="disabled"
        class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
    >
        <span wire:loading.remove>
            Actualiser
        </span>

        <span wire:loading>
            Actualisation...
        </span>
    </button>

</div>


{{-- ========================================================= --}}
{{-- LIVRAISONS --}}
{{-- ========================================================= --}}

<div>

    <h2 class="mb-3 text-lg font-semibold text-gray-900">
        Livraisons
    </h2>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Total --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                Total
            </p>

            <p class="mt-2 text-3xl font-bold text-gray-900">
                {{ $statistics['deliveries']['total'] ?? 0 }}
            </p>
        </div>

        {{-- En attente --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                En attente
            </p>

            <p class="mt-2 text-3xl font-bold text-yellow-600">
                {{ $statistics['deliveries']['pending'] ?? 0 }}
            </p>
        </div>

        {{-- Assignées --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                Assignées
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-600">
                {{ $statistics['deliveries']['assigned'] ?? 0 }}
            </p>
        </div>

        {{-- Livrées --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                Livrées
            </p>

            <p class="mt-2 text-3xl font-bold text-green-600">
                {{ $statistics['deliveries']['delivered'] ?? 0 }}
            </p>
        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- STATUTS --}}
{{-- ========================================================= --}}

<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

    <h2 class="mb-5 text-lg font-semibold text-gray-900">
        État des livraisons
    </h2>

    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">

        <div>
            <p class="text-sm text-gray-500">
                Acceptées
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['deliveries']['accepted'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Vers restaurant
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['deliveries']['going_to_restaurant'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Au restaurant
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['deliveries']['at_restaurant'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Récupérées
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['deliveries']['picked_up'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                En livraison
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['deliveries']['on_the_way'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Arrivées
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['deliveries']['arrived'] ?? 0 }}
            </p>
        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- LIVREURS --}}
{{-- ========================================================= --}}

<div>

    <h2 class="mb-3 text-lg font-semibold text-gray-900">
        Livreurs
    </h2>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Total --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                Total livreurs
            </p>

            <p class="mt-2 text-3xl font-bold text-gray-900">
                {{ $statistics['drivers']['total'] ?? 0 }}
            </p>
        </div>

        {{-- Online --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                En ligne
            </p>

            <p class="mt-2 text-3xl font-bold text-green-600">
                {{ $statistics['drivers']['online'] ?? 0 }}
            </p>
        </div>

        {{-- Busy --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                En livraison
            </p>

            <p class="mt-2 text-3xl font-bold text-blue-600">
                {{ $statistics['drivers']['busy'] ?? 0 }}
            </p>
        </div>

        {{-- Offline --}}
        <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">
                Hors ligne
            </p>

            <p class="mt-2 text-3xl font-bold text-gray-500">
                {{ $statistics['drivers']['offline'] ?? 0 }}
            </p>
        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- REVENUS --}}
{{-- ========================================================= --}}

<div>

    <h2 class="mb-3 text-lg font-semibold text-gray-900">
        Revenus
    </h2>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

        {{-- Frais livraison --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

            <p class="text-sm text-gray-500">
                Frais de livraison
            </p>

            <p class="mt-2 text-2xl font-bold text-gray-900">
                {{ number_format(
                    $statistics['revenue']['delivery_fees'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </p>

        </div>

        {{-- Gains livreurs --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

            <p class="text-sm text-gray-500">
                Gains des livreurs
            </p>

            <p class="mt-2 text-2xl font-bold text-blue-600">
                {{ number_format(
                    $statistics['revenue']['driver_earnings'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </p>

        </div>

        {{-- Commission --}}
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

            <p class="text-sm text-gray-500">
                Commission FoodBenin
            </p>

            <p class="mt-2 text-2xl font-bold text-green-600">
                {{ number_format(
                    $statistics['revenue']['platform_commission'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </p>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- AUJOURD'HUI --}}
{{-- ========================================================= --}}

<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

    <h2 class="mb-5 text-lg font-semibold text-gray-900">
        Aujourd'hui
    </h2>

    <div class="grid grid-cols-2 gap-5 md:grid-cols-5">

        <div>
            <p class="text-sm text-gray-500">
                Livraisons
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['today']['total'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Livrées
            </p>

            <p class="mt-1 text-xl font-semibold text-green-600">
                {{ $statistics['today']['delivered'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Échecs
            </p>

            <p class="mt-1 text-xl font-semibold text-red-600">
                {{ $statistics['today']['failed'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Annulées
            </p>

            <p class="mt-1 text-xl font-semibold text-orange-600">
                {{ $statistics['today']['cancelled'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Commission
            </p>

            <p class="mt-1 text-xl font-semibold text-green-600">
                {{ number_format(
                    $statistics['today']['platform_commission'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </p>
        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- MOIS --}}
{{-- ========================================================= --}}

<div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">

    <h2 class="mb-5 text-lg font-semibold text-gray-900">
        Mois en cours
    </h2>

    <div class="grid grid-cols-2 gap-5 md:grid-cols-4">

        <div>
            <p class="text-sm text-gray-500">
                Livraisons
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ $statistics['month']['total'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Livrées
            </p>

            <p class="mt-1 text-xl font-semibold text-green-600">
                {{ $statistics['month']['delivered'] ?? 0 }}
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Revenus
            </p>

            <p class="mt-1 text-xl font-semibold">
                {{ number_format(
                    $statistics['month']['revenue'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </p>
        </div>

        <div>
            <p class="text-sm text-gray-500">
                Commission
            </p>

            <p class="mt-1 text-xl font-semibold text-green-600">
                {{ number_format(
                    $statistics['month']['platform_commission'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                FCFA
            </p>
        </div>

    </div>

</div>
```

</div>
