<?php
session_start();
include "koneksi.php";

$error = ""; 

if (isset($_POST['register'])) {

    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nim_nidn = mysqli_real_escape_string($koneksi, $_POST['nim_nidn']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    // 1. Validasi Data Tidak Valid (Contoh: Input Kosong atau Password terlalu pendek)
    if (empty($nama) || empty($nim_nidn) || empty($role) || strlen($password) < 5) {
        $error = "<script>
                    alert('Registrasi Gagal! Data tidak valid. Pastikan semua kolom terisi dan password minimal 5 karakter.');
                    window.location.href = 'register.php';
                  </script>";
    } else {
        
        // 2. Cek apakah NIM/NIDN sudah digunakan
        $check = mysqli_query($koneksi, "SELECT * FROM users WHERE nim_nidn='$nim_nidn' LIMIT 1");

        if (mysqli_num_rows($check) > 0) {
            $error = "<script>
                        alert('Gagal Registrasi! NIM / NIDN sudah terdaftar.');
                        window.location.href = 'register.php';
                      </script>";
        } else {
            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // 3. Simpan user baru
            $sql = "INSERT INTO users (nama, nim_nidn, password, role)
                    VALUES ('$nama', '$nim_nidn', '$password_hash', '$role')";

            if (mysqli_query($koneksi, $sql)) {
                echo "<script>
                        alert('Data berhasil ditambahkan.');
                        window.location.href = 'login.php';
                      </script>";
                exit();
            } else {
                $error = "<script>
                            alert('Terjadi kesalahan sistem. Silakan coba lagi.');
                            window.location.href = 'register.php';
                          </script>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="stylelogreg.css">
  <title>Registrasi</title>
  <style>
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
  <h2>Registrasi</h2>

  <?php if(!empty($error)): ?>
      <?php echo $error; ?>
  <?php endif; ?>

  <form method="POST">
    <input type="text" name="nama" placeholder="Nama Lengkap" required>
    <input type="text" name="nim_nidn" placeholder="NIM / NIDN" required>

    <select name="role" required style="width: 100%; padding: 10px; margin-bottom: 15px; border-radius: 5px;">
      <option value="">-- Pilih Role --</option>
      <option value="mahasiswa">Mahasiswa</option>
      <option value="dosen">Dosen</option>
    </select>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="register">Daftar</button>

    <p>Sudah punya akun? <a href="login.php">Login</a></p>
  </form>
</div>
</div>
</section>
</body>
</html>
