@extends('layouts.be')

@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
            <div class="row">
                <!-- Statistik lainnya -->
            </div>

            <!-- Tambahkan div untuk grafik penjualan -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Grafik Penjualan</h4>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesData = @json($salesData); // Mengirim data dari controller ke JavaScript

            console.log(salesData); // Tambahkan ini untuk memeriksa data

            var brands = [];
            var pendingQuantities = [];
            var confirmedQuantities = [];

            // Loop through the sales data to populate the arrays
            Object.keys(salesData.pending).forEach(function(month) {
                salesData.pending[month].forEach(function(data) {
                    brands.push(data.umkm_name);
                    pendingQuantities.push(data.total_quantity_sold);
                });
            });

            Object.keys(salesData.confirmed).forEach(function(month) {
                salesData.confirmed[month].forEach(function(data) {
                    // Assuming we want to merge pending and confirmed quantities for the same brands
                    var index = brands.indexOf(data.umkm_name);
                    if (index > -1) {
                        confirmedQuantities[index] = data.total_quantity_sold;
                    } else {
                        brands.push(data.umkm_name);
                        confirmedQuantities.push(data.total_quantity_sold);
                    }
                });
            });

            // Create chart
            new Chart(ctx, {
                type: 'bar', // Jenis grafik (bar)
                data: {
                    labels: brands, // Label sumbu X (nama brand)
                    datasets: [
                        {
                            label: 'Total Terjual',
                            data: confirmedQuantities, // Data penjualan Total Terjual
                            backgroundColor: 'rgba(75, 192, 192, 0.2)', // Warna latar belakang
                            borderColor: 'rgba(75, 192, 192, 1)', // Warna garis
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            beginAtZero: true
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush
