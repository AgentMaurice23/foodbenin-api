<x-app-layout>

    <div class="flex">

        @include('admin.partials.sidebar')

        <div class="flex-1">

            @include('admin.partials.header')

            <main class="p-6 bg-gray-100 min-h-screen">

                {{ $slot }}

            </main>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</x-app-layout>