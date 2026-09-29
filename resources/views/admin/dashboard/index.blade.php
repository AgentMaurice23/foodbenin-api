

{{-- <div class="p-6">

    <h1 class="text-3xl font-bold mb-8">
        Dashboard FoodBenin
    </h1>

    <div class="grid grid-cols-4 gap-6">

        <div class="bg-white p-5 rounded shadow">
            <h3>Restaurants</h3>
            <h1>{{ $stats['restaurants'] }}</h1>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <h3>Clients</h3>
            <h1>{{ $stats['clients'] }}</h1>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <h3>Commandes</h3>
            <h1>{{ $stats['orders'] }}</h1>
        </div>

        <div class="bg-white p-5 rounded shadow">
            <h3>Revenus</h3>
            <h1>
                {{ number_format($stats['revenue']) }}
                FCFA
            </h1>
        </div>

    </div>

</div> --}}

<x-layouts.admin>

    <div class="space-y-6">

        <livewire:admin.dashboard.stats-cards />

        <livewire:admin.dashboard.activity-cards />

        <div class="grid grid-cols-2 gap-6">

            <livewire:admin.dashboard.recent-orders />

            <livewire:admin.dashboard.recent-restaurants />

        </div>

        <div class="grid grid-cols-2 gap-6">

            <livewire:admin.dashboard.pending-restaurants />

            <livewire:admin.dashboard.system-alerts />

        </div>

        <livewire:admin.dashboard.analytics-cards />

        <livewire:admin.dashboard.revenue-chart />

        <livewire:admin.dashboard.platform-health />

    </div>

</x-layouts.admin>