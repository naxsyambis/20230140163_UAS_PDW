<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Manajemen Modul';
$activePage = 'modul';

// Ambil semua modul + nama praktikum
$result = $conn->query("
    SELECT modul.*, praktikum.Nama AS Nama_praktikum 
    FROM modul 
    JOIN praktikum ON modul.id_praktikum = praktikum.id_praktikum
    ORDER BY modul.id_modul DESC
");

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Daftar Modul</h2>
        <a href="tambah.php" class="bg-green-500 text-white px-3 py-2 rounded">+ Tambah Modul</a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 text-left border-b">#</th>
                    <th class="px-3 py-2 text-left border-b">Praktikum</th>
                    <th class="px-3 py-2 text-left border-b">Judul</th>
                    <th class="px-3 py-2 text-left border-b">Deskripsi</th>
                    <th class="px-3 py-2 text-left border-b">Tanggal Pertemuan</th>
                    <th class="px-3 py-2 text-left border-b">File Materi</th>
                    <th class="px-3 py-2 text-center border-b">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($modul = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 border-b"><?= $modul['id_modul'] ?></td>
                            <td class="px-3 py-2 border-b"><?= htmlspecialchars($modul['Nama_praktikum']) ?></td>
                            <td class="px-3 py-2 border-b"><?= htmlspecialchars($modul['Judul']) ?></td>
                            <td class="px-3 py-2 border-b"><?= nl2br(htmlspecialchars($modul['Deskripsi'])) ?></td>
                            <td class="px-3 py-2 border-b"><?= htmlspecialchars($modul['Tanggal_pertemuan']) ?></td>
                            <td class="px-3 py-2 border-b">
                                <?php if ($modul['File_materi']): ?>
                                    <a href="../../uploads/<?= htmlspecialchars($modul['File_materi']) ?>" target="_blank" class="text-blue-500 underline">Lihat</a>
                                <?php else: ?>-
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 border-b text-center">
                                <a href="edit.php?id=<?= $modul['id_modul'] ?>" class="text-yellow-500 hover:underline">Edit</a> |
                                <a href="hapus.php?id=<?= $modul['id_modul'] ?>" onclick="return confirm('Hapus modul ini?')" class="text-red-500 hover:underline">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-center text-gray-500">Belum ada modul yang ditambahkan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../templates/footer.php'; ?>
