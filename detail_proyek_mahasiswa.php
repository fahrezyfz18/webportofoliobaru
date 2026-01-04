<?php
session_start();
include "koneksi.php";

// Proteksi: Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: dashboard_mahasiswa.php");
    exit();
}

$id_proyek = mysqli_real_escape_string($koneksi, $_GET['id']);

// Query ambil data proyek beserta nama pemiliknya (join dengan tabel users)
$query = "SELECT projects.*, users.nama FROM projects 
          JOIN users ON projects.user_id = users.id 
          WHERE projects.id = '$id_proyek'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Proyek tidak ditemukan!";
    exit();
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Detail Proyek - <?php echo $data['judul']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f6f8fc; }
        .detail-card { background: #fff; border-radius: 15px; overflow: hidden; border: none; }
        .img-header { width: 100%; max-height: 400px; object-fit: cover; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <a href="dashboard_mahasiswa.php" class="btn btn-sm btn-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            
            <div class="card detail-card shadow-sm">
                <img src="uploads/<?php echo $data['gambar']; ?>" class="img-header" alt="Gambar Proyek">
                
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2 class="fw-bold"><?php echo $data['judul']; ?></h2>
                            <span class="badge bg-primary mb-3"><?php echo $data['jurusan']; ?></span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Diupload oleh:</small>
                            <span class="fw-semibold text-primary"><?php echo $data['nama']; ?></span>
                        </div>
                    </div>

                    <hr>

                    <h5><i class="bi bi-info-circle me-2"></i>Deskripsi</h5>
                    <p class="text-muted" style="white-space: pre-line;">
                        <?php echo $data['deskripsi']; ?>
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h5><i class="bi bi-calendar-event me-2"></i>Waktu Pelaksanaan</h5>
                            <p><?php echo date('d F Y - H:i', strtotime($data['waktu'])); ?> WIB</p>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-link-45deg me-2"></i>Link Proyek</h5>
                            <?php if(!empty($data['link_proyek'])): ?>
                                <a href="<?php echo $data['link_proyek']; ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    Lihat Demo / Repository
                                </a>
                            <?php else: ?>
                                <span class="text-muted small">Tidak ada link tersedia</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-light rounded border">
                        <h5><i class="bi bi-award me-2"></i>Penilaian Dosen</h5>
                        <p class="mb-0">
                            <strong>Status:</strong> 
                            <?php echo isset($data['nilai']) ? '<span class="text-success">Sudah Dinilai</span>' : '<span class="text-warning">Menunggu Penilaian</span>'; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>