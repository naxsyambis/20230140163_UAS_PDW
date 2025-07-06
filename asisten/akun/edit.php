<?php
session_start();
require_once '../../config.php';

if ($_SESSION['role'] != 'asisten') { header("Location: ../../login.php"); exit(); }

$pageTitle = 'Edit Akun';
$activePage = 'akun';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE id=$id");
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, password=?, role=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $email, $password, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $nama, $email, $role, $id);
    }
    $stmt->execute();

    header("Location: index.php");
    exit();
}

require_once '../templates/header.php';
?>

<div class="bg-white p-6 rounded shadow">
    <h2 class="text-xl font-bold mb-4">Edit Akun</h2>
    <form method="post">
        <div class="mb-3">
            <label class="block">Nama</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Password (kosongkan jika tidak ganti)</label>
            <input type="password" name="password" class="border px-2 py-1 w-full">
        </div>
        <div class="mb-3">
            <label class="block">Role</label>
            <select name="role" required class="border px-2 py-1 w-full">
                <option value="mahasiswa" <?= ($user['role']=='mahasiswa')?'selected':''; ?>>Mahasiswa</option>
                <option value="asisten" <?= ($user['role']=='asisten')?'selected':''; ?>>Asisten</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-3 py-2 rounded">Update</button>
        <a href="index.php" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>

<?php require_once '../templates/footer.php'; ?>
