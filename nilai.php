<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }

    include 'onek.php';
    $pesan = "";

    // -- LOGIKA CRUD NILAI --
    if (isset($_POST['simpan_nilai'])) {
        $nisn = mysqli_real_escape_string($dbcon, $_POST['nisn']);
        $np = (float)$_POST['np'];
        $nk = (float)$_POST['nk'];
        $na = (float)$_POST['na'];

        // Cek apakah kandidat ini sudah ada nilainya di database
        $cek_nilai = mysqli_query($dbcon, "SELECT id_penilaian FROM penilaian WHERE nisn='$nisn'");
        if (mysqli_num_rows($cek_nilai) > 0) {
            // Update jika sudah ada
            mysqli_query($dbcon, "UPDATE penilaian SET np='$np', nk='$nk', na='$na' WHERE nisn='$nisn'");
            $pesan = "<div class='alert alert-success'>Nilai kandidat berhasil diperbarui!</div>";
        } else {
            // Insert jika belum ada
            mysqli_query($dbcon, "INSERT INTO penilaian (nisn, np, nk, na) VALUES ('$nisn', '$np', '$nk', '$na')");
            $pesan = "<div class='alert alert-success'>Nilai kandidat berhasil ditambahkan!</div>";
        }
    }

    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Input Penilaian</h2>
            <p class="text-muted m-0">Masukkan skor kandidat berdasarkan variabel yang ada</p>
        </div>
    </div>

    <?= $pesan ?>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>NISN / ID</th>
                        <th>Nama Kandidat</th>
                        <th class="text-center">Nilai Pelajaran (NP)</th>
                        <th class="text-center">Nilai Kepribadian (NK)</th>
                        <th class="text-center">Nilai Akademik (NA)</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        // Ambil semua kandidat beserta nilainya (jika ada)
                        $sql_nilai = "SELECT s.nisn, s.nama, p.np, p.nk, p.na 
                                      FROM siswa s 
                                      LEFT JOIN penilaian p ON s.nisn = p.nisn 
                                      ORDER BY s.nama ASC";
                        $query_nilai = mysqli_query($dbcon, $sql_nilai);

                        while($row = mysqli_fetch_array($query_nilai)){
                            $has_score = isset($row['np']) && $row['np'] !== null;
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                        <td class="text-secondary"><?= htmlspecialchars($row['nisn']) ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                        
                        <?php if($has_score): ?>
                            <td class="text-center fw-bold text-success"><?= $row['np'] ?></td>
                            <td class="text-center fw-bold text-success"><?= $row['nk'] ?></td>
                            <td class="text-center fw-bold text-success"><?= $row['na'] ?></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalNilai<?= $row['nisn'] ?>">
                                    <i class="fas fa-check-circle me-1"></i> Edit
                                </button>
                            </td>
                        <?php else: ?>
                            <td class="text-center text-muted fst-italic">-</td>
                            <td class="text-center text-muted fst-italic">-</td>
                            <td class="text-center text-muted fst-italic">-</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNilai<?= $row['nisn'] ?>">
                                    <i class="fas fa-pencil-alt me-1"></i> Input
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>

                    <div class="modal fade" id="modalNilai<?= $row['nisn'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">
                                        <?= $has_score ? 'Ubah Nilai Kandidat' : 'Input Nilai Baru' ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="" method="POST">
                                    <div class="modal-body">
                                        <div class="alert alert-info py-2 mb-4 border-0 rounded-3 text-center">
                                            <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                                            <span class="small">(<?= htmlspecialchars($row['nisn']) ?>)</span>
                                        </div>

                                        <input type="hidden" name="nisn" value="<?= $row['nisn'] ?>">
                                        
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label text-muted small fw-bold">Pelajaran (NP)</label>
                                                <input type="number" step="0.01" name="np" class="form-control rounded-3 text-center" value="<?= $has_score ? $row['np'] : '' ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label text-muted small fw-bold">Kepribadian (NK)</label>
                                                <input type="number" step="0.01" name="nk" class="form-control rounded-3 text-center" value="<?= $has_score ? $row['nk'] : '' ?>" required>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label text-muted small fw-bold">Akademik (NA)</label>
                                                <input type="number" step="0.01" name="na" class="form-control rounded-3 text-center" value="<?= $has_score ? $row['na'] : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="simpan_nilai" class="btn <?= $has_score ? 'btn-success' : 'btn-primary' ?> rounded-pill px-4">
                                            Simpan Nilai
                                        </button>
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