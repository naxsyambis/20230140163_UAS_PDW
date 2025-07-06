<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Tambah Akun';
$activePage = 'akun';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $password, $role);
    $stmt->execute();

    header("Location: index.php");
    exit();
}

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Tambah Akun</h2>
    <form method="post">
        <div class="mb-3">
            <label class="block">Nama</label>
            <input type="text" name="nama" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Email</label>
            <input type="email" name="email" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Password</label>
            <input type="password" name="password" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Role</label>
            <select name="role" required class="border px-2 py-1 w-full">
                <option value="mahasiswa">Mahasiswa</option>
                <option value="asisten">Asisten</option>
            </select>
        </div>
        <button type="submit" class="bg-green-500 text-white px-3 py-2 rounded">Simpan</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
