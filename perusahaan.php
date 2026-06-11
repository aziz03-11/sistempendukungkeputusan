<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }

    include 'onek.php';
    $pesan = "";

    // -- LOGIKA CRUD PERUSAHAAN --
    if (isset($_POST['edit_perusahaan'])) {
        $id_p = (int)$_POST['id_perusahaan'];
        $nama_p = mysqli_real_escape_string($dbcon, $_POST['nama_perusahaan']);
        $passing = (float)$_POST['passing_grade'];
        
        mysqli_query($dbcon, "UPDATE perusahaan SET nama_perusahaan='$nama_p', passing_grade='$passing' WHERE id_perusahaan=$id_p");
        $pesan = "<div class='alert alert-success'>Data perusahaan berhasil diperbarui!</div>";
    }
    
    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Data Perusahaan Mitra</h2>
            <p class="text-muted m-0">Kelola nama perusahaan dan standar kelulusan nilai (Passing Grade)</p>
        </div>
    </div>

    <?= $pesan ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="10%">ID</th>
                        <th>Nama Perusahaan</th>
                        <th class="text-center">Minimal Nilai Kelulusan (Passing Grade)</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $query_p = mysqli_query($dbcon, "SELECT * FROM perusahaan");
                        while($row = mysqli_fetch_array($query_p)){
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $row['id_perusahaan'] ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_perusahaan']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-success rounded-pill px-3 py-2 fs-6 shadow-sm"><?= $row['passing_grade'] ?></span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEditP<?= $row['id_perusahaan'] ?>">
                                <i class="fas fa-edit me-1"></i> Edit / Atur Skor
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditP<?= $row['id_perusahaan'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Ubah Parameter Perusahaan</h5>
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
                                            <label class="form-label text-muted small fw-bold">Batas Minimal Nilai (Passing Grade)</label>
                                            <input type="number" step="0.1" name="passing_grade" class="form-control rounded-3" value="<?= $row['passing_grade'] ?>" required>
                                            <div class="form-text text-muted small">Kandidat dengan skor di atas atau sama dengan nilai ini akan ditempatkan di sini.</div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit_perusahaan" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
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

<?php require_once 'foot.php'; ?>