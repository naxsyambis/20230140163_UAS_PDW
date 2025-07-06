<?php
session_start();
require_once 'config.php';

// Jika sudah login, redirect ke halaman yang sesuai
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'asisten') {
        header("Location: asisten/dashboard.php");
    } elseif ($_SESSION['role'] == 'mahasiswa') {
        header("Location: mahasiswa/dashboard.php");
    }
    exit();
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password benar, simpan semua data penting ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                // ====== INI BAGIAN YANG DIUBAH ======
                // Logika untuk mengarahkan pengguna berdasarkan peran (role)
                if ($user['role'] == 'asisten') {
                    header("Location: asisten/dashboard.php");
                    exit();
                } elseif ($user['role'] == 'mahasiswa') {
                    header("Location: mahasiswa/dashboard.php");
                    exit();
                } else {
                    // Fallback jika peran tidak dikenali
                    $message = "Peran pengguna tidak valid.";
                }
                // ====== AKHIR DARI BAGIAN YANG DIUBAH ======

            } else {
                $message = "Password yang Anda masukkan salah.";
            }
        } else {
            $message = "Akun dengan email tersebut tidak ditemukan.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Login - Sistem Pengumpulan Tugas</title>
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
    .form-group input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #CBD5E1;
      border-radius: 6px;
      box-sizing: border-box;
      font-size: 14px;
      transition: border-color 0.2s;
    }
    .form-group input:focus {
      border-color: #3B82F6;
      outline: none;
    }
    .btn {
      background: #4F46E5;
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
    .btn:hover { background: #4338CA; }
    .message {
      text-align: center;
      margin-bottom: 15px;
      font-size: 14px;
    }
    .message.success { color: #16A34A; }
    .message.error { color: #DC2626; }
    .register-link {
      text-align: center;
      margin-top: 18px;
      font-size: 14px;
    }
    .register-link a {
      color: #3B82F6;
      text-decoration: none;
    }
    .register-link a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login Sistem</h2>
    <?php 
      if (isset($_GET['status']) && $_GET['status'] == 'registered') {
          echo '<p class="message success">Registrasi berhasil! Silakan login.</p>';
      }
      if (!empty($message)) {
          echo '<p class="message error">' . htmlspecialchars($message) . '</p>';
      }
    ?>
    <form action="login.php" method="post">
      <div class="form-group">
        <label for="email">Email Kampus</label>
        <input type="email" id="email" name="email" required placeholder="nama@kampus.ac.id">
      </div>
      <div class="form-group">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="btn">Masuk</button>
    </form>
    <div class="register-link">
      Belum punya akun? <a href="register.php">Daftar di sini</a>
    </div>
  </div>
</body>
</html>
