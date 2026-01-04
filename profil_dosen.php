<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data dosen terbaru
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$dosen = mysqli_fetch_assoc($query);

// Hitung Statistik
$total_proyek = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM projects"));
$sudah_dinilai = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM projects WHERE nilai IS NOT NULL"));
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil Dosen - Portofolio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background: #f6f8fc; overflow-x: hidden; }
        
        /* Sidebar Fixed */
        #sidebar { width: 250px; height: 100vh; background: #fff; position: fixed; left: 0; top: 0; padding: 20px; border-right: 1px solid #e5e5e5; transition: 0.3s; z-index: 1000; }
        #sidebar.hide { margin-left: -250px; }
        #sidebar a { display: block; padding: 10px 15px; border-radius: 8px; color: #333; text-decoration: none; margin-bottom: 6px; }
        #sidebar a.active { background: #e9f1ff; color: #0d6efd; font-weight: 500; }
        #sidebar .logout { color: #dc3545; margin-top: 30px; }

        /* Main Content Area */
        #main { margin-left: 250px; padding: 25px; transition: 0.3s; min-height: 100vh; }
        #main.full { margin-left: 0; }

        /* Topbar Style */
        .topbar { height: 48px; background: #243b64; border-radius: 8px; display: flex; align-items: center; padding: 0 15px; color: #fff; margin-bottom: 20px; }
        .toggle-btn { background: none; border: none; color: #fff; font-size: 20px; }

        /* Profile Card Style */
        .profile-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden; margin-top: 10px; }
        .profile-header-bg { background: linear-gradient(135deg, rgba(255, 255, 255, 1) 0%, #ffffffff 100%); height: 100px; }
        .profile-body { padding: 0 30px 30px; margin-top: -50px; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; border: 4px solid #fff; background: #eee; object-fit: cover; }
    </style>
</head>
<body>

    <div id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
        </div>
        <a href="dashboard_dosen.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="penilaian_dosen.php"><i class="bi bi-star me-2"></i>Penilaian</a>
        <a href="profil_dosen.php" class="active"><i class="bi bi-person me-2"></i>Profil</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>

    <div id="main">
        <div class="topbar">
            <button class="toggle-btn me-3" onclick="toggleSidebar()">☰</button>
            <span class="fw-semibold">Profil Saya</span>
        </div>

        <?php if(isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>Profil berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="profile-card">
            <div class="profile-header-bg"></div>
            <div class="profile-body">
                <div class="d-flex align-items-end mb-4">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($dosen['nama']); ?>&background=0d6efd&color=fff&size=128" class="profile-img shadow">
                    <div class="ms-3 mb-2">
                        <h4 class="fw-bold mb-0"><?php echo $dosen['nama']; ?></h4>
                        <span class="badge bg-primary-subtle text-primary">DOSEN</span>
                    </div>
                </div>

                <form action="proses_editprofil_dosen.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">NAMA LENGKAP</label>
                            <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($dosen['nama']); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted">PASSWORD BARU</label>
                            <input type="password" name="password" class="form-control" placeholder="Ganti Password">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mt-4 g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success-subtle p-3 rounded-3">
                            <i class="bi bi-check2-circle text-success fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted small fw-bold mb-1">PROYEK TELAH DINILAI</h6>
                            <h3 class="fw-bold mb-0"><?php echo $sudah_dinilai; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary-subtle p-3 rounded-3">
                            <i class="bi bi-files text-primary fs-3"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted small fw-bold mb-1">TOTAL PROYEK MASUK</h6>
                            <h3 class="fw-bold mb-0"><?php echo $total_proyek; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("hide");
            document.getElementById("main").classList.toggle("full");
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>