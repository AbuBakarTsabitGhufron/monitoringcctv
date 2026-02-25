@extends('layouts.user_type.auth')

@section('content')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <title>Dashboard CCTV</title>
    <link rel="stylesheet" href="{{ asset('assets/css/weather-card.css') }}">
    <style>
        #wilayahChart { 
            height: 400px !important;
            max-width: 100%;
        }
        /* ===== CARD BASE ===== */
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* ===== LAYOUT ===== */
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .weather-card {
            flex: 1 1 300px;
            min-width: 250px;
            max-width: 350px;
        }
        .card-container {
            flex: 2 1 400px;
            min-width: 250px;
            max-width: 100%;
        }
        .card-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            flex: 1 1 180px;
            min-width: 140px;
        }

        /* ===== CARD CONTENT ===== */
        .card-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .left-column h3, .left-column h5, .left-column h6 {
            margin: 0;
            color: white;
        }
        .left-column p {
            color: white;
            font-size: 24pt;
            margin: 5px 0 0 0;
        }
        .right-column {
            font-size: 2rem;
            color: #fff;
        }

        /* ===== HEADER ===== */
        .header {
            padding: 10px 20px;
        }
        .header h4 {
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #555;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 990px) {
            .statistik-section {
            margin-top: 725px !important;
            margin-bottom: 20px !important;
        }
            .dashboard-container {
                flex-direction: column;
            }
            .card-row {
                flex-direction: column;
            }
            .card {
                min-width: 100%;
            }
        }
        @media (max-width: 576px) {
            .left-column p {
                font-size: 18pt;
            }
            .right-column {
                font-size: 1.5rem;
            }
        }
    </style>

</head>

<header class="header">
    <h4 style="text-align: left; margin-left: 20px;">Selamat Datang, Admin</h4>
    <p style="text-align: left; margin-left: 20px; margin-bottom: -15px;">Semoga kebaikan selalu menyertaimu</p>
</header>

<div class="container">
    <div class="dashboard-container">
        <!-- Weather -->
        <div class="weather-card">
            <img class="image-card" src="{{ asset('images/people.svg') }}" alt="">
            <div class="weather-info d-flex" id="weatherInfo" style="margin-top: 10px;">
                <div class="weather-details">
                    <h4 class="location font-weight-normal" id="location">Loading...</h4>
                    <h5 class="country font-weight-normal" id="country">Indonesia</h5>
                </div>
                <div class="temperature-info d-flex justify-content-end" style="margin-left: 125px;">
                    <h2 class="mb-0 font-weight-normal" id="temperature">
                        <i id="weatherIcon" class="mdi" style="font-size: 30px;"></i>
                        <span id="tempValue"></span>
                    </h2>
                </div>
            </div>
        </div>

        <!-- Card section -->
        <div class="card-container">
            <div class="card-row">
                <div class="card" style="background-color:#7da0fa; cursor: pointer;">
                    <a href="{{ route('rekapan.users') }}" style="text-decoration: none;">
                        <div class="card-content">
                            <div class="left-column">
                                <h3 style="color: white;">Users</h3>
                                <p style="color: white; font-size: 24pt;" class="fs-30 mb-2">{{ $userCount }}</p>
                            </div>
                            <div class="right-column">
                                <i class="fas fa-users icon-card"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="card" style="background-color: rgba(71,71,161,255); cursor: pointer;">
                    <a href="{{ route('rekapan.cctv.panorama') }}" style="text-decoration: none;">
                        <div class="card-content">
                            <div class="left-column">
                                <h6 style="color: white; font-size: 13pt;">CCTV Panorama</h6>
                                <p style="color: white; font-size: 24pt;" class="fs-30 mb-2">{{ $panoramaCount }}</p>
                            </div>
                            <div class="right-column">
                                <i class="fas fa-earth-americas icon-card"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="card-row">
                <div class="card" style="background-color:#7978e9; cursor: pointer;">
                    <a href="{{ route('rekapan.cctv.lokasi') }}" style="text-decoration: none;">
                        <div class="card-content">
                            <div class="left-column">
                                <h5 style="color: white; font-size: 13pt;">CCTV Lokasi</h5>
                                <p style="color: white; font-size: 24pt;" class="fs-30 mb-2">{{ $lokasiCount }}</p>
                            </div>
                            <div class="right-column">
                                <i class="fas fa-book-open icon-card"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="card" style="background-color: #f3797e; cursor: pointer;">
                    <a href="{{ route('rekapan.detaillokasi') }}" style="text-decoration: none;">
                        <div class="card-content">
                            <div class="left-column">
                                <h3 style="color: white;">Lokasi</h3>
                                <p style="color: white; font-size: 24pt;" class="fs-30 mb-2">
                                    {{ $jumlahCCTVPerLokasi->count() }}
                                </p>
                            </div>
                            <div class="right-column">
                                <i class="fas fa-book-open icon-card"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistik -->
<div style="padding: 20px 15px; max-width: 1140px; margin: 0 auto;">
    <div class="statistik-section" style="margin-top: -70px; margin-bottom: 40px;">
        <h4>Statistik CCTV</h4>
        <div style="margin-top: 20px;">
            <h5>Grafik Jumlah Lokasi dan CCTV per Wilayah</h5>
            <canvas id="wilayahChart" style="max-width: 100%; height: 400px;"></canvas>
        </div>
        <div style="margin-top: 40px;">
            <h5>Grafik Jumlah CCTV per Lokasi</h5>
            <canvas id="lokasiChart" style="max-width: 100%; height: 300px;"></canvas>
        </div>
    </div>
</div>

<script>
    const lokasiPerWilayah = @json($jumlahLokasiPerWilayah);
    const cctvPerWilayah = @json($jumlahCCTVPerWilayah);
    const cctvPerLokasi = @json($jumlahCCTVPerLokasi);

    const wilayahLabels = [...new Set([
        ...lokasiPerWilayah.map(d => d.namaWilayah),
        ...cctvPerWilayah.map(d => d.namaWilayah)
    ])];

    const lokasiData = wilayahLabels.map(w => {
        const match = lokasiPerWilayah.find(d => d.namaWilayah === w);
        return match ? match.total_lokasi : 0;
    });

    const cctvWilayahData = wilayahLabels.map(w => {
        const match = cctvPerWilayah.find(d => d.namaWilayah === w);
        return match ? match.total_cctv : 0;
    });

    new Chart(document.getElementById('wilayahChart'), {
        type: 'bar',
        data: {
            labels: wilayahLabels,
            datasets: [{
                label: 'Jumlah Lokasi',
                data: lokasiData,
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }, {
                label: 'Jumlah CCTV',
                data: cctvWilayahData,
                backgroundColor: 'rgba(255, 99, 132, 0.7)'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: {
                    display: true,
                    text: 'Statistik Lokasi & CCTV per Wilayah'
                }
            }
        }
    });

    const lokasiByWilayah = {};
    cctvPerLokasi.forEach(item => {
        if (!lokasiByWilayah[item.namaWilayah]) {
            lokasiByWilayah[item.namaWilayah] = [];
        }
        lokasiByWilayah[item.namaWilayah].push(item);
    });

    const lokasiLabels = [];
    const cctvLokasiData = [];
    const backgroundColors = [];

    Object.entries(lokasiByWilayah).forEach(([wilayah, lokasiList], wilayahIndex) => {
        lokasiList.forEach((item, lokasiIndex) => {
            lokasiLabels.push(item.namaLokasi);
            cctvLokasiData.push(item.total_cctv);
            const hue = (wilayahIndex * 60) % 360;
            const lightness = 50 + (lokasiIndex * 10) % 30;
            backgroundColors.push(`hsl(${hue}, 70%, ${lightness}%)`);
        });
    });

    new Chart(document.getElementById('lokasiChart'), {
        type: 'pie',
        data: {
            labels: lokasiLabels,
            datasets: [{
                label: 'Jumlah CCTV',
                data: cctvLokasiData,
                backgroundColor: backgroundColors
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                title: {
                    display: true,
                    text: 'Jumlah CCTV per Lokasi'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed} CCTV`;
                        }
                    }
                }
            }
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const apiKey = '66db882578a46f03c27c30a952240556';
        const lat = -7.797068;
        const lon = 110.370529;

        const iconMap = {
            '01d': 'mdi-weather-sunny',
            '01n': 'mdi-weather-night',
            '02d': 'mdi-weather-partlycloudy',
            '02n': 'mdi-weather-night',
            '03d': 'mdi-weather-cloudy',
            '03n': 'mdi-weather-cloudy',
            '04d': 'mdi-weather-cloudy',
            '04n': 'mdi-weather-cloudy',
            '09d': 'mdi-weather-pouring',
            '09n': 'mdi-weather-pouring',
            '10d': 'mdi-weather-rainy',
            '10n': 'mdi-weather-rainy',
            '11d': 'mdi-weather-lightning',
            '11n': 'mdi-weather-lightning',
            '13d': 'mdi-weather-snowy',
            '13n': 'mdi-weather-snowy',
            '50d': 'mdi-weather-fog',
            '50n': 'mdi-weather-fog',
        };

        fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`)
            .then(response => response.json())
            .then(data => {
                const temperature = Math.round(data.main.temp);
                const location = data.name;
                const iconCode = data.weather[0].icon;
                const mdiClass = iconMap[iconCode] || 'mdi-weather-cloudy';

                document.getElementById('weatherIcon').className = 'mdi mr-2 ' + mdiClass;
                document.getElementById('tempValue').innerHTML = `${temperature}<sup>°C</sup>`;
                document.getElementById('location').textContent = location;
                document.getElementById('country').textContent = 'Indonesia';
            })
            .catch(error => {
                console.error('Error fetching weather data:', error);
            });
    });
</script>

@endsection
