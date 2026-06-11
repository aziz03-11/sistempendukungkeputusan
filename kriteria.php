<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }
    include 'onek.php';
    $pesan = "";

    // 1. Tambah Kriteria
    if (isset($_POST['tambah_kriteria'])) {
        $id_pt = (int)$_POST['id_perusahaan'];
        $nama_k = mysqli_real_escape_string($dbcon, $_POST['nama_kriteria']);
        $bobot = (float)$_POST['bobot'];
        mysqli_query($dbcon, "INSERT INTO kriteria (id_perusahaan, nama_kriteria, bobot) VALUES ($id_pt, '$nama_k', '$bobot')");
        $pesan = "<div class='alert alert-success'>Kriteria berhasil ditambahkan ke perusahaan terkait!</div>";
    }

    // 2. Edit Kriteria
    if (isset($_POST['edit_kriteria'])) {
        $id_k = (int)$_POST['id_kriteria'];
        $id_pt = (int)$_POST['id_perusahaan'];
        $nama_k = mysqli_real_escape_string($dbcon, $_POST['nama_kriteria']);
        $bobot = (float)$_POST['bobot'];
        mysqli_query($dbcon, "UPDATE kriteria SET id_perusahaan=$id_pt, nama_kriteria='$nama_k', bobot='$bobot' WHERE id_kriteria=$id_k");
        $pesan = "<div class='alert alert-success'>Kriteria berhasil diperbarui!</div>";
    }

    // 3. Hapus Kriteria
    if (isset($_GET['hapus'])) {
        $id_h = (int)$_GET['hapus'];
        mysqli_query($dbcon, "DELETE FROM kriteria WHERE id_kriteria=$id_h");
        $pesan = "<div class='alert alert-success'>Kriteria berhasil dihapus!</div>";
    }

    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Data Kriteria Perusahaan</h2>
            <p class="text-muted m-0">Kelola indikator penilaian unik untuk setiap perusahaan</p>
        </div>
        <div class="col-lg-6 text-end">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#mdlTambahK">
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
                        <th>Perusahaan / Mitra</th>
                        <th>Nama Indikator Kriteria</th>
                        <th class="text-center">Bobot Nilai</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        // JOIN untuk membaca nama perusahaan asal kriteria
                        $sql_k = "SELECT k.*, p.nama_perusahaan FROM kriteria k 
                                  JOIN perusahaan p ON k.id_perusahaan = p.id_perusahaan 
                                  ORDER BY p.nama_perusahaan ASC, k.id_kriteria ASC";
                        $query_k = mysqli_query($dbcon, $sql_k);
                        while($row = mysqli_fetch_array($query_k)){
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                        <td><span class="badge bg-secondary rounded-pill px-2 py-1"><?= htmlspecialchars($row['nama_perusahaan']) ?></span></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama_kriteria']) ?></td>
                        <td class="text-center"><span class="fw-bold text-primary"><?= $row['bobot'] ?></span></td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#mdlEditK<?= $row['id_kriteria'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="kriteria.php?hapus=<?= $row['id_kriteria'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Hapus kriteria ini?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="mdlEditK<?= $row['id_kriteria'] ?>" tabindex="-1">
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
                                            <label class="form-label text-muted small fw-bold">Pilih Perusahaan</label>
                                            <select name="id_perusahaan" class="form-select rounded-3" required>
                                                <?php 
                                                $qp2 = mysqli_query($dbcon, "SELECT * FROM perusahaan");
                                                while($r_p2 = mysqli_fetch_array($qp2)) {
                                                    $sel = ($r_p2['id_perusahaan'] == $row['id_perusahaan']) ? "selected" : "";
                                                    echo "<option value='".$r_p2['id_perusahaan']."' $sel>".$r_p2['nama_perusahaan']."</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nama Kriteria</label>
                                            <input type="text" name="nama_kriteria" class="form-control rounded-3" value="<?= htmlspecialchars($row['nama_kriteria']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Bobot Nilai</label>
                                            <input type="number" step="0.1" name="bobot" class="form-control rounded-3" value="<?= $row['bobot'] ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit_kriteria" class="btn btn-primary rounded-pill px-4">Simpan</button>
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

<div class="modal fade" id="mdlTambahK" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kriteria Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Untuk Perusahaan Mitra</label>
                        <select name="id_perusahaan" class="form-select rounded-3" required>
                            <option value="">-- Pilih Perusahaan --</option>
                            <?php 
                            $qp = mysqli_query($dbcon, "SELECT * FROM perusahaan");
                            while($r_p = mysqli_fetch_array($qp)) {
                                echo "<option value='".$r_p['id_perusahaan']."'>".$r_p['nama_perusahaan']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Indikator Kriteria</label>
                        <input type="text" name="nama_kriteria" class="form-control rounded-3" placeholder="Contoh: Kekuatan Fisik, Administrasi, dll" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Bobot Nilai</label>
                        <input type="number" step="0.1" name="bobot" class="form-control rounded-3" placeholder="Contoh: 30" required>
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