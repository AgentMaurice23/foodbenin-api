<div class="bg-white p-6 rounded-xl">

    <form
        wire:submit="save"
        class="space-y-5">

        <input
            wire:model="name"
            type="text"
            placeholder="Nom">

        <textarea
            wire:model="description">
        </textarea>

        <input
            wire:model="phone"
            type="text">

        <input
            wire:model="email"
            type="email">

        <input
            wire:model="address"
            type="text">

        <select
            wire:model="city_id">

            <option>
                Ville
            </option>

            @foreach($cities as $city)

                <option
                    value="{{$city->id}}">

                    {{$city->name}}

                </option>

            @endforeach

        </select>

        <select
            wire:model="zone_id">

            <option>
                Zone
            </option>

            @foreach($zones as $zone)

                <option
                    value="{{$zone->id}}">

                    {{$zone->name}}

                </option>

            @endforeach

        </select>

        <input
            wire:model="delivery_fee"
            type="number">

        <input
            wire:model="minimum_order"
            type="number">

        <input
            wire:model="logo"
            type="file">

        <input
            wire:model="cover"
            type="file">

        <button type="submit">

            Créer

        </button>

    </form>

</div>