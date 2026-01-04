<?php
session_start();
include 'koneksi.php';

// Proteksi halaman
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$nama_dosen = $_SESSION['nama'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Panel Penilaian Dosen</title>
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
        
        .card-table { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .grade-badge { width: 35px; height: 35px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .status-pill { font-size: 0.7rem; padding: 2px 8px; border-radius: 20px; }
    </style>
</head>

<body>
    <div id="sidebar">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5></h5>
        </div>
        <a href="dashboard_dosen.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="penilaian.php" class="active"><i class="bi bi-star me-2"></i>Penilaian</a>
        <a href="profil_dosen.php"><i class="bi bi-person me-2"></i>Profil</a>
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </div>

    <div id="main">
        <div class="topbar">
            <button class="toggle-btn me-3" onclick="toggleSidebar()">☰</button>
            <span class="fw-semibold">Penilaian Mahasiswa</span>
        </div>

        <div class="card card-table">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Daftar Pengumpulan Proyek</h5>
                    <div class="badge bg-primary px-3 py-2">Dosen: <?php echo $nama_dosen; ?></div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Mahasiswa</th>
                                <th>Judul Proyek</th>
                                <th>Status</th>
                                <th>Nilai</th>
                                <th>Grade</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT p.*, u.nama as nama_mhs 
                                    FROM projects p 
                                    JOIN users u ON p.user_id = u.id 
                                    ORDER BY p.nilai ASC, p.waktu DESC";
                            $result = mysqli_query($koneksi, $sql);

                            while ($p = mysqli_fetch_assoc($result)) {
                                $nilai = $p['nilai'];
                                // Logika Grade
                                if($nilai === null || $nilai === "") { $grade = "-"; $color = "secondary"; $status = "Belum"; }
                                elseif($nilai >= 85) { $grade = "A"; $color = "success"; $status = "Selesai"; }
                                elseif($nilai >= 75) { $grade = "B"; $color = "primary"; $status = "Selesai"; }
                                elseif($nilai >= 60) { $grade = "C"; $color = "warning"; $status = "Selesai"; }
                                else { $grade = "E"; $color = "danger"; $status = "Selesai"; }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo $p['nama_mhs']; ?></div>
                                    <small class="text-muted"><?php echo $p['jurusan']; ?></small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="uploads/<?php echo $p['gambar']; ?>" class="rounded me-2" style="width: 45px; height: 45px; object-fit: cover;">
                                        <div>
                                            <div class="small fw-medium"><?php echo $p['judul']; ?></div>
                                            <small class="text-muted" style="font-size: 0.7rem;">Upload: <?php echo date('d/m/y', strtotime($p['waktu'])); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-pill bg-<?php echo ($status == 'Belum') ? 'light text-dark' : 'success-subtle text-success'; ?> border">
                                        <?php echo $status; ?> dinilai
                                    </span>
                                </td>
                                <td class="fw-bold"><?php echo ($nilai !== null) ? $nilai : '-'; ?></td>
                                <td>
                                    <div class="grade-badge bg-<?php echo $color; ?> text-white">
                                        <?php echo $grade; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNilai<?php echo $p['id']; ?>">
                                        <i class="bi bi-pencil-fill me-1"></i> Nilai
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalNilai<?php echo $p['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content rounded-4 shadow border-0">
                                        <form action="proses_penilaian.php" method="POST">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title fw-bold">Input Nilai Mahasiswa</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-4">
                                                    <img src="uploads/<?php echo $p['gambar']; ?>" class="rounded-3 mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                                    <h6 class="mb-0 fw-bold"><?php echo $p['judul']; ?></h6>
                                                    <small class="text-muted"><?php echo $p['nama_mhs']; ?></small>
                                                </div>
                                                
                                                <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Skor (0 - 100)</label>
                                                    <input type="number" name="nilai" class="form-control form-control-lg bg-light border-0" value="<?php echo $p['nilai']; ?>" min="0" max="100" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Komentar/Feedback Dosen</label>
                                                    <textarea name="feedback" class="form-control bg-light border-0" rows="4" placeholder="Berikan arahan atau masukan untuk mahasiswa..."><?php echo $p['feedback']; ?></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">Simpan & Kirim Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php } ?>
                        </tbody>
                    </table>
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