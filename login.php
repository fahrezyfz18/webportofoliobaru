<?php
session_start();
include "koneksi.php";

$error = ""; 

if (isset($_POST['login'])) {
    $nim_nidn = mysqli_real_escape_string($koneksi, $_POST['nim_nidn']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $sql = "SELECT * FROM users WHERE nim_nidn='$nim_nidn' LIMIT 1";
    $result = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        $db_pass = $row['password'];

        // Cek password (mendukung hash maupun plain text untuk transisi)
        $password_valid = password_verify($password, $db_pass) || $password === $db_pass; 

        if ($password_valid) {
            $_SESSION['login']   = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama']    = $row['nama'];
            $_SESSION['role']    = $row['role'];

            // LOGIKA PENGALIHAN BERDASARKAN ROLE
            $redirect_page = "";
            if ($row['role'] == 'admin') {
                $redirect_page = 'dashboard_admin.php';
            } elseif ($row['role'] == 'dosen') {
                $redirect_page = 'dashboard_dosen.php';
            } else {
                $redirect_page = 'dashboard_mahasiswa.php';
            }

            echo "<script>
                    alert('Berhasil Login! Selamat datang, " . $_SESSION['nama'] . "');
                    window.location.href = '$redirect_page';
                  </script>";
            exit();
        } else {
            $error = "<script>alert('Password salah'); window.location.href = 'login.php';</script>";
        }
    } else {
        $error = "<script>alert('Akun tidak ditemukan'); window.location.href = 'login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="stylelogreg.css">
  <title>Login</title>
  <style>
      /* Tambahan style untuk alert error */
      .alert-error {
          background-color: #f8d7da;
          color: #721c24;
          padding: 10px;
          border-radius: 5px;
          border: 1px solid #f5c6cb;
          margin-bottom: 15px;
          font-size: 14px;
          text-align: center;
      }
  </style>
</head>
<body>
    <section class="hero" id="home">
    <div class="overlay"></div>
    <div class="glass">

<div class="container">
  <h2>Login</h2>

  <?php if(!empty($error)): ?>
          <?php echo $error; ?>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="nim_nidn" placeholder="Masukkan NIM / NIDN" required>
    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="login">Login</button>

    <p>Belum punya akun? <a href="register.php">Daftar</a></p>
  </form>
</div>
</div> </section> </body>
</html>
