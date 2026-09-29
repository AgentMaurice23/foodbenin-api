<div class="bg-white rounded-xl shadow p-6">

    <div class="flex justify-between mb-4">

        <h2 class="text-lg font-bold">
            Restaurants récents
        </h2>

        <a href="#"
           class="text-sm text-blue-600">
            Voir tout
        </a>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full">

            <thead>

                <tr class="border-b">

                    <th class="text-left py-3">
                        Restaurant
                    </th>

                    <th class="text-left py-3">
                        Propriétaire
                    </th>

                    <th class="text-left py-3">
                        Ville
                    </th>

                    <th class="text-left py-3">
                        Zone
                    </th>

                    <th class="text-left py-3">
                        Statut
                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($restaurants as $restaurant)

                    <tr class="border-b">

                        <td class="py-3">

                            <div class="font-semibold">
                                {{ $restaurant->name }}
                            </div>

                        </td>

                        <td class="py-3">

                            {{ $restaurant->owner?->name }}

                        </td>

                        <td class="py-3">

                            {{ $restaurant->city?->name }}

                        </td>

                        <td class="py-3">

                            {{ $restaurant->zone?->name }}

                        </td>

                        <td class="py-3">

                            @if($restaurant->status == 'approved')

                                <span class="px-2 py-1 rounded bg-green-100">
                                    Approuvé
                                </span>

                            @elseif($restaurant->status == 'pending')

                                <span class="px-2 py-1 rounded bg-yellow-100">
                                    En attente
                                </span>

                            @else

                                <span class="px-2 py-1 rounded bg-red-100">
                                    Suspendu
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5" class="text-center py-6">

                            Aucun restaurant

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>