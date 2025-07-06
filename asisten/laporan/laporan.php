<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { 
    header("Location: ../../login.php"); 
    exit(); 
}

$pageTitle = 'Laporan Masuk';
$activePage = 'laporan';

// Ambil data filter dari form
$id_modul = $_GET['id_modul'] ?? '';
$id_mahasiswa = $_GET['id'] ?? ''; // sesuai kolom tabel memang namanya id
$status = $_GET['status'] ?? '';   // name form: status

// Ambil semua modul dan mahasiswa untuk pilihan filter
$modulList = $conn->query("SELECT * FROM modul");
$mahasiswaList = $conn->query("SELECT * FROM users WHERE role = 'mahasiswa'");

// Query laporan
$sql = "
SELECT l.*, m.judul AS judul_modul, u.nama AS nama_mahasiswa
FROM laporan_mahasiswa l
JOIN modul m ON l.id_modul = m.id_modul
JOIN users u ON l.id = u.id
WHERE (? = '' OR l.id_modul = ?)
  AND (? = '' OR l.id = ?)
  AND (
    (? = 'dinilai' AND l.nilai IS NOT NULL)
    OR (? = 'belum' AND l.nilai IS NULL)
    OR (? = '')
)
ORDER BY l.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('sisisss', $id_modul, $id_modul, $id_mahasiswa, $id_mahasiswa, $status, $status, $status);
$stmt->execute();
$laporanList = $stmt->get_result();

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <form method="get" class="mb-4 flex flex-wrap gap-2">
        <select name="id_modul" class="border px-2 py-1">
            <option value="">-- Filter Modul --</option>
            <?php while($m = $modulList->fetch_assoc()): ?>
                <option value="<?= $m['id_modul'] ?>" <?= ($id_modul == $m['id_modul']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m['Judul']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="id" class="border px-2 py-1">
            <option value="">-- Filter Mahasiswa --</option>
            <?php while($u = $mahasiswaList->fetch_assoc()): ?>
                <option value="<?= $u['id'] ?>" <?= ($id_mahasiswa == $u['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($u['nama']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <select name="status" class="border px-2 py-1">
            <option value="">-- Status --</option>
            <option value="dinilai" <?= ($status == 'dinilai') ? 'selected' : '' ?>>Sudah Dinilai</option>
            <option value="belum" <?= ($status == 'belum') ? 'selected' : '' ?>>Belum Dinilai</option>
        </select>

        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Filter</button>
    </form>

    <table class="w-full table-auto border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Modul</th>
                <th class="border px-2 py-1">Mahasiswa</th>

                <th class="border px-2 py-1">Nilai</th>
                <th class="border px-2 py-1">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; while($l = $laporanList->fetch_assoc()): ?>
            <tr>
                <td class="border px-2 py-1"><?= $no++ ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($l['judul_modul']) ?></td>
                <td class="border px-2 py-1"><?= htmlspecialchars($l['nama_mahasiswa']) ?></td>

                <td class="border px-2 py-1"><?= $l['Nilai'] !== null ? $l['Nilai'] : '-' ?></td>
                <td class="border px-2 py-1">
                    <a href="detail.php?id=<?= $l['id_laporanma'] ?>" class="text-blue-500">Detail</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once '../templates/footer.php'; ?>
