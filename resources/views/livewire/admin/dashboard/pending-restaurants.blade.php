<div class="bg-white rounded-xl shadow p-6">

    <h2 class="font-bold mb-5">
        Restaurants en attente
    </h2>

    <table class="w-full">

        <thead>
            <tr>
                <th>Nom</th>
                <th>Propriétaire</th>
                <th>Ville</th>
            </tr>
        </thead>

        <tbody>

            @foreach($restaurants as $restaurant)

                <tr>

                    <td>
                        {{ $restaurant->name }}
                    </td>

                    <td>
                        {{ $restaurant->owner->name }}
                    </td>

                    <td>
                        {{ $restaurant->city->name }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>