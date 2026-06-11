<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }
    include 'onek.php';
    $pesan = "";

    if (isset($_POST['simpan_nilai_dinamis'])) {
        $nisn = mysqli_real_escape_string($dbcon, $_POST['nisn']);
        $nilai_array = $_POST['nilai']; 

        foreach ($nilai_array as $id_kriteria => $skor) {
            $id_kriteria = (int)$id_kriteria;
            $skor = (float)$skor;

            $cek = mysqli_query($dbcon, "SELECT id_penilaian FROM penilaian WHERE nisn='$nisn' AND id_kriteria=$id_kriteria");
            if (mysqli_num_rows($cek) > 0) {
                mysqli_query($dbcon, "UPDATE penilaian SET nilai='$skor' WHERE nisn='$nisn' AND id_kriteria=$id_kriteria");
            } else {
                mysqli_query($dbcon, "INSERT INTO penilaian (nisn, id_kriteria, nilai) VALUES ('$nisn', $id_kriteria, '$skor')");
            }
        }
        $pesan = "<div class='alert alert-success'>Seluruh parameter nilai berhasil disimpan!</div>";
    }

    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3">
        <div class="col-lg-12">
            <h2 class="fw-bold text-dark m-0">Input Penilaian Multi-Perusahaan</h2>
            <p class="text-muted">Form penilaian otomatis menyesuaikan kriteria dinamis perusahaan di database</p>
        </div>
    </div>

    <?= $pesan ?>

    <?php 
    $q_pt = mysqli_query($dbcon, "SELECT * FROM perusahaan ORDER BY id_perusahaan ASC");
    while($pt = mysqli_fetch_array($q_pt)) {
        $id_pt = $pt['id_perusahaan'];
    ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-light border-0 pt-3 px-4">
            <h5 class="fw-bold text-primary m-0"><i class="fas fa-building me-2"></i>Parameter Kriteria: <?= htmlspecialchars($pt['nama_perusahaan']) ?></h5>
        </div>
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Nama Kandidat</th>
                        <th>Skor Terinput Saat Ini</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    $q_siswa = mysqli_query($dbcon, "SELECT * FROM siswa ORDER BY nama ASC");
                    while($s = mysqli_fetch_array($q_siswa)) {
                        $nisn = $s['nisn'];
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($s['nama']) ?></span><br>
                            <span class="text-muted small"><?= htmlspecialchars($s['nisn']) ?></span>
                        </td>
                        <td>
                            <div class="row g-2">
                                <?php 
                                $q_kr = mysqli_query($dbcon, "SELECT * FROM kriteria WHERE id_perusahaan=$id_pt");
                                while($kr = mysqli_fetch_array($q_kr)) {
                                    $id_kr = $kr['id_kriteria'];
                                    $q_v = mysqli_query($dbcon, "SELECT nilai FROM penilaian WHERE nisn='$nisn' AND id_kriteria=$id_kr");
                                    $v_data = mysqli_fetch_assoc($q_v);
                                    $nilai_sekarang = isset($v_data['nilai']) ? $v_data['nilai'] : '';
                                ?>
                                <div class="col-auto me-2">
                                    <span class="badge bg-secondary mb-1 d-block text-start small"><?= htmlspecialchars($kr['nama_kriteria']) ?></span>
                                    <span class="fw-bold text-success ms-1"><?= $nilai_sekarang !== '' ? $nilai_sekarang : '<span class="text-muted fst-italic small">Belum diisi</span>' ?></span>
                                </div>
                                <?php } ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#mdl<?= $id_pt ?>_<?= $nisn ?>">
                                <i class="fas fa-edit"></i> Isi Nilai
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="mdl<?= $id_pt ?>_<?= $nisn ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Input Parameter Nilai</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="" method="POST">
                                    <div class="modal-body">
                                        <div class="alert alert-primary py-2 text-center rounded-3 small mb-3">
                                            Kandidat: <b><?= htmlspecialchars($s['nama']) ?></b>
                                        </div>
                                        <input type="hidden" name="nisn" value="<?= $nisn ?>">
                                        
                                        <?php 
                                        $q_kr2 = mysqli_query($dbcon, "SELECT * FROM kriteria WHERE id_perusahaan=$id_pt");
                                        while($kr2 = mysqli_fetch_array($q_kr2)) {
                                            $id_kr2 = $kr2['id_kriteria'];
                                            $q_v2 = mysqli_query($dbcon, "SELECT nilai FROM penilaian WHERE nisn='$nisn' AND id_kriteria=$id_kr2");
                                            $v_data2 = mysqli_fetch_assoc($q_v2);
                                            $nilai_sekarang2 = isset($v_data2['nilai']) ? $v_data2['nilai'] : '';
                                        ?>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold"><?= htmlspecialchars($kr2['nama_kriteria']) ?> (Bobot: <?= $kr2['bobot'] ?>)</label>
                                            <input type="number" step="0.1" name="nilai[<?= $id_kr2 ?>]" class="form-control rounded-3" value="<?= $nilai_sekarang2 ?>" required>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="simpan_nilai_dinamis" class="btn btn-primary rounded-pill px-4">Simpan Nilai</button>
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
    <?php } ?>
</div>

<?php require_once 'foot.php'; ?>