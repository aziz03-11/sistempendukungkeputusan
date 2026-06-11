<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }

    include 'onek.php';
    $pesan = "";

    // -- LOGIKA CRUD KRITERIA --
    if (isset($_POST['tambah_kriteria'])) {
        $nama_k = mysqli_real_escape_string($dbcon, $_POST['nama_kriteria']);
        $bobot = (float)$_POST['bobot'];
        mysqli_query($dbcon, "INSERT INTO kriteria (nama_kriteria, bobot) VALUES ('$nama_k', '$bobot')");
        $pesan = "<div class='alert alert-success'>Kriteria berhasil ditambahkan!</div>";
    }

    if (isset($_POST['edit_kriteria'])) {
        $id_k = (int)$_POST['id_kriteria'];
        $nama_k = mysqli_real_escape_string($dbcon, $_POST['nama_kriteria']);
        $bobot = (float)$_POST['bobot'];
        mysqli_query($dbcon, "UPDATE kriteria SET nama_kriteria='$nama_k', bobot='$bobot' WHERE id_kriteria=$id_k");
        $pesan = "<div class='alert alert-success'>Bobot kriteria berhasil diperbarui!</div>";
    }

    if (isset($_GET['hapus'])) {
        $id_hapus = (int)$_GET['hapus'];
        mysqli_query($dbcon, "DELETE FROM kriteria WHERE id_kriteria=$id_hapus");
        $pesan = "<div class='alert alert-success'>Kriteria berhasil dihapus!</div>";
    }

    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Data Kriteria</h2>
            <p class="text-muted m-0">Manajemen variabel dan bobot perhitungan</p>
        </div>
        <div class="col-lg-6 text-end">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahK">
                <i class="fas fa-plus me-2"></i>Tambah Kriteria
            </button>
        </div>
    </div>

    <?= $pesan ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Nama Kriteria</th>
                        <th class="text-center">Bobot Penilaian</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $sql_sum = mysqli_query($dbcon, "SELECT SUM(bobot) as tot FROM kriteria");
                        $sum_data = mysqli_fetch_assoc($sql_sum);
                        $total_bobot = ($sum_data['tot'] > 0) ? $sum_data['tot'] : 1;

                        $query_kriteria = mysqli_query($dbcon, "SELECT * FROM kriteria");
                        while($row = mysqli_fetch_array($query_kriteria)){
                            $persentase = round(($row['bobot'] / $total_bobot) * 100, 1);
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_kriteria']) ?></td>
                        <td class="text-center">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6 shadow-sm"><?= $row['bobot'] ?></span>
                            <span class="text-muted ms-2 fw-medium">(<?= $persentase ?>%)</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-warning rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditK<?= $row['id_kriteria'] ?>">
                                <i class="fas fa-sliders"></i> Ubah Bobot
                            </button>
                            <a href="kriteria.php?hapus=<?= $row['id_kriteria'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hapus kriteria ini? Perhitungan metode SMART bisa berubah signifikan.');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEditK<?= $row['id_kriteria'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Ubah Kriteria</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="id_kriteria" value="<?= $row['id_kriteria'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nama Kriteria</label>
                                            <input type="text" name="nama_kriteria" class="form-control rounded-3" value="<?= htmlspecialchars($row['nama_kriteria']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nilai Bobot</label>
                                            <input type="number" step="0.01" name="bobot" class="form-control rounded-3" value="<?= $row['bobot'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit_kriteria" class="btn btn-warning text-dark fw-bold rounded-pill px-4">Simpan Bobot</button>
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

<div class="modal fade" id="modalTambahK" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kriteria</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Kriteria</label>
                        <input type="text" name="nama_kriteria" class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nilai Bobot</label>
                        <input type="number" step="0.01" name="bobot" class="form-control rounded-3" placeholder="Contoh: 10, 20, 30" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_kriteria" class="btn btn-primary rounded-pill px-4">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>