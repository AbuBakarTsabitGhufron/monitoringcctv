@extends('layouts.user_type.auth')

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('lifemedia_logo.png') }}?v={{ time() }}" type="image/png">
    <title>Dashboard CCTV</title> <!--  ini judul -->

    <script>
        function toggleDaerah(id) {
            const daerahList = document.getElementById(id);
            daerahList.style.display = (daerahList.style.display === "none" || daerahList.style.display === "") ? "block" : "none";
        }

        function toggleCCTV(id, checkbox) {
            const cctvContainer = document.getElementById(id);
            const iframe = cctvContainer.querySelector("iframe");

            if (checkbox.checked) {
                cctvContainer.style.display = "block";
                iframe.src = iframe.getAttribute("data-src"); // Load link hanya jika checkbox dicentang
            } else {
                cctvContainer.style.display = "none";
                iframe.src = ""; // Hapus src untuk menghemat bandwidth
            }
        }

        window.onload = function () {
            const checkboxes = document.querySelectorAll('.form-check-input');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    toggleCCTV(checkbox.id.replace('checkbox-', ''), checkbox);
                }
            });
        };
    </script>

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
        }

        .container-fluid {
            width: 100%;
            margin: 0;
        }

        /* Sidebar Styling */
        .sidebar {
            border-radius: 10px;
            background-color: #ffffff;
            overflow-y: auto;
            max-height: 100vh;
            width: 300px;
            border-right: 2px solid #ddd;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            scrollbar-width: none;
            padding: 10px;
            float: left;
        }

        .sidebar .list-group-item {
            font-size: 16px;
            font-weight: 600;
            padding: 15px;
            cursor: pointer;
            background: #f8f9fa;
            border: none;
            transition: all 0.3s ease;
            border-radius: 8px;
            display: block;
            /* Mengisi lebar sidebar */
            margin: 5px;
            overflow-y: auto;
            text-align: left;
            text-underline-offset: 100px;
            scrollbar-width: none;
        }

        .sidebar .list-group-item:hover {
            background-color: #007bff;
            color: white;
        }

        /* Daftar CCTV Styling */
        .sidebar .form-check {
            margin-left: 15px;
            padding: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .sidebar .form-check input {
            transform: scale(1.3);
            cursor: pointer;
            margin-right: 10px;
            margin-left: -5px;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .sidebar .form-check label {
            cursor: pointer;
            font-weight: 500;
            color: #555;
        }

        .sidebar .form-check label:hover {
            color: #007bff;
        }

        /* Animasi Toggle */
        .wilayah-container {
            display: none;
            padding-left: 10px;
            transition: all 0.3s ease-in-out;
            overflow-y: auto;
            scrollbar-width: none;
        }

        .content {
            padding: 15px;
        }

        .content .row {
            margin: 0;
            display: flex;
            flex-wrap: wrap;
        }

        .card {
            background-color: #ffffff;
            padding: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            border-radius: 10px;
            margin-top: -25px;
        }

        .card-title {
            font-size: 14px;
        }

        .wilayah-name {
            font-size: 16px;
            margin-bottom: 10px;
            display: none;
        }

        .cctv-view {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 10px;
        }

        .iframe-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
        }

        .iframe-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .iframe-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .btn-play {
            fill: #fff;
        }

        .card0 {
            background-color: #ffffff;
            padding: 15px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-radius: 10px;
            margin: 15px 0px 15px 0px;
        }

        .card0 h1 {
            font-size: 24px;
            font-weight: bold;
            color: #343a40;
            margin-bottom: 10px;
        }

        .card0 p {
            font-size: 16px;
            color: #666;
        }
    </style>
</head>

@php
    $groupedLokasi = $lokasi->groupBy('namaWilayah');
@endphp



<div class="container-fluid">
    <div class="row">

        <div class="card0">
            <h1 style="text-align: center;">Dashboard CCTV DIY</h1>
            <p style="text-align: center;">Memantau kondisi lalu lintas di berbagai titik kota secara real-time</p>
        </div>

        <!-- Sidebar (Kiri) -->
        <div class="col-md-3 sidebar">
            <div class="list-group">
                @foreach($groupedLokasi as $wilayah => $lokasiGroup)
                    <a href="javascript:void(0);" class="list-group-item list-group-item-action"
                        onclick="toggleDaerah('{{ Str::slug($wilayah) }}', this)">
                        {{ $wilayah }}
                    </a>
                    <div id="{{ Str::slug($wilayah) }}" class="wilayah-container">
                        @foreach($lokasiGroup as $cctv)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="checkbox-{{ Str::slug($cctv->namaTitik) }}"
                                    onclick="toggleCCTV('{{ Str::slug($cctv->namaTitik) }}', this)">
                                <label class="form-check-label" for="checkbox-{{ Str::slug($cctv->namaTitik) }}">
                                    {{ $cctv->namaTitik }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endforeach

            </div>
        </div>

        <!-- Main Content (Kanan) -->
        <div class="col-md-9 content">
            <div class="row">
                @foreach($groupedLokasi as $wilayah => $lokasiGroup)
                    @foreach($lokasiGroup as $cctv)
                        <div class="col-md-4 cctv-view" id="{{ Str::slug($cctv->namaTitik) }}" style="display: none;">
                            <div class="card">
                                <h5 class="card-title text-center mb-3">
                                    {{ $cctv->namaLokasi }}<br>
                                    {{ $cctv->namaTitik }}
                                </h5>
                                <div class="iframe-container">
                                    <iframe data-src="{{ $cctv->link }}" frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach

            </div>
        </div>
    </div>
</div>
