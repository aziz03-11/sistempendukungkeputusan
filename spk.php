<?php
    session_start();
    // Validasi keamanan sesi login
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }

    include 'onek.php';
    require_once 'nav.php';
?>
            
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Hasil Keputusan SPK</h2>
            <p class="text-muted m-0">Rekomendasi penempatan kerja otomatis berdasarkan perhitungan Metode SMART</p>
        </div>
        <div class="col-lg-6 text-end">
            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4 shadow-sm">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 text-nowrap table-responsive">
                    <table class="table table-hover table-borderless align-middle m-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="15%">NISN / ID Pelamar</th>
                                <th width="25%">Nama Kandidat</th>
                                <th class="text-center">Nilai Pelajaran (NP)</th>
                                <th class="text-center">Nilai Kepribadian (NK)</th>
                                <th class="text-center">Nilai Akademik (NA)</th>
                                <th class="text-center">Nilai Evaluasi Akhir</th> 
                                <th class="text-center">Rekomendasi Penempatan</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $n = 1;

                            // 1. Ambil total akumulasi nilai bobot kriteria secara dinamis dari database
                            $sqljumlah = "SELECT SUM(bobot) AS total_bobot FROM kriteria";
                            $queryjumlah = mysqli_query($dbcon, $sqljumlah);
                            $jumlah_data = mysqli_fetch_assoc($queryjumlah);
                            $jumlah = isset($jumlah_data['total_bobot']) && $jumlah_data['total_bobot'] > 0 ? $jumlah_data['total_bobot'] : 1;
                            
                            // 2. Pemetaan bobot aman berdasarkan id_kriteria dari tabel kriteria
                            $bobot_na = 0;
                            $bobot_np = 0;
                            $bobot_nk = 0;

                            $sqlkriteria = "SELECT id_kriteria, bobot FROM kriteria";
                            $querykriteria = mysqli_query($dbcon, $sqlkriteria);
                            while ($bariskriteria = mysqli_fetch_array($querykriteria)) {
                                if ($bariskriteria['id_kriteria'] == 1) {
                                    $bobot_na = $bariskriteria['bobot'];
                                } elseif ($bariskriteria['id_kriteria'] == 2) {
                                    $bobot_np = $bariskriteria['bobot'];
                                } elseif ($bariskriteria['id_kriteria'] == 3) {
                                    $bobot_nk = $bariskriteria['bobot'];
                                }
                            }

                            // 3. Ambil aturan kelulusan perusahaan dari database (diurutkan dari passing grade tertinggi)
                            $perusahaan_query = mysqli_query($dbcon, "SELECT * FROM perusahaan ORDER BY passing_grade DESC");
                            $pt_utama = mysqli_fetch_assoc($perusahaan_query);    // Baris 1: PT Utama (Passing Grade Tinggi)
                            $pt_sekunder = mysqli_fetch_assoc($perusahaan_query); // Baris 2: PT Kedua (Catch-all / Sisa)
                            
                            // 4. Menggabungkan data nilai dengan nama kandidat menggunakan JOIN
                            $sqlnilai = "SELECT p.*, s.nama FROM penilaian p JOIN siswa s ON p.nisn = s.nisn";
                            $querynilai = mysqli_query($dbcon, $sqlnilai);

                            while ($barisnilai = mysqli_fetch_array($querynilai)) {  
                                
                                // Penghitungan bobot evaluasi SMART yang disinkronkan dengan kriteria database
                                $nilaiA = $barisnilai['na'] * ($bobot_na / $jumlah);
                                $nilaiP = $barisnilai['np'] * ($bobot_np / $jumlah);
                                $nilaiK = $barisnilai['nk'] * ($bobot_nk / $jumlah);
                                $nilaievaluasi = $nilaiP + $nilaiK + $nilaiA;
                                
                                // Logika penentuan rekomendasi perusahaan berdasarkan passing grade di database
                                if ($pt_utama && $nilaievaluasi >= $pt_utama['passing_grade']) {
                                    $keputusan = $pt_utama['nama_perusahaan'];
                                    $badge = "bg-success"; // Warna hijau untuk yang lolos PT Utama
                                } else if ($pt_sekunder) {
                                    $keputusan = $pt_sekunder['nama_perusahaan'];
                                    $badge = "bg-primary"; // Warna biru untuk alternatif penempatan sisa
                                } else {
                                    $keputusan = "Belum Terklasifikasi";
                                    $badge = "bg-secondary";
                                }
                                ?>
                                <tr>
                                    <td class="text-center text-muted fw-bold"><?=$n?></td>
                                    <td class="text-secondary"><?=$barisnilai['nisn']?></td>
                                    <td class="fw-semibold text-dark"><?=$barisnilai['nama'] ?></td>
                                    <td class="text-center"><?=$barisnilai['np']?></td>
                                    <td class="text-center"><?=$barisnilai['nk']?></td>
                                    <td class="text-center"><?=$barisnilai['na']?></td>
                                    <td class="text-center fw-bold text-success"><?= round($nilaievaluasi, 3)?></td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill <?=$badge?> px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                            <i class="fas fa-building me-1"></i> <?= htmlspecialchars($keputusan) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php    
                            $n++;
                            }
                            ?>
                        </tbody>
                    </table>  
                </div>
            </div>  
        </div>
    </div>
</div>

<?php 
    require_once 'foot.php';
?>