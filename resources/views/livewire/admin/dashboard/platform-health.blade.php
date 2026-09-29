<x-app-layout>

{{-- <div class="p-6 space-y-6">

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

    <livewire:admin.dashboard.revenue-chart />

    <livewire:admin.dashboard.platform-health />

</div> --}}
<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between items-center mb-5">

        <h2 class="text-lg font-bold">
            Santé de la plateforme
        </h2>

        <span class="text-sm text-gray-500">
            Vue globale
        </span>

    </div>

    <div class="grid grid-cols-3 gap-6">

        <div class="border rounded-lg p-4">

            <div class="text-gray-500">
                Utilisateurs
            </div>

            <div class="text-3xl font-bold mt-2">
                {{ number_format($users) }}
            </div>

        </div>

        <div class="border rounded-lg p-4">

            <div class="text-gray-500">
                Restaurants
            </div>

            <div class="text-3xl font-bold mt-2">
                {{ number_format($restaurants) }}
            </div>

        </div>

        <div class="border rounded-lg p-4">

            <div class="text-gray-500">
                Commandes
            </div>

            <div class="text-3xl font-bold mt-2">
                {{ number_format($orders) }}
            </div>

        </div>

    </div>

</div>
</x-app-layout>