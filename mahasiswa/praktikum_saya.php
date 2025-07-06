<?php
session_start();
require_once '../config.php';
if ($_SESSION['role'] != 'mahasiswa') { header("Location: ../login.php"); exit(); }

$id_mahasiswa = $_SESSION['user_id'];
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once 'templates/header_mahasiswa.php';

// Ambil praktikum yang diikuti mahasiswa + ringkasan nilai/feedback
$sql = "
SELECT p.*, 
    COUNT(DISTINCT l.id_laporanma) as total_laporan, 
    SUM(CASE WHEN l.Nilai IS NOT NULL THEN 1 ELSE 0 END) as laporan_dinilai,
    MAX(l.Feedback) as feedback_terbaru
FROM praktikum p
JOIN peserta_praktikum pp ON p.id_praktikum=pp.id_praktikum
LEFT JOIN modul m ON p.id_praktikum=m.id_praktikum
LEFT JOIN laporan_mahasiswa l ON l.id_modul=m.id_modul AND l.id=$id_mahasiswa
WHERE pp.id=$id_mahasiswa
GROUP BY p.id_praktikum
ORDER BY p.Tahun_ajaran DESC, p.Semester DESC
";
$list = $conn->query($sql);
?>

<div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Praktikum Saya</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while($p = $list->fetch_assoc()): ?>
        <div class="bg-white p-4 rounded shadow flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold"><?= htmlspecialchars($p['Nama']) ?></h2>
                <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($p['Deskripsi']) ?></p>
                <p class="text-xs text-gray-500">Semester: <?= htmlspecialchars($p['Semester']) ?>, Tahun: <?= htmlspecialchars($p['Tahun_ajaran']) ?></p>
            </div>
            <div class="mt-2 text-sm text-gray-700">
                <p>Tugas terkumpul: <?= $p['total_laporan'] ?> | Sudah dinilai: <?= $p['laporan_dinilai'] ?></p>
                <?php if($p['feedback_terbaru']): ?>
                    <p class="text-xs text-green-600 italic mt-1">Feedback terakhir: "<?= htmlspecialchars($p['feedback_terbaru']) ?>"</p>
                <?php endif; ?>
            </div>
            <a href="detail_praktikum.php?id=<?= $p['id_praktikum'] ?>" class="text-blue-500 mt-2">Lihat Detail</a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
