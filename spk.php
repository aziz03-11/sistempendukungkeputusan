<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }
    include 'onek.php';
    require_once 'nav.php';
?>
            
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-lg-8">
            <h2 class="fw-bold text-dark m-0">Matriks Keputusan Penempatan & Pembinaan</h2>
            <p class="text-muted m-0">Siswa yang gagal memenuhi standar (passing grade) masuk Program Pembinaan</p>
        </div>
        <div class="col-lg-4 text-end">
            <a href="nilai.php" class="btn btn-primary rounded-pill px-4 shadow-sm"><i class="fas fa-edit me-2"></i>Update Nilai Pembinaan</a>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 text-nowrap table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th>Siswa</th>
                        <th class="text-center">Kelas</th>
                        <th>Detail Evaluasi Standar Perusahaan</th>
                        <th class="text-center">Rekomendasi Akhir</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $n = 1;
                    $q_siswa = mysqli_query($dbcon, "SELECT * FROM siswa ORDER BY nama ASC");

                    while ($s = mysqli_fetch_array($q_siswa)) {  
                        $nisn = $s['nisn'];
                        $kelas = $s['kelas'];
                        
                        $skor_tertinggi = -1;
                        $rekomendasi_pt = "Belum Dinilai";
                        $badge_color = "bg-secondary";
                        $ada_penilaian = false;
                ?>
                        <tr>
                            <td class="text-center text-muted fw-bold"><?=$n++?></td>
                            <td>
                                <span class="fw-semibold text-dark"><?= htmlspecialchars($s['nama']) ?></span><br>
                                <span class="text-muted small"><?= htmlspecialchars($nisn) ?></span>
                            </td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?= htmlspecialchars($kelas) ?></span></td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                <?php 
                                $q_pt = mysqli_query($dbcon, "SELECT * FROM perusahaan ORDER BY passing_grade DESC");
                                while($pt = mysqli_fetch_array($q_pt)) {
                                    $id_pt = $pt['id_perusahaan'];
                                    
                                    $q_sum = mysqli_query($dbcon, "SELECT SUM(bobot) as total_b FROM kriteria WHERE id_perusahaan=$id_pt");
                                    $sum_d = mysqli_fetch_assoc($q_sum);
                                    $total_bobot_pt = isset($sum_d['total_b']) && $sum_d['total_b'] > 0 ? $sum_d['total_b'] : 1;
                                    
                                    $nilai_akhir_smart = 0;
                                    $punya_nilai_pt_ini = false;
                                    
                                    $q_kr = mysqli_query($dbcon, "SELECT * FROM kriteria WHERE id_perusahaan=$id_pt");
                                    while($kr = mysqli_fetch_array($q_kr)) {
                                        $id_kr = $kr['id_kriteria'];
                                        $bobot_kr = $kr['bobot'];
                                        
                                        $q_v = mysqli_query($dbcon, "SELECT nilai FROM penilaian WHERE nisn='$nisn' AND id_kriteria=$id_kr");
                                        if(mysqli_num_rows($q_v) > 0) {
                                            $v_data = mysqli_fetch_assoc($q_v);
                                            $nilai_akhir_smart += $v_data['nilai'] * ($bobot_kr / $total_bobot_pt);
                                            $punya_nilai_pt_ini = true;
                                            $ada_penilaian = true;
                                        }
                                    }
                                    
                                    if($punya_nilai_pt_ini) {
                                        $lolos = ($nilai_akhir_smart >= $pt['passing_grade']);
                                        $txt_color = $lolos ? "text-success" : "text-danger";
                                        $status_txt = $lolos ? "Lolos" : "Gagal";
                                        
                                        echo "<span class='small fw-medium'>• ".htmlspecialchars($pt['nama_perusahaan'])." (Min: ".$pt['passing_grade'].") = <b class='".$txt_color."'>".round($nilai_akhir_smart, 2)."</b> (".$status_txt.")</span>";
                                        
                                        // Cari nilai lolos tertinggi
                                        if($lolos && $nilai_akhir_smart > $skor_tertinggi) {
                                            $skor_tertinggi = $nilai_akhir_smart;
                                            $rekomendasi_pt = $pt['nama_perusahaan'];
                                            $badge_color = "bg-success";
                                        }
                                    } else {
                                        echo "<span class='small text-muted'>• ".htmlspecialchars($pt['nama_perusahaan'])." = <i class='small'>Skor kriteria kosong</i></span>";
                                    }
                                }
                                
                                // LOGIKA PROGRAM PEMBINAAN: 
                                // Jika siswa sudah dinilai, TAPI skor tertingginya masih -1 (artinya gagal di semua perusahaan)
                                if($ada_penilaian && $skor_tertinggi == -1) {
                                    $rekomendasi_pt = "Program Pembinaan";
                                    $badge_color = "bg-danger"; // Warna merah menandakan butuh pembinaan/perbaikan nilai
                                }
                                ?>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill <?=$badge_color?> px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                    <?= htmlspecialchars($rekomendasi_pt) ?>
                                </span>
                            </td>
                        </tr>
                <?php } ?>
                </tbody>
            </table>  
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>