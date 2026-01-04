<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nim_nidn = mysqli_real_escape_string($koneksi, $_POST['nim_nidn']);
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    // Cek apakah NIM/NIDN sudah terdaftar
    $cek = mysqli_query($koneksi, "SELECT id FROM users WHERE nim_nidn = '$nim_nidn'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIM/NIDN sudah digunakan!'); window.location.href='admin_users.php';</script>";
    } else {
        $sql = "INSERT INTO users (nim_nidn, nama, password, role) VALUES ('$nim_nidn', '$nama', '$password', '$role')";
        if (mysqli_query($koneksi, $sql)) {
            header("Location: admin_users.php?status=sukses");
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
}
?>