<div
    class="bg-white rounded-xl shadow p-6"
    wire:ignore>

    <div class="mb-6">

        <h2 class="text-lg font-bold">

            Revenus de la plateforme

        </h2>

        <p class="text-sm text-gray-500">

            Historique des paiements validés

        </p>

    </div>

    <div id="revenueChart"></div>

</div>

@script

<script>

document.addEventListener(
    'livewire:init',
    function () {

        const options = {

            chart: {

                type: 'area',

                height: 350,

                toolbar: {
                    show: false
                }
            },

            series: [{

                name: 'Revenus',

                data: @json($series)

            }],

            xaxis: {

                categories:
                    @json($categories)
            },

            stroke: {

                curve: 'smooth'
            },

            dataLabels: {

                enabled: false
            },

            yaxis: {

                labels: {

                    formatter: function(val) {

                        return val + ' FCFA';
                    }
                }
            },

            tooltip: {

                y: {

                    formatter: function(val) {

                        return val + ' FCFA';
                    }
                }
            }
        };

        const chart =
            new ApexCharts(
                document.querySelector(
                    "#revenueChart"
                ),
                options
            );

        chart.render();
    });

</script>

@endscript