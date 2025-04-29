@extends('layouts.dashboard')
@section('content')
<div class="container">
    <h3 class="mb-4">Real-time Analytics</h3>

    <div class="row g-4">
        <!-- Total Revenue -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Total Revenue</h5>
                    <h2 id="totalRevenue">${{ number_format(0, 2) }}</h2>
                </div>
            </div>
        </div>

        <!-- Orders Count Last Minute -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5>Orders Last Minute</h5>
                    <h2 id="lastMinOrders">0</h2>
                </div>
            </div>
        </div>

        <!-- Revenue Over Time Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="orderRevenueChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products Bar Chart -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Top Products (by sales)</h5>
                    <canvas id="topProductsChart" height="100"></canvas>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const ctxRevenue = document.getElementById('orderRevenueChart').getContext('2d');
    const orderRevenueChart = new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Orders Revenue (last min)',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    }
                }
            }
        }
    });

    const ctxTopProducts = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(ctxTopProducts, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Top Products by Sales',
                data: [],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            }]
        }
    });

    setInterval(fetchAnalytics, 5000);  // Fetch analytics every 5 seconds

    function fetchAnalytics() {
        fetch('{{ route('orders.analytics') }}')
            .then(response => response.json())
            .then(data => {
                const now = new Date().toLocaleTimeString();

                // Update total revenue and orders count
                document.getElementById('totalRevenue').textContent = `$${data.total_revenue.toFixed(2)}`;
                document.getElementById('lastMinOrders').textContent = data.last_min_orders;

                // Update Revenue Chart
                if (orderRevenueChart.data.labels.length > 10) {
                    orderRevenueChart.data.labels.shift();
                    orderRevenueChart.data.datasets[0].data.shift();
                }

                orderRevenueChart.data.labels.push(now);
                orderRevenueChart.data.datasets[0].data.push(data.last_min_revenue);
                orderRevenueChart.update();

                // Update Top Products Chart
                topProductsChart.data.labels = data.top_products.map(product => `Product #${product.product_id}`);
                topProductsChart.data.datasets[0].data = data.top_products.map(product => product.total_sold);
                topProductsChart.update();
            })
            .catch(error => console.error('Error fetching analytics:', error));
    }
</script>
@endpush
