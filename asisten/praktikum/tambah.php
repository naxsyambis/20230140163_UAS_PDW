<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Tambah Praktikum';
$activePage = 'praktikum';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $semester = $_POST['semester'];
    $tahun = $_POST['tahun'];

    $stmt = $conn->prepare("INSERT INTO praktikum (nama, deskripsi, semester, tahun_ajaran) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $deskripsi, $semester, $tahun);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}

require_once '../templates/header.php';

?>

<div class="bg-white p-6 rounded shadow">
    <form method="post">
        <div class="mb-3">
            <label class="block">Nama Praktikum</label>
            <input type="text" name="nama" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Deskripsi</label>
            <textarea name="deskripsi" class="border px-2 py-1 w-full"></textarea>
        </div>
        <div class="mb-3">
            <label class="block">Semester</label>
            <input type="text" name="semester" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Tahun Ajaran</label>
            <input type="text" name="tahun" required class="border px-2 py-1 w-full">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-3 py-2 rounded">Simpan</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
