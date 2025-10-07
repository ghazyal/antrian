<?php
require '../config.php';
date_default_timezone_set('Asia/Jakarta');
$today = date('Y-m-d');

function jsonResponse($data){
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// ======================
// GET DATA UNTUK DASHBOARD DAN MONITOR
// ======================
if ($method === 'GET') {
    // Ambil daftar yang Menunggu
    $waitingResult = $conn->query("SELECT id, number, created_at, loket, status FROM queue WHERE date = '$today' AND status = 'Menunggu' ORDER BY id ASC");
    $waiting = [];
    while ($row = $waitingResult->fetch_assoc()) {
        $waiting[] = $row;
    }

    // Ambil yang Dilayani (per loket)
    $servingResult = $conn->query("SELECT id, number, loket, status FROM queue WHERE date = '$today' AND status = 'Dilayani'");
    $serving = [];
    while ($row = $servingResult->fetch_assoc()) {
        $serving[] = $row;
    }

    // Ambil log 10 terakhir (semua status)
    $logResult = $conn->query("SELECT number, loket, status, called_at FROM queue WHERE date = '$today' ORDER BY id DESC LIMIT 10");
    $log = [];
    while ($row = $logResult->fetch_assoc()) {
        $log[] = $row;
    }

    // Gabungkan Menunggu dan Dilayani untuk monitor
    $merged = array_merge($serving, $waiting);

    jsonResponse([
        'waiting' => $waiting,
        'serving' => $serving,
        'log'     => $log,
        'monitor' => $merged
    ]);
}

// ======================
// Helper: ambil antrian berikutnya (status Menunggu)
// ======================
function getNextWaiting($conn, $today) {
    $result = $conn->query("SELECT * FROM queue WHERE date = '$today' AND status = 'Menunggu' ORDER BY id ASC LIMIT 1");
    return $result->fetch_assoc();
}

// ======================
// POST ACTIONS
// ======================
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST' && $input) {
    $action = $input['action'] ?? null;

    // ======================
    // Assign next ke loket
    // ======================
    if ($action === 'assign_next') {
        $loket = (int)$input['loket'];

        // Cek apakah loket masih melayani antrean
        $check = $conn->query("SELECT id FROM queue WHERE date='$today' AND status='Dilayani' AND loket=$loket LIMIT 1");
        if ($check->num_rows > 0) {
            jsonResponse(['ok'=>false,'msg'=>'Loket masih melayani, selesaikan dulu']);
        }

        // Ambil antrean berikutnya
        $next = getNextWaiting($conn, $today);
        if (!$next) jsonResponse(['ok'=>false,'msg'=>'Tidak ada antrian Menunggu']);

        // Update antrean menjadi Dilayani untuk loket ini
        $conn->query("UPDATE queue SET status='Dilayani', loket=$loket, called_at=NOW() WHERE id=".$next['id']);
        jsonResponse(['ok'=>true, 'assigned'=>$next]);
    }

    // ======================
    // Assign manual
    // ======================
    if ($action === 'assign') {
        $id = (int)$input['id'];
        $loket = (int)$input['loket'];

        // Cek apakah loket masih melayani
        $check = $conn->query("SELECT id FROM queue WHERE date='$today' AND status='Dilayani' AND loket=$loket LIMIT 1");
        if ($check->num_rows > 0) {
            jsonResponse(['ok'=>false,'msg'=>'Loket masih melayani, selesaikan dulu']);
        }

        $conn->query("UPDATE queue SET status='Dilayani', loket=$loket, called_at=NOW() WHERE id=$id");
        jsonResponse(['ok'=>true]);
    }

    // ======================
    // Tandai Selesai
    // ======================
    if ($action === 'finish') {
        $loket = (int)$input['loket'];
        $result = $conn->query("SELECT id FROM queue WHERE date='$today' AND status='Dilayani' AND loket=$loket ORDER BY called_at DESC LIMIT 1");
        $row = $result->fetch_assoc();
        if (!$row) jsonResponse(['ok'=>false,'msg'=>'Tidak ada yang sedang Dilayani']);
        $conn->query("UPDATE queue SET status='Selesai' WHERE id=".$row['id']);
        jsonResponse(['ok'=>true]);
    }

    // ======================
    // Lewati antrian
    // ======================
    if ($action === 'skip') {
        $loket = (int)$input['loket'];
        $result = $conn->query("SELECT id FROM queue WHERE date='$today' AND status='Dilayani' AND loket=$loket ORDER BY called_at DESC LIMIT 1");
        $row = $result->fetch_assoc();
        if (!$row) jsonResponse(['ok'=>false,'msg'=>'Tidak ada yang sedang Dilayani']);
        $conn->query("UPDATE queue SET status='Lewati' WHERE id=".$row['id']);
        jsonResponse(['ok'=>true]);
    }
}

jsonResponse(['ok'=>false,'msg'=>'Invalid request']);