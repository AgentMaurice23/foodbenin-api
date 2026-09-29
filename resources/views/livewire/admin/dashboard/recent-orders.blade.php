<div class="bg-white rounded-xl p-6 shadow">

    <h2 class="font-bold mb-4">
        Dernières commandes
    </h2>

    <table class="w-full">

        <thead>
            <tr>
                <th>ID</th>
                <th>Total</th>
                <th>Statut</th>
            </tr>
        </thead>

        <tbody>

            @foreach($orders as $order)

                <tr>
                    <td>
                        {{ $order->id }}
                    </td>

                    <td>
                        {{ $order->total }}
                    </td>

                    <td>
                        {{ $order->status }}
                    </td>
                </tr>

            @endforeach

        </tbody>

    </table>

</div>