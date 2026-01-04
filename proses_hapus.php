<?php
session_start();
include "koneksi.php";

// Pastikan mengecek 'user_id' sesuai dengan yang diset di login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_proyek = mysqli_real_escape_string($koneksi, $_GET['id']);
    $user_id_login = $_SESSION['user_id']; // Menggunakan 'user_id'

    // Ambil data gambar untuk dihapus dari folder uploads
    $query_cari = "SELECT gambar FROM projects WHERE id = '$id_proyek' AND user_id = '$user_id_login'";
    $result = mysqli_query($koneksi, $query_cari);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $path_file = "uploads/" . $data['gambar'];

        if (!empty($data['gambar']) && file_exists($path_file)) {
            unlink($path_file);
        }

        // Hapus data dari database
        $query_hapus = "DELETE FROM projects WHERE id = '$id_proyek' AND user_id = '$user_id_login'";
        mysqli_query($koneksi, $query_hapus);

        header("Location: dashboard_mahasiswa.php?status=success_delete");
        exit();
    }
}
?>