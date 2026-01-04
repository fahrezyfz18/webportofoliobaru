<?php
session_start();
include 'koneksi.php';

// Proteksi akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $nilai      = intval($_POST['nilai']); // Pastikan angka
    $feedback   = $_POST['feedback'];
    $dosen_id   = $_SESSION['user_id'];

    // Validasi range nilai
    if ($nilai < 0 || $nilai > 100) {
        echo "<script>alert('Nilai harus di antara 0-100'); window.history.back();</script>";
        exit();
    }

    // Menggunakan Prepared Statement agar lebih aman
    $stmt = $koneksi->prepare("UPDATE projects SET nilai = ?, feedback = ?, dosen_id = ? WHERE id = ?");
    $stmt->bind_param("isii", $nilai, $feedback, $dosen_id, $project_id);

    if ($stmt->execute()) {
        echo "<script>
                alert('Penilaian berhasil disimpan!');
                window.location.href = 'penilaian_dosen.php';
              </script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    $stmt->close();
}
?>