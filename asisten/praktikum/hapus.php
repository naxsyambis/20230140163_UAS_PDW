<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') {
    header("Location: ../../login.php");
    exit();
}

$id = $_GET['id'];

// Aktifkan exception supaya query error bisa ditangkap
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn->query("DELETE FROM praktikum WHERE id_praktikum=$id");
    $_SESSION['success'] = "Data berhasil dihapus.";
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Gagal menghapus! Pastikan tidak ada modul yang masih terkait dengan praktikum ini.";
}

// Redirect kembali ke index.php
header("Location: index.php");
exit();
?>
