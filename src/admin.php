<?php
    require '../config.php'; 
    date_default_timezone_set('Asia/Jakarta');
    $today = date('Y-m-d');

    $result = $conn->query("SELECT * FROM queue WHERE date = '$today' AND status = 'Menunggu'");
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Antrian</title>
    <link href="assets/css/bootstrap-grid.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>Panel Admin - Antrian (<?php echo $today;?>)</h3>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Antrian Menunggu</h5>
                        <div id="waitingList"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Loket</h5>

                        <div class="mb-3">
                            <div><strong>Loket 1:</strong> <span id="loket1Serving">-</span></div>
                            <div class="mt-1">
                                <button class="btn btn-primary btn-sm" onclick="assignNext(1)">Panggil Next ke Loket 1</button>
                                <button class="btn btn-danger btn-sm" onclick="finish(1)">Selesai</button>
                                <button class="btn btn-warning btn-sm" onclick="skip(1)">Lewati</button>
                            </div>
                        </div>

                        <div>
                            <div><strong>Loket 2:</strong> <span id="loket2Serving">-</span></div>
                            <div class="mt-1">
                                <button class="btn btn-primary btn-sm" onclick="assignNext(2)">Panggil Next ke Loket 2</button>
                                <button class="btn btn-danger btn-sm" onclick="finish(2)">Selesai</button>
                                <button class="btn btn-warning btn-sm" onclick="skip(2)">Lewati</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5>Log Singkat</h5>
                        <div id="log"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function getBadgeClass(status) {
            switch (status) {
                case 'Menunggu': return 'secondary';
                case 'Dilayani': return 'primary';
                case 'Selesai': return 'success';
                case 'Lewati': return 'warning';
                default: return 'dark';
            }
        }

        async function fetchData() {
            try {
                const r = await fetch('update.php');
                const j = await r.json();

                const wl = document.getElementById('waitingList');
                wl.innerHTML = '';

                if (Array.isArray(j.waiting)) {
                    j.waiting.forEach(it => {
                        const div = document.createElement('div');
                        div.className = 'd-flex justify-content-between align-items-center mb-2 border rounded p-2 bg-white';
                        div.innerHTML = `
                            <div>#${it.number} <small class='text-muted'>${it.created_at}</small></div>
                            <div>
                                <button class='btn btn-sm btn-outline-primary' onclick='assign(${it.id},1)'>Ke Loket 1</button>
                                <button class='btn btn-sm btn-outline-success' onclick='assign(${it.id},2)'>Ke Loket 2</button>
                            </div>`;
                        wl.appendChild(div);
                    });
                }

                const loket1 = j.serving.find(s => s.loket == 1);
                const loket2 = j.serving.find(s => s.loket == 2);
                document.getElementById('loket1Serving').textContent = loket1 ? '#'+loket1.number : '-';
                document.getElementById('loket2Serving').textContent = loket2 ? '#'+loket2.number : '-';

                const logEl = document.getElementById('log');
                logEl.innerHTML = '';

                if (Array.isArray(j.log)) {
                    j.log.forEach(it => {
                        const d = document.createElement('div');
                        d.className = 'small mb-1';
                        const badgeClass = getBadgeClass(it.status);
                        d.innerHTML = `
                            <span class="text-muted">${it.called_at ?? '-'}</span> Antrian
                            #${it.number}
                            <span class="badge bg-${badgeClass}">${it.status ?? '-'}</span>`;
                        logEl.appendChild(d);
                    });
                }

            } catch (err) {
                console.error('FetchData error:', err);
            }
        }

        async function assign(id, loket) {
            await fetch('update.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({action:'assign', id, loket})
            });
            fetchData();
        }

        async function assignNext(loket) {
            await fetch('update.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({action:'assign_next', loket})
            });
            fetchData();
        }

        async function finish(loket) {
            await fetch('update.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({action:'finish', loket})
            });
            fetchData();
        }

        async function skip(loket) {
            await fetch('update.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({action:'skip', loket})
            });
            fetchData();
        }

        setInterval(fetchData, 2000);
        fetchData();
    </script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>