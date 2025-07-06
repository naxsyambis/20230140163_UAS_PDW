<?php
// asisten/praktikum/index.php
session_start();
require_once '../../config.php';

// Cek login
if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Kelola Praktikum';
$activePage = 'praktikum';
require_once '../templates/header.php';

// Ambil data
$result = $conn->query("SELECT * FROM praktikum ORDER BY id_praktikum DESC");
?>

<div class="bg-white p-6 rounded shadow">
    <a href="tambah.php" class="bg-green-500 text-white px-3 py-2 rounded mb-4 inline-block">+ Tambah Praktikum</a>
        <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 mb-4 rounded">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="min-w-full border">
        <thead>
            <tr>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Semester</th>
                <th class="border px-2 py-1">Tahun</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while($p = $result->fetch_assoc()): ?>
            <tr>
                <td class="border px-2 py-1"><?= htmlspecialchars($p['Nama']) ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($p['Semester']) ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($p['Tahun_ajaran']) ?></td>
                <td class="border px-2 py-1">
                    <a href="edit.php?id=<?= $p['id_praktikum'] ?>" class="text-yellow-500">Edit</a> |
                    <a href="hapus.php?id=<?= $p['id_praktikum'] ?>" onclick="return confirm('Yakin?')" class="text-red-500">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer.php'; ?>

