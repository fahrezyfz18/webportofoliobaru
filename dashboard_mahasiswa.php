<?php
session_start();
include 'koneksi.php';

// Anggap session sudah terisi saat login
// Jika belum ada session, kita buat dummy untuk testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; 
    $_SESSION['nama'] = "Mahasiswa";
    $_SESSION['role'] = "mahasiswa";
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Mahasiswa</title>
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
        .welcome-card { background: #243b64; color: #fff; border-radius: 12px; padding: 35px; text-align: center; margin-bottom: 30px; }
        .project-card { border-radius: 12px; height: 100%; }
        .project-card img { height: 170px; object-fit: cover; border-radius: 12px 12px 0 0; }
    </style>
</head>

<body>
    <div id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5></h5>
        </div>
        <a href="dashboard_mahasiswa.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="penilaian_mahasiswa.php"><i class="bi bi-star me-2"></i>Penilaian</a>
        <a href="profil_mahasiswa.php"><i class="bi bi-person me-2"></i>Profil</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>

    <div id="main">
        <div class="topbar">
            <button class="toggle-btn me-3" onclick="toggleSidebar()">☰</button>
            <span class="fw-semibold">Dashboard <?php echo ucfirst($role); ?></span>
        </div>

        <div class="welcome-card">
            <h4>Selamat Datang, <?php echo $_SESSION['nama']; ?></h4>
            <p>Kelola project Anda dan lihat progress penilaian dosen</p>
            <?php if($role == 'mahasiswa'): ?>
            <button class="btn btn-light btn-sm px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="bi bi-upload me-1"></i> Upload Proyek
            </button>
            <?php endif; ?>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari proyek...">
            </div>
        </div>

        <h6 class="fw-semibold mb-3">Proyek</h6>
        <div class="row g-4" id="projectContainer">
            <?php
            // Ambil data dari database
            $sql = ($role == 'dosen') ? "SELECT * FROM projects" : "SELECT * FROM projects WHERE user_id = $user_id";
            $result = mysqli_query($koneksi, $sql);

            while ($p = mysqli_fetch_assoc($result)) {
                $badgeColor = [
                    "Teknik Informatika" => "primary",
                    "Teknik Mesin" => "success",
                    "Teknik Elektro" => "warning",
                    "Manajemen dan Bisnis" => "danger"
                ][$p['jurusan']] ?? "secondary";
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 project-item" data-jurusan="<?php echo $p['jurusan']; ?>">
                <div class="card project-card shadow-sm h-100">
                    <img src="uploads/<?php echo $p['gambar']; ?>" class="card-img-top">
                    <div class="card-body d-flex flex-column">
                        <h6 class="project-title"><?php echo $p['judul']; ?></h6>
                        <span class="badge bg-<?php echo $badgeColor; ?> mb-2"><?php echo $p['jurusan']; ?></span>
                        <p class="small text-muted flex-grow-1"><?php echo $p['deskripsi']; ?></p>
                        <p class="small text-muted mb-2">
                            <strong>Upload:</strong><br>
                            <?php echo date('d M Y', strtotime($p['waktu'])); ?>
                        </p>
                        <div class="d-flex justify-content-between mt-auto">
                            <a href="detail_proyek_mahasiswa.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary btn-sm">Detail</a>
                            <?php if($role == 'mahasiswa'): ?>
                            <a href="proses_hapus.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin meghapus proyek?')">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4">
                <form action="proses_upload.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Upload Proyek</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Proyek</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jurusan</label>
                            <select name="jurusan" class="form-select" required>
                                <option value="Teknik Informatika">Teknik Informatika</option>
                                <option value="Teknik Elektro">Teknik Elektro</option>
                                <option value="Teknik Mesin">Teknik Mesin</option>
                                <option value="Manajemen dan Bisnis">Manajemen dan Bisnis</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link</label>
                            <input type="url" name="link_proyek" class="form-control" placeholder="https://github.com/username/project">
                            <small class="text-muted"></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Proyek</label>
                            <input type="datetime-local" name="waktu" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar Proyek</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Proyek</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("hide");
            document.getElementById("main").classList.toggle("full");
        }
        
        // Search filter logic
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let val = this.value.toLowerCase();
            document.querySelectorAll(".project-item").forEach(item => {
                let title = item.querySelector(".project-title").innerText.toLowerCase();
                item.style.display = title.includes(val) ? "" : "none";
            });
        });
    </script>
</body>
</html>