<?php
session_start();
include 'koneksi.php';

// Proteksi halaman: pastikan hanya dosen yang bisa masuk
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Dosen</title>
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
        /* Perbedaan warna card untuk Dosen agar lebih eksklusif */
        .welcome-card { background: linear-gradient(135deg, #243b64 0%, #1a2a4d 100%); color: #fff; border-radius: 12px; padding: 35px; text-align: center; margin-bottom: 30px; }
        .project-card { border-radius: 12px; height: 100%; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .project-card img { height: 170px; object-fit: cover; border-radius: 12px 12px 0 0; }
        .student-name { font-size: 0.75rem; font-weight: bold; color: #0d6efd; text-transform: uppercase; }
    </style>
</head>

<body>
    <div id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5></h5>
        </div>
        <a href="dashboard_dosen.php" class="active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="penilaian_dosen.php"><i class="bi bi-star me-2"></i>Penilaian</a>
        <a href="profil_dosen.php"><i class="bi bi-person me-2"></i>Profil</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>

    <div id="main">
        <div class="topbar">
            <button class="toggle-btn me-3" onclick="toggleSidebar()">☰</button>
            <span class="fw-semibold">Dashboard Dosen</span>
        </div>

        <div class="welcome-card">
            <h4>Selamat Datang, <?php echo $_SESSION['nama']; ?></h4>
            <p>Pantau semua proyek mahasiswa dan berikan penilaian akademik</p>
            <a href="penilaian_dosen.php" class="btn btn-light btn-sm px-4">
                <i class="bi bi-check2-all me-1"></i> Mulai Menilai
            </a>
        </div>

        <div class="row mb-4">
            <div class="col-md-6 mb-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama mahasiswa atau judul proyek...">
            </div>
        </div>

        <h6 class="fw-semibold mb-3">Proyek Mahasiswa</h6>
        <div class="row g-4" id="projectContainer">
            <?php
            // Join dengan tabel users untuk mendapatkan nama pengunggah (mahasiswa)
            $sql = "SELECT projects.*, users.nama as nama_mhs 
                    FROM projects 
                    JOIN users ON projects.user_id = users.id 
                    ORDER BY projects.waktu DESC";
            $result = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($p = mysqli_fetch_assoc($result)) {
                    $badgeColor = [
                        "Teknik Informatika" => "primary",
                        "Teknik Mesin" => "success",
                        "Teknik Elektro" => "warning",
                        "Manajemen dan Bisnis" => "danger"
                    ][$p['jurusan']] ?? "secondary";
            ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 project-item">
                <div class="card project-card shadow-sm h-100">
                    <img src="uploads/<?php echo $p['gambar']; ?>" class="card-img-top">
                    <div class="card-body d-flex flex-column">
                        <div class="student-name mb-1">
                            <i class="bi bi-person-fill"></i> <?php echo $p['nama_mhs']; ?>
                        </div>
                        <h6 class="project-title fw-bold"><?php echo $p['judul']; ?></h6>
                        <span class="badge bg-<?php echo $badgeColor; ?>-subtle text-<?php echo $badgeColor; ?> mb-2" style="width: fit-content;">
                            <?php echo $p['jurusan']; ?>
                        </span>
                        <p class="small text-muted flex-grow-1"><?php echo substr($p['deskripsi'], 0, 80) . '...'; ?></p>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($p['waktu'])); ?></small>
                                <?php if($p['nilai']): ?>
                                    <span class="badge bg-success text-white">Sudah Dinilai</span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">Belum Dinilai</span>
                                <?php endif; ?>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="penilaian_dosen.php?id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm rounded-pill">
                                    <i class="bi bi-pencil-square me-1"></i> Beri Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<div class='col-12 text-center'><p class='text-muted'>Belum ada proyek yang diunggah mahasiswa.</p></div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("hide");
            document.getElementById("main").classList.toggle("full");
        }
        
        // Search filter logic (bisa mencari berdasarkan judul atau nama mahasiswa)
        document.getElementById("searchInput").addEventListener("keyup", function() {
            let val = this.value.toLowerCase();
            document.querySelectorAll(".project-item").forEach(item => {
                let content = item.innerText.toLowerCase();
                item.style.display = content.includes(val) ? "" : "none";
            });
        });
    </script>
</body>
</html>