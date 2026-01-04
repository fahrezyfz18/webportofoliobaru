<?php
include 'koneksi.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Query Join untuk ambil data proyek dan pemiliknya
$query = mysqli_query($koneksi, "SELECT projects.*, users.nama, users.nim_nidn 
                                FROM projects 
                                JOIN users ON projects.user_id = users.id 
                                WHERE projects.id = '$id'");
$d = mysqli_fetch_assoc($query);

if (!$d) {
    echo "<script>alert('Proyek tidak ditemukan!'); window.location='index.php';</script>";
    exit();
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title><?php echo $d['judul']; ?> - Detail Proyek</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background: #f6f8fc; }
        .navbar-detail { background: #243b64; padding: 15px 0; color: white; }
        .content-card { background: #fff; border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; }
        .author-box { background: #f8f9fa; border-radius: 15px; padding: 20px; }
        .btn-back { color: #fff; text-decoration: none; font-size: 0.9rem; }
        .btn-back:hover { opacity: 0.8; }
        .link-section { background: #e9f1ff; border-radius: 15px; padding: 25px; border: 1px dashed #0d6efd; }
    </style>
</head>
<body>

    <nav class="navbar-detail mb-4">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="public_projects.php" class="btn-back"><i class="bi bi-arrow-left me-2"></i>Kembali ke Beranda</a>
            <span class="fw-bold d-none d-md-inline">DETAIL KARYA MAHASISWA</span>
            <a href="login.php" class="btn btn-sm btn-light rounded-pill px-3">Login</a>
        </div>
    </nav>

    <div class="container mb-5">
        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="content-card p-4 mb-4 text-center">
                    <img src="uploads/<?php echo $d['gambar']; ?>" class="img-fluid rounded-4 mb-3 shadow-sm" alt="Thumbnail">
                    <h5 class="fw-bold text-dark"><?php echo $d['judul']; ?></h5>
                    <span class="badge bg-primary-subtle text-primary mb-3"><?php echo $d['jurusan']; ?></span>
                    <hr>
                    <div class="author-box text-start">
                        <p class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Dibuat Oleh:</p>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle fs-2 me-3 text-secondary"></i>
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo $d['nama']; ?></h6>
                                <small class="text-muted"><?php echo $d['nim_nidn']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="content-card p-4 p-md-5">
                    <h4 class="fw-bold mb-3">Tentang Proyek</h4>
                    <p class="text-secondary mb-5" style="line-height: 1.8; text-align: justify;">
                        <?php echo nl2br($d['deskripsi']); ?>
                    </p>

                    <div class="link-section text-center">
                        <h5 class="fw-bold mb-3"><i class="bi bi-link-45deg me-2"></i>Tautan Eksternal</h5>
                        <p class="text-muted small mb-4">Proyek ini dapat diakses melalui tautan di bawah ini (GitHub, Demo, atau Portofolio Luar):</p>
                        
                        <?php if (!empty($d['link_proyek'])): ?>
                            <a href="<?php echo $d['link_proyek']; ?>" target="_blank" class="btn btn-primary btn-lg rounded-pill px-5 shadow">
                                <i class="bi bi-box-arrow-up-right me-2"></i>Buka Link Proyek
                            </a>
                            <div class="mt-2">
                                <small class="text-muted"><?php echo $d['link_proyek']; ?></small>
                            </div>
                        <?php else: ?>
                            <div class="text-secondary p-3">
                                <i class="bi bi-info-circle me-2"></i>Maaf, link proyek tidak tersedia untuk karya ini.
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mt-5 p-3 bg-warning-subtle rounded-3">
                        <small class="text-warning-emphasis">
                            <i class="bi bi-info-circle me-1"></i> Hak cipta karya ini sepenuhnya milik mahasiswa yang bersangkutan dan Politeknik Negeri Batam.
                        </small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>