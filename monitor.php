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
    <link rel="stylesheet" href="assets/css/monitor.css">
</head>
<body>
<div class="d-flex justify-content-center align-items-center vh-100">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="loket-card">
                        <div class="loket-title">Loket 1</div>
                        <div class="small-text">Nomor Antrian</div>
                        <div id="l1" class="number">-</div>
                        <div class="small-text"><span id="l1next" class="next-number">-</span></div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="loket-card">
                        <div class="loket-title">Loket 2</div>
                        <div class="small-text">Nomor Antrian</div>
                        <div id="l2" class="number">-</div>
                        <div class="small-text"><span id="l2next" class="next-number">-</span></div>
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
                const monitorData = data.monitor || [];

                // Loket 1
                const l1Current = monitorData.find(item => item.loket == 1 && item.status === 'Dilayani');
                document.getElementById('l1').textContent = l1Current ? + l1Current.number : '-';

                const l1Next = monitorData.find(item => item.status === 'Menunggu' && item.number % 2 === 1);
                document.getElementById('l1next').textContent = l1Next ? 'Berikutnya: ' + l1Next.number : 'Berikutnya: -';

                // Loket 2
                const l2Current = monitorData.find(item => item.loket == 2 && item.status === 'Dilayani');
                document.getElementById('l2').textContent = l2Current ? + l2Current.number : '-';

                const l2Next = monitorData.find(item => item.status === 'Menunggu' && item.number % 2 === 0);
                document.getElementById('l2next').textContent = l2Next ? 'Berikutnya: ' + l2Next.number : 'Berikutnya: -';

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