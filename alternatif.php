<?php
    session_start();
    if (!isset($_SESSION['stat']) || $_SESSION['stat'] != 'masuk') {
        header("Location: login.php?id=out");
        exit;
    }

    include 'onek.php';

    $pesan = "";

    // -- LOGIKA CRUD KANDIDAT --
    // 1. Tambah Data
    if (isset($_POST['tambah_kandidat'])) {
        $nisn = mysqli_real_escape_string($dbcon, $_POST['nisn']);
        $nama = mysqli_real_escape_string($dbcon, $_POST['nama']);
        $cek = mysqli_query($dbcon, "SELECT nisn FROM siswa WHERE nisn='$nisn'");
        if(mysqli_num_rows($cek) > 0) {
            $pesan = "<div class='alert alert-danger'>Gagal: ID/NISN sudah terdaftar!</div>";
        } else {
            mysqli_query($dbcon, "INSERT INTO siswa (nisn, nama) VALUES ('$nisn', '$nama')");
            $pesan = "<div class='alert alert-success'>Data kandidat berhasil ditambahkan!</div>";
        }
    }

    // 2. Edit Data
    if (isset($_POST['edit_kandidat'])) {
        $nisn_lama = mysqli_real_escape_string($dbcon, $_POST['nisn_lama']);
        $nisn_baru = mysqli_real_escape_string($dbcon, $_POST['nisn']);
        $nama = mysqli_real_escape_string($dbcon, $_POST['nama']);
        
        mysqli_query($dbcon, "UPDATE siswa SET nisn='$nisn_baru', nama='$nama' WHERE nisn='$nisn_lama'");
        $pesan = "<div class='alert alert-success'>Data kandidat berhasil diperbarui!</div>";
    }

    // 3. Hapus Data
    if (isset($_GET['hapus'])) {
        $nisn_hapus = mysqli_real_escape_string($dbcon, $_GET['hapus']);
        // Karena di database menggunakan CASCADE, nilai di tabel penilaian juga akan otomatis terhapus
        mysqli_query($dbcon, "DELETE FROM siswa WHERE nisn='$nisn_hapus'");
        $pesan = "<div class='alert alert-success'>Data kandidat berhasil dihapus!</div>";
    }

    require_once 'nav.php';
?>

<div class="container-fluid py-4">
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6">
            <h2 class="fw-bold m-0 text-dark">Data Kandidat</h2>
            <p class="text-muted m-0">Kelola data peserta yang akan dievaluasi</p>
        </div>
        <div class="col-lg-6 text-end">
            <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus me-2"></i>Tambah Kandidat
            </button>
        </div>
    </div>

    <?= $pesan ?> <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 table-responsive">
            <table class="table table-hover table-borderless align-middle m-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="5%">No</th>
                        <th width="20%">NISN / ID Pelamar</th>
                        <th>Nama Lengkap</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $query_siswa = mysqli_query($dbcon, "SELECT * FROM siswa ORDER BY nama ASC");
                        while($row = mysqli_fetch_array($query_siswa)){
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-bold"><?= $no++ ?></td>
                        <td class="text-secondary"><?= htmlspecialchars($row['nisn']) ?></td>
                        <td class="fw-semibold text-dark"><?= htmlspecialchars($row['nama']) ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['nisn'] ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <a href="alternatif.php?hapus=<?= $row['nisn'] ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Yakin ingin menghapus kandidat ini? Data nilainya juga akan hilang.');">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit<?= $row['nisn'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content rounded-4 border-0 shadow">
                                <div class="modal-header border-bottom-0 pb-0">
                                    <h5 class="modal-title fw-bold">Edit Kandidat</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="" method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="nisn_lama" value="<?= $row['nisn'] ?>">
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">NISN / ID</label>
                                            <input type="text" name="nisn" class="form-control rounded-3" value="<?= htmlspecialchars($row['nisn']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control rounded-3" value="<?= htmlspecialchars($row['nama']) ?>" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" name="edit_kandidat" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
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

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Kandidat Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">NISN / ID Baru</label>
                        <input type="text" name="nisn" class="form-control rounded-3" placeholder="Contoh: 2024005" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3" placeholder="Masukkan nama" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_kandidat" class="btn btn-primary rounded-pill px-4">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'foot.php'; ?>