<?php
    require 'config.php';

    date_default_timezone_set('Asia/Jakarta');
    $today = date('Y-m-d');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil nomor antrian terakhir
        $stmt = $conn->prepare("SELECT MAX(number) AS maxn FROM queue WHERE date = ?");
        $stmt->bind_param("s", $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $next = ($row && $row['maxn']) ? $row['maxn'] + 1 : 1;

        // Insert antrian baru
        $stmt = $conn->prepare("INSERT INTO queue (`number`, `date`, `status`) VALUES (?, ?, 'Menunggu')");
        $stmt->bind_param("is", $next, $today);
        $stmt->execute();
        $id = $stmt->insert_id;

        $resp = ['id' => $id, 'number' => $next, 'date' => $today];
        header('Content-Type: application/json');
        echo json_encode($resp);
        exit;
    }

    // Hitung jumlah Menunggu hari ini
    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM queue WHERE date = ? AND status = 'Menunggu'");
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $Menunggu = $row['cnt'];
?>

<!doctype html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>Ambil Antrian</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h3>Ambil Nomor Antrian</h3>
                            <p class="text-muted">Tanggal: <?php echo $today; ?></p>
                            <p>Menunggu: <strong id="MenungguCount"><?php echo $Menunggu; ?></strong></p>
                            <button id="btnTake" class="btn btn-primary btn-lg">Ambil Antrian</button>
                            <hr>
                            <small class="text-muted">Setelah klik akan muncul popup cetak struk otomatis.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.getElementById('btnTake').addEventListener('click', async function(){
                this.disabled = true;
                const res = await fetch('index.php', {method:'POST'});
                const data = await res.json();
            
                const wc = document.getElementById('MenungguCount');
                wc.textContent = parseInt(wc.textContent || '0') + 1;

                const w = window.open('', '_blank', 'width=320,height=380');
                    w.document.write('<html><head><title>Tiket Antrian</title><meta name="viewport" content="width=device-width,initial-scale=1"></head><body style="font-family:monospace;text-align:center;padding:20px">');
                    w.document.write('<h2>Bank - Antrian</h2>');
                    w.document.write('<p>Tanggal: '+data.date+'</p>');
                    w.document.write('<div style="font-size:64px;font-weight:700;margin:18px 0">#'+data.number+'</div>');
                    w.document.write('<p>Terima kasih. Silakan tunggu panggilan.</p>');
                    w.document.write('</body></html>');
                    w.document.close();
                setTimeout(()=>{ w.print(); w.close(); }, 500);
                this.disabled = false;
            });
        </script>
        <script src="assets/js/bootstrap.min.js"></script>
    </body>
</html>