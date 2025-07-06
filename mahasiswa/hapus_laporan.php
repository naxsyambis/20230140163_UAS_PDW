<?php
session_start();
require_once '../config.php';

// Pastikan hanya mahasiswa
if ($_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Ambil id_laporanm dari GET
$id_laporanma = (int)$_GET['id_laporanma'];
$id_mahasiswa = $_SESSION['user_id'];

// Cari data laporan
$result = $conn->query("SELECT * FROM laporan_mahasiswa WHERE id_laporanma=$id_laporanma AND id=$id_mahasiswa");
$laporan = $result->fetch_assoc();

if ($laporan) {
    // Hapus file jika ada
    if ($laporan['File_laporan']) {
        @unlink("../uploads/" . $laporan['File_laporan']);
    }
    
    // Hapus data dari database
    $conn->query("DELETE FROM laporan_mahasiswa WHERE id_laporanma=$id_laporanma AND id=$id_mahasiswa");
}

// Kembali ke halaman sebelumnya
header("Location: detail_praktikum.php?id=" . (int)$_GET['id_praktikum']);
exit();
?>
