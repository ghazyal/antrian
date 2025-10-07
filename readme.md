# 🧾 **Aplikasi Antrian**

Aplikasi Antrian berbasis **PHP Native** untuk mengelola sistem antrian secara digital. Cocok untuk pelayanan publik seperti **rumah sakit**, **kantor pemerintahan**, **bank**, dan sebagainya.

---

## ✨ Fitur Utama

- ✅ **Ambil Nomor Antrian Otomatis**
- 🧍‍💼 **Panel Admin Lengkap** (assign, next, finish, skip)
- 🖥️ **Tampilan Monitor Real-Time**
- 🌐 **Multi Loket**
- 📅 **Reset Harian Otomatis**

---

## 🧰 Teknologi yang Digunakan

- 🟦 PHP Native  
- 🟨 MySQL  
- 🟧 Bootstrap 5

---

## 🛠️ Instalasi Lokal

### 1️⃣ Clone Repository
```bash
git clone https://github.com/username/aplikasi-antrian.git
```

### 2️⃣ Buat Database
```bash
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Menunggu','Dilayani','Selesai','Lewati') NOT NULL DEFAULT 'Menunggu',
  `loket` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `called_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
);
```

### 3️⃣ Sesuaikan config.php
```bash
<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'queue_db';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
```

### 4️⃣ Jalankan Aplikasi
- 🧭 Admin Panel → http://localhost/antrian/src/admin.php
- 🖥️ Monitor → http://localhost/antrian/monitor.php

---

## 🌍 Hosting Online
Bisa diupload ke hosting PHP biasa. Cukup sesuaikan konfigurasi database dan import tabel.

## 📂 Struktur Folder
```bash
aplikasi-antrian/
├── admin/
│   └── admin.php
├── src/
│   └── update.php
├── monitor.php
├── config.php
└── README.md
```

## ❤️ Kontribusi
Silakan buat pull request atau issue jika ingin berkontribusi.
🇮🇩 Ditulis dalam bahasa Indonesia karena cinta tanah air (bukan pejabat korupsi 😎✊).

## 📄 Lisensi
Bebas digunakan untuk keperluan pribadi, instansi, maupun komersial. Mohon sertakan credit jika mengembangkan ulang.