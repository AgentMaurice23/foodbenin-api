<div>

    <div class="flex gap-4 mb-6">

        <input
            wire:model.live="search"
            type="text"
            placeholder="Recherche..."
            class="border rounded p-2">

        <select
            wire:model.live="status"
            class="border rounded p-2">

            <option value="">
                Tous
            </option>

            <option value="pending">
                En attente
            </option>

            <option value="approved">
                Approuvés
            </option>

            <option value="suspended">
                Suspendus
            </option>

        </select>

    </div>

    <div class="bg-white rounded-xl shadow">

        <table class="w-full">

            <thead>

                <tr>

                    <th>Restaurant</th>

                    <th>Propriétaire</th>

                    <th>Ville</th>

                    <th>Zone</th>

                    <th>Statut</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody>

                @foreach($restaurants as $restaurant)

                    <tr>

                        <td>
                            {{ $restaurant->name }}
                        </td>

                        <td>
                            {{ $restaurant->owner?->name }}
                        </td>

                        <td>
                            {{ $restaurant->city?->name }}
                        </td>

                        <td>
                            {{ $restaurant->zone?->name }}
                        </td>

                        <td>

                            {{ $restaurant->status }}

                        </td>

                        <td class="space-x-2">

                            <button
                                wire:click="
                                    approve(
                                        {{ $restaurant->id }}
                                    )
                                ">

                                Approuver

                            </button>

                            <button
                                wire:click="
                                    suspend(
                                        {{ $restaurant->id }}
                                    )
                                ">

                                Suspendre

                            </button>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <div class="mt-4">

        {{ $restaurants->links() }}

    </div>

</div>