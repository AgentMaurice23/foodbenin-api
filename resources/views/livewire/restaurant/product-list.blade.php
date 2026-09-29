<div class="space-y-6">

    <div class="flex justify-between">

        <h1 class="text-2xl font-bold">
            Produits
        </h1>

        <a
            href="{{
                route(
                    'restaurant.products.create',
                    $restaurant
                )
            }}"
            class="px-4 py-2 bg-black text-white rounded">

            Nouveau produit

        </a>

    </div>

    <input
        type="text"
        wire:model.live="search"
        placeholder="Rechercher">

    <table class="w-full">

        <thead>

            <tr>
                <th>Image</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix</th>
                <th>Stock</th>
                <th>Disponible</th>
                <th>Vedette</th>
                <th></th>
            </tr>

        </thead>

        <tbody>

        @foreach($products as $product)

            <tr>

                <td>

                    @if($product->thumbnail)

                        <img
                            src="{{ $product->thumbnail }}"
                            class="h-14">

                    @endif

                </td>

                <td>

                    {{ $product->name }}

                </td>

                <td>

                    {{ $product->category?->name }}

                </td>

                <td>

                    {{ $product->currentPrice() }}

                </td>

                <td>

                    @if(
                        $product
                            ->stock_type
                            ->value
                            === 'unlimited'
                    )

                        Illimité

                    @elseif(
                        $product
                            ->stock_type
                            ->value
                            === 'out_of_stock'
                    )

                        Rupture

                    @else

                        {{
                            $product
                                ->stock_quantity
                        }}

                    @endif

                </td>

                <td>

                    <button
                        wire:click="
                            toggleAvailability(
                                {{ $product->id }}
                            )
                        ">

                        {{
                            $product
                                ->is_available
                            ? 'Oui'
                            : 'Non'
                        }}

                    </button>

                </td>

                <td>

                    <button
                        wire:click="
                            toggleFeatured(
                                {{ $product->id }}
                            )
                        ">

                        {{
                            $product
                                ->is_featured
                            ? 'Oui'
                            : 'Non'
                        }}

                    </button>

                </td>

                <td>

                    <a
                        href="{{
                            route(
                                'restaurant.products.edit',
                                [
                                    $restaurant,
                                    $product
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

    {{ $products->links() }}

</div>