<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Ambil data user lebih lengkap dari database
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Profil Saya</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body { background: #f6f8fc; overflow-x: hidden; }
        #sidebar { width: 250px; height: 100vh; background: #fff; position: fixed; left: 0; top: 0; padding: 20px; border-right: 1px solid #e5e5e5; transition: 0.3s; z-index: 1000; }
        #sidebar.hide { margin-left: -250px; }
        #sidebar a { display: block; padding: 10px 15px; border-radius: 8px; color: #333; text-decoration: none; margin-bottom: 6px; }
        #sidebar a.active { background: #e9f1ff; color: #0d6efd; font-weight: 500; }
        #sidebar .logout { color: #dc3545; margin-top: 30px; }
        #main { margin-left: 250px; padding: 25px; transition: 0.3s; }
        #main.full { margin-left: 0; }
        .topbar { height: 48px; background: #243b64; border-radius: 8px; display: flex; align-items: center; padding: 0 15px; color: #fff; margin-bottom: 20px; }
        .toggle-btn { background: none; border: none; color: #fff; font-size: 20px; }

        /* Profile Styles */
        .profile-header { background: linear-gradient(135deg, #243b64 0%, #0d6efd 100%); height: 150px; border-radius: 12px 12px 0 0; }
        .profile-card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); margin-top: -75px; background: #fff; }
        .avatar-circle { width: 120px; height: 120px; background: #fff; border-radius: 50%; padding: 5px; margin: 0 auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .avatar-inner { width: 100%; height: 100%; background: #e9f1ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 50px; color: #0d6efd; font-weight: bold; }
        .info-label { color: #6c757d; font-size: 0.85rem; margin-bottom: 2px; }
        .info-value { font-weight: 600; color: #333; margin-bottom: 15px; }
    </style>
</head>

<body>
    <div id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5></h5>
        </div>
        <a href="dashboard_mahasiswa.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="penilaian_mahasiswa.php"><i class="bi bi-star me-2"></i>Penilaian</a>
        <a href="profil_mahasiswa.php" class="active"><i class="bi bi-person me-2"></i>Profil</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>

    <div id="main">
        <div class="topbar">
            <button class="toggle-btn me-3" onclick="toggleSidebar()">☰</button>
            <span class="fw-semibold">Profil</span>
        </div>

        <div class="modal fade" id="editProfilModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="proses_editprofil_mahasiswa.php" method="POST">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Perbarui Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo $user['nama']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NIM / NIDN (Tidak dapat diubah)</label>
                        <input type="text" class="form-control bg-light" value="<?php echo $user['nim_nidn']; ?>" readonly>
                    </div>
                    <hr>
                    <div class="mb-1">
                        <label class="form-label small fw-bold text-danger">Ganti Password</label>
                        <input type="password" name="password_baru" class="form-control" placeholder="Password baru">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update_profil" class="btn btn-primary w-100 py-2 rounded-3">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

        <div class="container-fluid">
            <div class="profile-header"></div>
            <div class="row justify-content-center px-3">
                <div class="col-md-8">
                    <div class="card profile-card">
                        <div class="card-body text-center pt-0">
                            <div class="avatar-circle">
                                <div class="avatar-inner">
                                    <?php echo strtoupper(substr($user['nama'], 0, 1)); ?>
                                </div>
                            </div>
                            <h3 class="mt-3 fw-bold"><?php echo $user['nama']; ?></h3>
                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill text-uppercase">
                                <?php echo $user['role']; ?>
                            </span>

                            <hr class="my-4 text-muted opacity-25">

                            <div class="row text-start px-md-4">
                                <div class="col-md-6">
                                    <p class="info-label">NIM / NIDN</p>
                                    <p class="info-value"><?php echo $user['nim_nidn']; ?></p>
                                    
                                    <p class="info-label">Status Akun</p>
                                    <p class="info-value text-success"><i class="bi bi-check-circle-fill me-1"></i> Aktif</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="info-label">Peran Sistem</p>
                                    <p class="info-value"><?php echo ucfirst($user['role']); ?></p>
                                </div>
                            </div>

                            <div class="mt-4 mb-2">
                                <button class="btn btn-primary btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#editProfilModal">
                                    <i class="bi bi-pencil-square me-1"></i> Edit Profil
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("hide");
            document.getElementById("main").classList.toggle("full");
        }
    </script>
</body>
</html>