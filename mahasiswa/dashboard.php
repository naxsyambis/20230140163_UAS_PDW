<?php
session_start();
require_once '../config.php';
if ($_SESSION['role'] != 'mahasiswa') { header("Location: ../login.php"); exit(); }

$pageTitle = 'Dashboard';
$activePage = 'dashboard';
$id_mahasiswa = $_SESSION['user_id'];

// Jumlah praktikum diikuti
$praktikum_diikuti = $conn->query("
    SELECT COUNT(DISTINCT m.id_praktikum) as total 
    FROM laporan_mahasiswa l
    JOIN modul m ON l.id_modul = m.id_modul
    WHERE l.id = $id_mahasiswa
")->fetch_assoc()['total'];

// Jumlah tugas selesai (Nilai IS NOT NULL)
$tugas_selesai = $conn->query("
    SELECT COUNT(*) as total 
    FROM laporan_mahasiswa
    WHERE id = $id_mahasiswa AND Nilai IS NOT NULL
")->fetch_assoc()['total'];

// Jumlah tugas menunggu (Nilai IS NULL)
$tugas_menunggu = $conn->query("
    SELECT COUNT(*) as total 
    FROM laporan_mahasiswa
    WHERE id = $id_mahasiswa AND Nilai IS NULL
")->fetch_assoc()['total'];

// Notifikasi terbaru: ambil 3 laporan terbaru mahasiswa ini
$notif = $conn->query("
    SELECT m.Judul AS judul_modul, l.Nilai, p.Nama AS nama_praktikum
    FROM laporan_mahasiswa l
    JOIN modul m ON l.id_modul = m.id_modul
    JOIN praktikum p ON m.id_praktikum = p.id_praktikum
    WHERE l.id = $id_mahasiswa
    ORDER BY l.created_at DESC
    LIMIT 3
");

require_once 'templates/header_mahasiswa.php';
?>

<div class="bg-gradient-to-r from-blue-500 to-cyan-400 text-white p-8 rounded-xl shadow-lg mb-8">
    <h1 class="text-3xl font-bold">Selamat Datang Kembali, <?= htmlspecialchars($_SESSION['nama']); ?>!</h1>
    <p class="mt-2 opacity-90">Terus semangat dalam menyelesaikan semua modul praktikummu.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-blue-600"><?= $praktikum_diikuti ?></div>
        <div class="mt-2 text-lg text-gray-600">Praktikum Diikuti</div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-green-500"><?= $tugas_selesai ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Selesai</div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center justify-center">
        <div class="text-5xl font-extrabold text-yellow-500"><?= $tugas_menunggu ?></div>
        <div class="mt-2 text-lg text-gray-600">Tugas Menunggu</div>
    </div>
</div>

<div class="bg-white p-6 rounded-xl shadow-md">
    <h3 class="text-2xl font-bold text-gray-800 mb-4">Notifikasi Terbaru</h3>
    <ul class="space-y-4">
        <?php while($n = $notif->fetch_assoc()): ?>
            <li class="flex items-start p-3 border-b border-gray-100 last:border-b-0">
                <span class="text-xl mr-4">🔔</span>
                <div>
                    <?php if($n['Nilai'] !== null): ?>
                        Nilai untuk <span class="font-semibold text-blue-600"><?= htmlspecialchars($n['judul_modul']) ?></span>
                        di praktikum <span class="font-semibold"><?= htmlspecialchars($n['nama_praktikum']) ?></span> telah diberikan:
                        <span class="font-semibold"><?= htmlspecialchars($n['Nilai']) ?></span>.
                    <?php else: ?>
                        Anda telah mengumpulkan laporan untuk <span class="font-semibold text-blue-600"><?= htmlspecialchars($n['judul_modul']) ?></span>
                        di praktikum <span class="font-semibold"><?= htmlspecialchars($n['nama_praktikum']) ?></span>. Menunggu penilaian.
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ul>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
