<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah CCTV Lokasi</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .cctv-card {
            left: 1000px;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
            color: black;
            margin-bottom: 25px;
            margin-top: -3px
        }

        .form-group {
            margin-bottom: 15px;
            max-width: 380px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: rgb(26, 255, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 5px;
        }

        .btn:hover {
            background-color: rgb(19, 186, 0);
        }

        .warning {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .background-image {
            position: absolute;
            /* Menghilangkan elemen dari flow dokumen */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            z-index: -1;
            /* Pastikan gambar berada di belakang konten lain */
        }

        .background-image::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 110%;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), rgba(255, 255, 255, 1));
            transform: rotate(180deg);
        }

        .form-control {
            width: 101%;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>TAMBAH CCTV LOKASI</h2>
        <div class="card-form">
            <form id="cctvForm" action="{{ route('lokasi.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="namaWilayah">Nama Wilayah</label>
                    <select class="form-control" id="namaWilayah" name="namaWilayah" style="width: 402px;" required>
                        <option value="">Pilih Wilayah</option>
                        <option value="KABUPATEN BANTUL">KABUPATEN BANTUL</option>
                        <option value="KOTA YOGYAKARTA">KOTA YOGYAKARTA</option>
                        <option value="KABUPATEN SLEMAN">KABUPATEN SLEMAN</option>
                        <option value="KABUPATEN KULON PROGO">KABUPATEN KULON PROGO</option>
                        <option value="KABUPATEN GUNUNG KIDUL">KABUPATEN GUNUNG KIDUL</option>
                        <option value="ATCS KOTA">ATCS KOTA</option>
                        <option value="ATCS DIY">ATCS DIY</option>
                        <option value="MALIOBORO">MALIOBORO</option>
                        <option value="PANORAMA">PANORAMA</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="namaLokasi">Nama Lokasi</label>
                    <input type="text" class="form-control" id="namaLokasi" name="namaLokasi" required>
                </div>
                <div class="form-group">
                    <label for="namaTitik">Nama Titik</label>
                    <input type="text" class="form-control" id="namaTitik" name="namaTitik">
                </div>
                <div class="form-group">
                    <label for="link">Link</label>
                    <input type="text" class="form-control" id="link" name="link">
                    <!-- <p class="warning" id="warningLink">⚠ Link sudah digunakan!</p> -->
                </div>
                <button type="submit" class="btn" id="submitBtn">TAMBAH</button>
            </form>
        </div>
    </div>
    <div class="background-image" style="background-image: url('{{ asset('images/pattern.jpg') }}');"></div>

    <!-- <script>
        $(document).ready(function () {
            // Fungsi untuk memeriksa duplikasi link
            function checkDuplicateLink(value) {
                if (value.trim() === "") {
                    $("#warningLink").hide();
                    $("#submitBtn").prop("disabled", false);
                    return;
                }

                $.ajax({
                    url: "{{ route('lokasi.checkDuplicate') }}",
                    type: "GET",
                    data: { field: "link", value: value },
                    success: function (response) {
                        if (response.exists) {
                            $("#warningLink").show();
                            $("#submitBtn").prop("disabled", true);
                        } else {
                            $("#warningLink").hide();
                            $("#submitBtn").prop("disabled", false);
                        }
                    },
                    error: function () {
                        console.log("Gagal memeriksa duplikasi.");
                    }
                });
            }

            $("#link").on("input", function () {
                checkDuplicateLink($(this).val());
            });
        });
    </script> -->
</body>

</html>