<?php
session_start();
require_once '../config.php';
$pageTitle = 'Katalog Praktikum';
$activePage = 'courses';  // sesuaikan sama header_mahasiswa.php
require_once 'templates/header_mahasiswa.php';

// Tangkap keyword pencarian (jika ada)
$keyword = $_GET['q'] ?? '';

// Query praktikum (filter jika ada keyword)
if($keyword){
    $stmt = $conn->prepare("SELECT * FROM praktikum WHERE Nama LIKE ?");
    $like = "%$keyword%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $list = $stmt->get_result();
} else {
    $list = $conn->query("SELECT * FROM praktikum");
}
?>

<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Katalog Praktikum</h1>

    <form method="get" class="mb-6">
        <input type="text" name="q" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari nama praktikum..."
            class="border px-3 py-2 rounded-md w-full md:w-1/3">
        <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-md">Cari</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php if($list->num_rows > 0): ?>
            <?php while($p = $list->fetch_assoc()): ?>
            <div class="bg-white p-4 rounded shadow">
                <h2 class="text-lg font-bold"><?= htmlspecialchars($p['Nama']) ?></h2>
                <p class="text-gray-600"><?= htmlspecialchars($p['Deskripsi']) ?></p>
                <p class="text-sm text-gray-500">Semester: <?= htmlspecialchars($p['Semester']) ?>, Tahun: <?= htmlspecialchars($p['Tahun_ajaran']) ?></p>
                <?php if(isset($_SESSION['role']) && $_SESSION['role']=='mahasiswa'): ?>
                    <a href="daftar.php?id=<?= $p['id_praktikum'] ?>" class="inline-block mt-2 bg-blue-500 text-white px-3 py-1 rounded">Daftar</a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-600">Praktikum tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'templates/footer_mahasiswa.php'; ?>
