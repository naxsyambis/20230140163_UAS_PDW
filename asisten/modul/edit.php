<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Edit Modul';
$activePage = 'modul';

$id = (int)$_GET['id'];

// Data lama
$modul = $conn->query("SELECT * FROM modul WHERE id_modul=$id")->fetch_assoc();

// Praktikum list
$praktikumList = $conn->query("SELECT * FROM praktikum");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_praktikum = $_POST['id_praktikum'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tanggal = $_POST['tanggal_pertemuan'];

    if (!empty($_FILES['file_materi']['name'])) {
        $newFile = time().'_'.basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../../uploads/" . $newFile);
        // hapus lama
        if ($modul['File_materi']) { @unlink("../../uploads/" . $modul['File_materi']); }

        // update + file
        $stmt = $conn->prepare("UPDATE modul SET id_praktikum=?, Judul=?, Deskripsi=?, File_materi=?, Tanggal_pertemuan=? WHERE id_modul=?");
        $stmt->bind_param("issssi", $id_praktikum, $judul, $deskripsi, $newFile, $tanggal, $id);
    } else {
        // update tanpa file
        $stmt = $conn->prepare("UPDATE modul SET id_praktikum=?, Judul=?, Deskripsi=?, Tanggal_pertemuan=? WHERE id_modul=?");
        $stmt->bind_param("isssi", $id_praktikum, $judul, $deskripsi, $tanggal, $id);
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
                    <option value="<?= $p['id_praktikum'] ?>" <?= $p['id_praktikum']==$modul['id_praktikum']?'selected':'' ?>>
                        <?= htmlspecialchars($p['Nama']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="block">Judul Modul</label>
            <input type="text" name="judul" value="<?= htmlspecialchars($modul['Judul']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Deskripsi Modul</label>
            <textarea name="deskripsi" class="border px-2 py-1 w-full"><?= htmlspecialchars($modul['Deskripsi']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="block">Tanggal Pertemuan</label>
            <input type="date" name="tanggal_pertemuan" value="<?= htmlspecialchars($modul['Tanggal_pertemuan']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">File Materi Sekarang</label>
            <?php if ($modul['File_materi']): ?>
                <a href="../../uploads/<?= htmlspecialchars($modul['File_materi']) ?>" target="_blank" class="text-blue-500 underline">Lihat File</a>
            <?php else: ?> - <?php endif; ?>
        </div>
        <div class="mb-3">
            <label class="block">Ganti File Materi (kosongkan jika tidak ingin ganti)</label>
            <input type="file" name="file_materi" class="border px-2 py-1 w-full" accept=".pdf,.docx">
        </div>
        <button type="submit" class="bg-yellow-500 text-white px-3 py-2 rounded">Update</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
