<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_profil'])) {
    $user_id = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $password_baru = $_POST['password_baru'];

    if (!empty($password_baru)) {
        $password_hashed = password_hash($password_baru, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET nama = '$nama', password = '$password_hashed' WHERE id = '$user_id'";
    } else {
        $sql = "UPDATE users SET nama = '$nama' WHERE id = '$user_id'";
    }

    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['nama'] = $nama;

        // PASTIKAN NAMA FILE DI SINI ADALAH profil.php
        echo "<script>
                alert('Profil berhasil diperbarui!');
                window.location.href = 'profil_mahasiswa.php'; 
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    // JIKA DIAKSES LANGSUNG, KEMBALIKAN KE profil.php
    header("Location: profil_mahasiswa.php");
    exit();
}
?>