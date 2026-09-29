<form wire:submit="save">

    <div>

        <label>

            Nom

        </label>

        <input
            type="text"
            wire:model="name">

    </div>

    <div>

        <label>

            Prix

        </label>

        <input
            type="number"
            wire:model="price">

    </div>

    <button>

        Enregistrer

    </button>

</form>