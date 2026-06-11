<?php
    require_once 'nav.php';
    include 'onek.php';

    // 1. Mengambil statistik jumlah data master dari database
    $jml_siswa = 0;
    $jml_kriteria = 0;
    $jml_dinilai = 0;

    $q_siswa = mysqli_query($dbcon, "SELECT COUNT(*) as total FROM siswa");
    if ($q_siswa && $row = mysqli_fetch_assoc($q_siswa)) {
        $jml_siswa = $row['total'];
    }

    $q_kriteria = mysqli_query($dbcon, "SELECT COUNT(*) as total FROM kriteria");
    if ($q_kriteria && $row = mysqli_fetch_assoc($q_kriteria)) {
        $jml_kriteria = $row['total'];
    }

    $q_nilai = mysqli_query($dbcon, "SELECT COUNT(*) as total FROM penilaian");
    if ($q_nilai && $row = mysqli_fetch_assoc($q_nilai)) {
        $jml_dinilai = $row['total'];
    }

    // 2. PROSES KALKULASI GRAFIK SECARA DINAMIS BERDASARKAN KRITERIA DATABASE
    $count_pt_a = 0; // Counter Rekomendasi PT. Teknologi Nusantara
    $count_pt_b = 0; // Counter Rekomendasi PT. Maju Mandiri

    // Ambil total nilai seluruh bobot kriteria yang ada di database
    $sqljumlah = "SELECT SUM(bobot) AS total_bobot FROM kriteria";
    $queryjumlah = mysqli_query($dbcon, $sqljumlah);
    $jumlah_data = mysqli_fetch_assoc($queryjumlah);
    $jumlah = isset($jumlah_data['total_bobot']) && $jumlah_data['total_bobot'] > 0 ? $jumlah_data['total_bobot'] : 1;
    
    // Memetakan bobot kriteria secara aman berdasarkan id_kriteria agar tidak tertukar saat proses CRUD
    $bobot_na = 0; // Nilai Akademik
    $bobot_np = 0; // Nilai Pelajaran
    $bobot_nk = 0; // Nilai Kepribadian

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
    
    // Ambil semua data nilai siswa untuk dihitung ke dalam grafik keputusan
    $sqlnilai = "SELECT * FROM penilaian";
    $querynilai = mysqli_query($dbcon, $sqlnilai);

    while ($barisnilai = mysqli_fetch_array($querynilai)) {  
        // Perhitungan Rumus Metode SMART secara dinamis menggunakan bobot database yang tepat
        $nilaiA = $barisnilai['na'] * ($bobot_na / $jumlah);
        $nilaiP = $barisnilai['np'] * ($bobot_np / $jumlah);
        $nilaiK = $barisnilai['nk'] * ($bobot_nk / $jumlah);
        $nilaievaluasi = $nilaiP + $nilaiK + $nilaiA;
        
        // Pengelompokan rekomendasi hasil akhir perusahaan
        if ($nilaievaluasi >= 75) {
            $count_pt_a++;
        } else {
            $count_pt_b++;
        }
    }
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h2 class="fw-bold text-dark m-0">Beranda Dashboard</h2>
</div>

<div class="alert alert-primary alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
    <strong>Selamat Datang, <?= isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Administrator' ?>!</strong><br>
    Sistem berjalan normal. Grafik di bawah memuat data real-time hasil normalisasi nilai kriteria dari database.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                    <i class="fas fa-users fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Total Data Kandidat</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $jml_siswa ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-3">
                    <i class="fas fa-list-check fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Data Kriteria</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $jml_kriteria ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-3">
                    <i class="fas fa-pen-to-square fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Kandidat Dinilai</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $jml_dinilai ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-7 mb-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark m-0"><i class="fas fa-chart-bar text-primary me-2"></i>Grafik Rekomendasi Penempatan Kerja</h5>
                <p class="text-muted small m-0">Menyesuaikan kriteria pembobotan dinamis database</p>
            </div>
            <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                <div style="position: relative; width: 100%; height: 280px;">
                    <canvas id="spkBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5 mb-4">
        <div class="card border-0 shadow-sm rounded-4 text-white h-100 d-flex flex-column justify-content-between p-4" style="background: linear-gradient(135deg, #0d6efd, #0b5ed7);">
            <div>
                <div class="mb-3">
                    <i class="fas fa-building fa-3x opacity-50"></i>
                </div>
                <h4 class="fw-bold">Detail Perankingan Penempatan</h4>
                <p class="text-light opacity-75 small">
                    Proses pembagian rekomendasi perusahaan disesuaikan dengan nilai passing grade evaluasi kriteria SMART. Klik tombol di bawah untuk meninjau rincian tabel skor.
                </p>
            </div>
            <div class="mt-4">
                <a href="spk.php" class="btn btn-light text-primary fw-bold rounded-pill px-4 w-100 shadow-sm py-2">
                    Buka Hasil SPK Lengkap <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('spkBarChart').getContext('2d');
    const spkBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['PT. Teknologi Nusantara', 'PT. Maju Mandiri'],
            datasets: [{
                label: 'Jumlah Kandidat',
                data: [<?= $count_pt_a ?>, <?= $count_pt_b ?>],
                backgroundColor: [
                    'rgba(25, 135, 84, 0.85)',
                    'rgba(13, 110, 253, 0.85)'
                ],
                borderColor: [
                    'rgb(25, 135, 84)',
                    'rgb(13, 110, 253)'
                ],
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 60
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { padding: 12, cornerRadius: 8 }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { family: 'Poppins' } },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' }
                },
                x: {
                    ticks: { font: { family: 'Poppins', weight: '600' } },
                    grid: { display: false }
                }
            }
        }
    });
</script>

<?php 
    require_once 'foot.php';
?>