<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { 
    header("Location: ../../login.php"); 
    exit(); 
}

$id = (int)$_GET['id']; // pastikan aman

// Ambil file lama
$result = $conn->query("SELECT file_materi FROM modul WHERE id_modul=$id");
$modul = $result->fetch_assoc();

// Hapus file materi jika ada
if ($modul && $modul['file_materi']) {
    @unlink("../../uploads/" . $modul['file_materi']);
}

try {
    if ($conn->query("DELETE FROM modul WHERE id_modul=$id")) {
        // Sukses
        header("Location: index.php");
        exit();
    } else {
        // Query gagal tanpa exception (jarang terjadi)
        $error = $conn->error;
    }
} catch (mysqli_sql_exception $e) {
    // Tangkap exception
    $error = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Gagal Menghapus Modul</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-md max-w-lg text-center">
        <h1 class="text-2xl font-bold text-red-600 mb-4">❌ Gagal Menghapus Modul</h1>
        <p class="text-gray-700 mb-4">Modul ini tidak dapat dihapus karena masih memiliki laporan yang bergantung padanya.</p>
        <p class="text-xs text-gray-400 mb-6">Error Teknis: <?= htmlspecialchars($error) ?></p>
        <a href="index.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Kembali ke Daftar Modul</a>
    </div>
</body>
</html>
