<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Tambah Modul';
$activePage = 'modul';

// Ambil semua praktikum (untuk select)
$praktikumList = $conn->query("SELECT * FROM praktikum");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_praktikum = $_POST['id_praktikum'];
    $judul = $_POST['judul'];

    // Upload file materi
    $fileName = null;
    if (!empty($_FILES['file_materi']['name'])) {
        $fileName = time().'_'.basename($_FILES['file_materi']['name']);
        move_uploaded_file($_FILES['file_materi']['tmp_name'], "../../uploads/" . $fileName);
    }

    $stmt = $conn->prepare("INSERT INTO modul (id_praktikum, judul, file_materi) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_praktikum, $judul, $fileName);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <form action="tambah.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="block">Pilih Praktikum</label>
            <select name="id_praktikum" required class="border px-2 py-1 w-full">
                <?php while ($p = $praktikumList->fetch_assoc()): ?>
                    <option value="<?= $p['id_praktikum'] ?>"><?= htmlspecialchars($p['Nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="block">Judul Modul</label>
            <input type="text" name="judul" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Upload File Materi (PDF/DOCX)</label>
            <input type="file" name="file_materi" class="border px-2 py-1 w-full" accept=".pdf,.docx">
        </div>
        <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded">Simpan</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
