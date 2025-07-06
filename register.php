<?php
require_once 'config.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Validasi sederhana
    if (empty($nama) || empty($email) || empty($password) || empty($role)) {
        $message = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid!";
    } elseif (!in_array($role, ['mahasiswa', 'asisten'])) {
        $message = "Peran tidak valid!";
    } else {
        // Cek apakah email sudah terdaftar
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email sudah terdaftar. Silakan gunakan email lain.";
        } else {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Simpan ke database
            $sql_insert = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ssss", $nama, $email, $hashed_password, $role);

            if ($stmt_insert->execute()) {
                header("Location: login.php?status=registered");
                exit();
            } else {
                $message = "Terjadi kesalahan. Silakan coba lagi.";
            }
            $stmt_insert->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Registrasi Pengguna</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(to bottom right, #4F46E5, #3B82F6);
      display: flex; justify-content: center; align-items: center;
      height: 100vh; margin: 0;
    }
    .container {
      background-color: #fff;
      padding: 40px 30px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 380px;
    }
    h2 {
      text-align: center;
      color: #1E293B;
      margin-bottom: 25px;
      font-weight: 600;
    }
    .form-group { margin-bottom: 18px; }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      color: #334155;
      font-size: 14px;
    }
    .form-group input, .form-group select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #CBD5E1;
      border-radius: 6px;
      box-sizing: border-box;
      font-size: 14px;
      transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus {
      border-color: #3B82F6;
      outline: none;
    }
    .btn {
      background: #22C55E;
      color: #fff;
      padding: 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      font-weight: 500;
      transition: background 0.3s;
    }
    .btn:hover { background: #16A34A; }
    .message {
      text-align: center;
      margin-bottom: 15px;
      color: #DC2626;
      font-size: 14px;
    }
    .login-link {
      text-align: center;
      margin-top: 18px;
      font-size: 14px;
    }
    .login-link a {
      color: #3B82F6;
      text-decoration: none;
    }
    .login-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registrasi Pengguna</h2>
    <?php if (!empty($message)): ?>
      <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="register.php" method="post">
      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" required placeholder="Nama sesuai kampus">
      </div>
      <div class="form-group">
        <label for="email">Email Kampus</label>
        <input type="email" id="email" name="email" required placeholder="nama@kampus.ac.id">
      </div>
      <div class="form-group">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" required placeholder="••••••••">
      </div>
      <div class="form-group">
        <label for="role">Daftar Sebagai</label>
        <select id="role" name="role" required>
          <option value="mahasiswa">Mahasiswa</option>
          <option value="asisten">Asisten</option>
        </select>
      </div>
      <button type="submit" class="btn">Daftar</button>
    </form>
    <div class="login-link">
      Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
  </div>
</body>
</html>
