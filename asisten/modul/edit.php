<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Edit Modul';
$activePage = 'modul';

$id = $_GET['id'];

// Ambil data modul
$result = $conn->query("SELECT * FROM modul WHERE id_modul=$id");
$modul = $result->fetch_assoc();

// Ambil semua praktikum (untuk select)
$praktikumList = $conn->query("SELECT * FROM praktikum");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_praktikum = $_POST['id_praktikum'];
    $judul = $_POST['judul'];

    // Cek apakah upload file baru
    if (!empty($_FILES['file_materi']['name'])) {
        $newFileName = time().'_'.basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../../uploads/" . $newFileName);

        // Hapus file lama jika ada
        if ($modul['file_materi']) {
            @unlink("../../uploads/" . $modul['file_materi']);
        }

        // Update termasuk file
        $stmt = $conn->prepare("UPDATE modul SET id_praktikum=?, judul=?, file_materi=? WHERE id_modul=?");
        $stmt->bind_param("sssi", $id_praktikum, $judul, $newFileName, $id);
    } else {
        // Update tanpa ganti file
        $stmt = $conn->prepare("UPDATE modul SET id_praktikum=?, judul=? WHERE id_modul=?");
        $stmt->bind_param("ssi", $id_praktikum, $judul, $id);
    }

    $stmt->execute();
    header("Location: index.php");
    exit();
}

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="block">Pilih Praktikum</label>
            <select name="id_praktikum" required class="border px-2 py-1 w-full">
                <?php while ($p = $praktikumList->fetch_assoc()): ?>
                    <option value="<?= $p['id_praktikum'] ?>" <?= ($p['id_praktikum'] == $modul['id_praktikum']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nama']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="block">Judul Modul</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($modul['judul']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">File Materi Sekarang</label>
            <?php if ($modul['file_materi']): ?>
                <a href="../../uploads/<?= htmlspecialchars($modul['file_materi']) ?>" target="_blank" class="text-blue-500 underline">Lihat File</a>
            <?php else: ?>-<?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="block">Ganti File Materi (kosongkan jika tidak ingin ganti)</label>
            <input type="file" name="file_materi" class="border px-2 py-1 w-full">
        </div>
        <button type="submit" class="bg-yellow-500 text-white px-3 py-2 rounded">Update</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
