<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Detail Laporan';
$activePage = 'laporan';

$id = $_GET['id'];
$stmt = $conn->prepare("
SELECT l.*, m.judul AS judul_modul, u.nama AS nama_mahasiswa
FROM laporan_mahasiswa l
JOIN modul m ON l.id_modul = m.id_modul
JOIN users u ON l.id = u.id
WHERE l.id_laporanma = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$laporan = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nilai = $_POST['nilai'];
    $feedback = $_POST['feedback'];

    $update = $conn->prepare("UPDATE laporan_mahasiswa SET Nilai=?, Feedback=? WHERE id_laporanma=?");
    $update->bind_param('isi', $nilai, $feedback, $id);
    $update->execute();

    header("Location: laporan.php");
    exit();
}

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-3">Detail Laporan</h2>
    <p><strong>Modul:</strong> <?= htmlspecialchars($laporan['judul_modul']) ?></p>
    <p><strong>Mahasiswa:</strong> <?= htmlspecialchars($laporan['nama_mahasiswa']) ?></p>
    <p><strong>Judul Laporan:</strong> <?= htmlspecialchars($laporan['Judul']) ?></p>
    <p><strong>File:</strong> 
        <?php if($laporan['File_laporan']): ?>
            <a href="../../uploads/<?= htmlspecialchars($laporan['File_laporan']) ?>" class="text-blue-500" download>Download</a>
        <?php else: ?>
            Tidak ada
        <?php endif; ?>
    </p>
    <p><strong>Nilai Saat Ini:</strong> <?= $laporan['Nilai'] !== null ? $laporan['Nilai'] : '-' ?></p>
    <p><strong>Feedback Saat Ini:</strong> <?= nl2br(htmlspecialchars($laporan['Feedback'])) ?></p>

    <form method="post" class="mt-4">
        <div class="mb-2">
            <label class="block">Nilai (angka)</label>
            <input type="number" name="nilai" value="<?= htmlspecialchars($laporan['Nilai']) ?>" class="border px-2 py-1">
        </div>
        <div class="mb-2">
            <label class="block">Feedback</label>
            <textarea name="feedback" class="border px-2 py-1 w-full"><?= htmlspecialchars($laporan['Feedback']) ?></textarea>
        </div>
        <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded">Simpan</button>
        <a href="laporan.php" class="ml-2 text-gray-600">Kembali</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
