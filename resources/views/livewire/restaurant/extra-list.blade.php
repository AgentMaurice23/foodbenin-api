<div>

    <div class="flex justify-between">

        <h1 class="text-2xl font-bold">

            Extras

        </h1>

        <a
            href="{{
                route(
                    'restaurant.extras.create',
                    $restaurant
                )
            }}"
            class="
                px-4
                py-2
                bg-black
                text-white
                rounded
            ">

            Ajouter

        </a>

    </div>

    <table class="w-full mt-6">

        <thead>

            <tr>

                <th>Nom</th>

                <th>Prix</th>

                <th>Statut</th>

                <th></th>

            </tr>

        </thead>

        <tbody>

            @foreach($extras as $extra)

                <tr>

                    <td>
                        {{ $extra->name }}
                    </td>

                    <td>
                        {{ $extra->price }}
                    </td>

                    <td>

                        <button
                            wire:click="
                                toggle(
                                    {{ $extra->id }}
                                )
                            ">

                            {{
                                $extra->is_available
                                ? 'Actif'
                                : 'Inactif'
                            }}

                        </button>

                    </td>

                    <td>

                        <a
                            href="{{
                                route(
                                    'restaurant.extras.edit',
                                    [
                                        $restaurant,
                                        $extra
                                    ]
                                )
                            }}">

                            Modifier

                        </a>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>