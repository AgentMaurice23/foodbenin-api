<div class="space-y-6">

    <div class="flex justify-between">

        <div>

            <h1 class="text-2xl font-bold">
                Mes restaurants
            </h1>

            <p class="text-gray-500">
                Gérez vos établissements
            </p>

        </div>

        <a
            href="{{ route('restaurant.create') }}"
            class="px-4 py-2 bg-black text-white rounded">

            Nouveau restaurant

        </a>

    </div>

    <div class="grid grid-cols-3 gap-6">

        @foreach($restaurants as $restaurant)

            <div
                class="bg-white rounded-xl shadow">

                <img
                    class="h-48 w-full object-cover rounded-t-xl"

                    src="{{
                        $restaurant
                            ->getFirstMediaUrl(
                                'cover'
                            )
                    }}">

                <div class="p-5">

                    <h2
                        class="font-bold text-lg">

                        {{
                            $restaurant->name
                        }}

                    </h2>

                    <p
                        class="text-sm text-gray-500">

                        {{
                            $restaurant
                                ->city
                                ?->name
                        }}
                        -
                        {{
                            $restaurant
                                ->zone
                                ?->name
                        }}

                    </p>

                    <div
                        class="mt-4">

                        Statut :

                        {{
                            $restaurant
                                ->status
                                ->value
                        }}

                    </div>

                    <div
                        class="mt-4">

                        <a

                            href="{{
                                route(
                                    'restaurant.dashboard',
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

                            Dashboard

                        </a>

                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>