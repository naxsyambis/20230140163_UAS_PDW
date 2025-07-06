<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Manajemen Akun';
$activePage = 'akun';

// Ambil semua akun (mahasiswa dan asisten)
$akunList = $conn->query("SELECT * FROM users ORDER BY role ASC, nama ASC");

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <div class="flex justify-between mb-4">
        <h2 class="text-xl font-bold">Daftar Akun</h2>
        <a href="tambah.php" class="bg-green-500 text-white px-3 py-2 rounded">+ Tambah Akun</a>
    </div>

    <table class="w-full table-auto border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Nama</th>
                <th class="border px-2 py-1">Email</th>
                <th class="border px-2 py-1">Role</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($u = $akunList->fetch_assoc()): ?>
            <tr>
                <td class="border px-2 py-1"><?= $no++ ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($u['nama']) ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($u['email']) ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($u['role']) ?></td>
                <td class="border px-2 py-1">
                    <a href="edit.php?id=<?= $u['id'] ?>" class="text-blue-500">Edit</a> | 
                    <a href="hapus.php?id=<?= $u['id'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="text-red-500">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer.php'; ?>
