<?php
session_start();
require '../../config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['Nama'];
    $deskripsi = $_POST['Deskripsi'];
    $semester = $_POST['Semester'];
    $tahun = $_POST['Tahun'];

    $stmt = $conn->prepare("UPDATE praktikum SET Nama=?, Deskripsi=?, Semester=?, Tahun_ajaran=? WHERE id_praktikum=?");
    $stmt->bind_param("ssssi", $nama, $deskripsi, $semester, $tahun, $id);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    }
}

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Edit Praktikum';
$activePage = 'praktikum';
require_once '../templates/header.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM praktikum WHERE id_praktikum=$id");
$praktikum = $result->fetch_assoc();
?>

<div class="bg-white p-6 rounded shadow">
    <form method="post">
        <div class="mb-3">
            <label class="block">Nama Praktikum</label>
            <input type="text" name="Nama" value="<?= htmlspecialchars($praktikum['Nama']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Deskripsi</label>
            <textarea name="Deskripsi" class="border px-2 py-1 w-full"><?= htmlspecialchars($praktikum['Deskripsi']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="block">Semester</label>
            <input type="text" name="Semester" value="<?= htmlspecialchars($praktikum['Semester']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Tahun Ajaran</label>
            <input type="text" name="Tahun" value="<?= htmlspecialchars($praktikum['Tahun_ajaran']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <button type="submit" class="bg-blue-500 text-white px-3 py-2 rounded">Update</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>

