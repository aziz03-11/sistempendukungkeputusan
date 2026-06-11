<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }
    include 'onek.php';
    $pesan = "";

    // 1. Tambah Perusahaan
    if (isset($_POST['tambah_perusahaan'])) {
        $nama_p = mysqli_real_escape_string($dbcon, $_POST['nama_perusahaan']);
        $passing = (float)$_POST['passing_grade'];
        mysqli_query($dbcon, "INSERT INTO perusahaan (nama_perusahaan, passing_grade) VALUES ('$nama_p', '$passing')");
        $pesan = "<div class='alert alert-success'>Perusahaan baru berhasil ditambahkan!</div>";
    }

    // 2. Edit Perusahaan
    if (isset($_POST['edit_perusahaan'])) {
        $id_p = (int)$_POST['id_perusahaan'];
        $nama_p = mysqli_real_escape_string($dbcon, $_POST['nama_perusahaan']);
        $passing = (float)$_POST['passing_grade'];
        mysqli_query($dbcon, "UPDATE perusahaan SET nama_perusahaan='$nama_p', passing_grade='$passing' WHERE id_perusahaan=$id_p");
        $pesan = "<div class='alert alert-success'>Data perusahaan berhasil diperbarui!</div>";
    }

    // 3. Hapus Perusahaan
    if (isset($_GET['hapus'])) {
        $id_h = (int)$_GET['hapus'];
        mysqli_query($dbcon, "DELETE FROM perusahaan WHERE id_perusahaan=$id_h");
        $pesan = "<div class='alert alert-success'>Perusahaan berhasil dihapus dari sistem!</div>";
    }

    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Data Perusahaan Mitra</h2>
            <p class="text-muted m-0">Manajemen instansi penempatan kerja kandidat</p>
        </div>
        <div class="col-lg-6 text-end">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#mdlTambahP">
                <i class="fas fa-plus me-2"></i>Tambah Perusahaan
            </button>
        </div>
    </div>

    <?= $pesan ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="10%">No</th>
                        <th>Nama Perusahaan / Instansi</th>
                        <th class="text-center">Passing Grade (Batas Nilai)</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $query_p = mysqli_query($dbcon, "SELECT * FROM perusahaan ORDER BY id_perusahaan ASC");
                        while($row = mysqli_fetch_array($query_p)){
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-success rounded-pill px-3 py-2">>= <?= $row['passing_grade'] ?></span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#mdlEditP<?= $row['id_perusahaan'] ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="perusahaan.php?hapus=<?= $row['id_perusahaan'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hapus perusahaan ini? Semua data kriteria dan nilai yang terikat akan terhapus permanen.');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="mdlEditP<?= $row['id_perusahaan'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Ubah Perusahaan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_perusahaan" value="<?= $row['id_perusahaan'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nama Perusahaan</label>
                                            <input type="text" name="nama_perusahaan" class="form-control rounded-3" value="<?= htmlspecialchars($row['nama_perusahaan']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Standar Nilai Kelulusan (Passing Grade)</label>
                                            <input type="number" step="0.1" name="passing_grade" class="form-control rounded-3" value="<?= $row['passing_grade'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit_perusahaan" class="btn btn-primary rounded-pill px-4">Simpan</button>
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

<div class="modal fade" id="mdlTambahP" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Perusahaan Mitra</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Perusahaan / Instansi</label>
                        <input type="text" name="nama_perusahaan" class="form-control rounded-3" placeholder="Masukkan nama PT / Instansi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Passing Grade Minimal</label>
                        <input type="number" step="0.1" name="passing_grade" class="form-control rounded-3" placeholder="Contoh: 75" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_perusahaan" class="btn btn-primary rounded-pill px-4">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>