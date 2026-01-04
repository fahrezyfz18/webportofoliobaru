<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $jurusan = $_POST['jurusan'];
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $waktu = $_POST['waktu'];
    $link_proyek = mysqli_real_escape_string($koneksi, $_POST['link_proyek']);

    // Handle Image
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $nama_gambar = time() . "." . $ext;
    $target = "uploads/" . $nama_gambar;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $sql = "INSERT INTO projects (user_id, judul, jurusan, deskripsi, waktu, gambar, link_proyek) 
        VALUES ('$user_id', '$judul', '$jurusan', '$deskripsi', '$waktu', '$nama_gambar', '$link_proyek')";
        
        if (mysqli_query($koneksi, $sql)) {
            header("Location: dashboard_mahasiswa.php");
        }
    }
}
?>