<?php
session_start();
require_once '../config.php';
if($_SESSION['role']!='mahasiswa'){ header("Location: ../login.php"); exit(); }

$id_praktikum = (int)$_GET['id'];
$id_mahasiswa = $_SESSION['user_id'];
$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';

// Ambil detail praktikum
$p = $conn->query("SELECT * FROM praktikum WHERE id_praktikum=$id_praktikum")->fetch_assoc();

// Ambil semua modul
$modulList = $conn->query("SELECT * FROM modul WHERE id_praktikum=$id_praktikum");

// Siapkan array untuk hasil laporan
$laporanList = [];
while($m = $modulList->fetch_assoc()){
    $id_modul = $m['id_modul'];
    // Ambil laporan mahasiswa untuk modul ini
    $laporan = $conn->query("SELECT * FROM laporan_mahasiswa WHERE id_modul=$id_modul AND id=$id_mahasiswa")->fetch_assoc();
    $laporanList[] = ['modul'=>$m, 'laporan'=>$laporan];
}
?>

<div class="max-w-6xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($p['Nama']) ?></h1>
    <p class="mb-6 text-gray-600"><?= htmlspecialchars($p['Deskripsi']) ?></p>

    <h2 class="text-xl font-semibold mb-4">Daftar Modul & Laporan Anda</h2>

    <div class="space-y-4">
        <?php foreach($laporanList as $item): ?>
        <?php $modul = $item['modul']; $laporan = $item['laporan']; ?>
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold"><?= htmlspecialchars($modul['Judul']) ?></h3>
                <a href="../uploads/<?= htmlspecialchars($modul['File_materi']) ?>" class="text-blue-500 text-sm hover:underline" download>📥 Unduh Materi</a>
            </div>

            <?php if($laporan): ?>
                <div class="text-sm text-gray-700 mt-2">
                    ✅ <span class="text-green-600">Sudah mengumpulkan laporan</span><br>
                    Nilai: <span class="font-semibold"><?= $laporan['Nilai'] !== null ? htmlspecialchars($laporan['Nilai']) : '-' ?></span><br>
                    Feedback: <span class="italic"><?= $laporan['Feedback'] ? htmlspecialchars($laporan['Feedback']) : '-' ?></span><br>
                    <a href="../uploads/<?= htmlspecialchars($laporan['File_laporan']) ?>" target="_blank" class="text-blue-500 hover:underline">📄 Lihat File Laporan</a>
                    <a href="hapus_laporan.php?id_laporanma=<?= $laporan['id_laporanma'] ?>&id_praktikum=<?= $id_praktikum ?>" 
                        onclick="return confirm('Yakin ingin menghapus laporan ini?');"
                        class="text-red-500 text-sm hover:underline ml-2">🗑 Hapus</a>

                </div>
            <?php else: ?>
                <form action="upload_laporan.php" method="post" enctype="multipart/form-data" class="mt-3 flex flex-col md:flex-row items-start md:items-center gap-2">
                    <input type="hidden" name="id_modul" value="<?= $modul['id_modul'] ?>">
                    <input type="text" name="judul" placeholder="Judul Laporan" required class="border px-2 py-1 rounded w-full md:w-auto">
                    <input type="file" name="file_laporan" required class="border px-2 py-1 rounded w-full md:w-auto">
                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">Upload</button>
                </form>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
