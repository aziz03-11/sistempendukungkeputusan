<?php
    require_once 'nav.php';
    include 'onek.php';

    $jml_siswa = 0; $jml_kriteria = 0; $jml_dinilai = 0;

    $q_siswa = mysqli_query($dbcon, "SELECT COUNT(*) as total FROM siswa");
    if ($q_siswa && $row = mysqli_fetch_assoc($q_siswa)) { $jml_siswa = $row['total']; }

    $q_kriteria = mysqli_query($dbcon, "SELECT COUNT(*) as total FROM kriteria");
    if ($q_kriteria && $row = mysqli_fetch_assoc($q_kriteria)) { $jml_kriteria = $row['total']; }

    $q_nilai = mysqli_query($dbcon, "SELECT COUNT(DISTINCT nisn) as total FROM penilaian");
    if ($q_nilai && $row = mysqli_fetch_assoc($q_nilai)) { $jml_dinilai = $row['total']; }

    // --- LOGIKA HITUNG GRAFIK SECARA 100% DINAMIS ---
    $arr_nama_perusahaan = array();
    $arr_jumlah_kandidat = array();

    $q_pt = mysqli_query($dbcon, "SELECT * FROM perusahaan ORDER BY id_perusahaan ASC");
    while($pt = mysqli_fetch_array($q_pt)) {
        $id_pt = $pt['id_perusahaan'];
        $arr_nama_perusahaan[] = $pt['nama_perusahaan'];
        
        // Hitung total kriteria & jumlah bobot perusahaan ini
        $q_sum = mysqli_query($dbcon, "SELECT SUM(bobot) as total_b FROM kriteria WHERE id_perusahaan=$id_pt");
        $sum_d = mysqli_fetch_assoc($q_sum);
        $total_bobot_pt = isset($sum_d['total_b']) && $sum_d['total_b'] > 0 ? $sum_d['total_b'] : 1;
        
        $lulus_counter = 0;
        
        // Hitung skor kelulusan setiap siswa untuk perusahaan ini
        $q_siswa_loop = mysqli_query($dbcon, "SELECT nisn FROM siswa");
        while($s_loop = mysqli_fetch_array($q_siswa_loop)) {
            $nisn_loop = $s_loop['nisn'];
            $nilai_smart_loop = 0;
            $punya_nilai = false;
            
            $q_kr = mysqli_query($dbcon, "SELECT * FROM kriteria WHERE id_perusahaan=$id_pt");
            while($kr = mysqli_fetch_array($q_kr)) {
                $id_kr = $kr['id_kriteria'];
                $bobot_kr = $kr['bobot'];
                
                $q_v = mysqli_query($dbcon, "SELECT nilai FROM penilaian WHERE nisn='$nisn_loop' AND id_kriteria=$id_kr");
                if(mysqli_num_rows($q_v) > 0) {
                    $v_d = mysqli_fetch_assoc($q_v);
                    $nilai_smart_loop += $v_d['nilai'] * ($bobot_kr / $total_bobot_pt);
                    $punya_nilai = true;
                }
            }
            
            if($punya_nilai && $nilai_smart_loop >= $pt['passing_grade']) {
                $lulus_counter++;
            }
        }
        $arr_jumlah_kandidat[] = $lulus_counter;
    }
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h2 class="fw-bold text-dark m-0">Beranda Dashboard</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3"><i class="fas fa-users fa-2x"></i></div>
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
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-3"><i class="fas fa-list-check fa-2x"></i></div>
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Total Indikator Kriteria</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $jml_kriteria ?></h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-2">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-3"><i class="fas fa-pen-to-square fa-2x"></i></div>
                <div>
                    <h6 class="text-muted fw-semibold mb-1">Kandidat Terpola Nilai</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= $jml_dinilai ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark m-0"><i class="fas fa-chart-bar text-primary me-2"></i>Grafik Rekomendasi Hasil Keputusan Otomatis</h5>
                <p class="text-muted small m-0">Balok grafik bertambah otomatis jika data perusahaan baru diinput ke database</p>
            </div>
            <div class="card-body px-4 pb-4">
                <div style="position: relative; width: 100%; height: 320px;">
                    <canvas id="spkBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('spkBarChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            // Memasukkan array nama perusahaan secara dinamis dari database menggunakan JSON
            labels: <?= json_encode($arr_nama_perusahaan) ?>,
            datasets: [{
                label: 'Jumlah Kandidat Lolos Standar',
                // Memasukkan array total counter hasil keputusan secara dinamis
                data: <?= json_encode($arr_jumlah_kandidat) ?>,
                backgroundColor: 'rgba(13, 110, 253, 0.85)',
                borderColor: 'rgb(13, 110, 253)',
                borderWidth: 1, borderRadius: 8, borderSkipped: false, barThickness: 50
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Poppins' } }, grid: { color: 'rgba(0, 0, 0, 0.05)' } },
                x: { ticks: { font: { family: 'Poppins', weight: '600' } }, grid: { display: false } }
            }
        }
    });
</script>
<?php require_once 'foot.php'; ?>