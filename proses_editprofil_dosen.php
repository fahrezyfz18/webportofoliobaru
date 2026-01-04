<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = $_POST['password'];

    // Update Nama
    $sql = "UPDATE users SET nama='$nama' WHERE id='$user_id'";
    
    if (mysqli_query($koneksi, $sql)) {
        // Update Session agar nama di Topbar/Sidebar ikut berubah
        $_SESSION['nama'] = $nama;

        // Jika password diisi, update password juga
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE users SET password='$hashed_password' WHERE id='$user_id'");
        }

        header("Location: profil_dosen.php?status=sukses");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>