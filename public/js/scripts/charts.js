function initChart() {
    $.ajax({
        type: "GET",
        dataType: "json",
        url: "{{ route('getDataSales') }}",
        success: function (r) {
            let days = r.data.days;
            let sales = r.data.sales;

            const data = {
                labels: days,
                datasets: [{
                    label: 'Dias',
                    data: sales,
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 205, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(201, 203, 207)',
                        'rgb(54, 162, 235)'
                    ],
                    hoverOffset: 4
                }]
            };

            const ctx = document.getElementById('myChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Ventas de la ultima semana'
                        }
                    }
                }
            });
        }
    });
}