<?php
require 'config.php';
date_default_timezone_set('Asia/Jakarta');
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Monitor Antrian</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #fff;
        }
        .card {
            background: transparent;
            border: none;
        }
        .number {
            font-size: 140px;
            font-weight: 800;
        }
        .small {
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card p-4 rounded-4">
                        <div class="small mb-4">Loket 1</div>
                        <p class="text-center">Nomor Antrian</p>
                        <div id="l1" class="number">-</div>
                        <div id="l1next" class="small text-muted">Berikutnya: -</div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card p-4 rounded-4">
                        <div class="small mb-4">Loket 2</div>
                        <p class="text-center">Nomor Antrian</p>
                        <div id="l2" class="number">-</div>
                        <div id="l2next" class="small text-muted">Berikutnya: -</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function load() {
            try {
                const response = await fetch('src/update.php');
                const data = await response.json();

                const serving = data.serving || [];
                const waiting = data.waiting || [];

                // === LOKET 1 ===
                const loket1 = serving.find(item => item.loket == 1);
                document.getElementById('l1').textContent = loket1 ? '#' + loket1.number : '-';

                // Ambil antrian berikutnya dari daftar waiting
                const next1 = waiting[0]; // yang paling pertama menunggu
                document.getElementById('l1next').textContent = next1 ? 'Berikutnya: #' + next1.number : 'Berikutnya: -';

                // === LOKET 2 ===
                const loket2 = serving.find(item => item.loket == 2);
                document.getElementById('l2').textContent = loket2 ? '#' + loket2.number : '-';

                const next2 = waiting[1]; // yang kedua menunggu, misal antrian ke-4 dst
                document.getElementById('l2next').textContent = next2 ? 'Berikutnya: #' + next2.number : 'Berikutnya: -';

            } catch (e) {
                console.error('Gagal memuat data antrian:', e);
            }
        }

        setInterval(load, 2000);
        load();
    </script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>