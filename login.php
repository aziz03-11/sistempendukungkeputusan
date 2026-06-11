<?php  
    session_start();
    include 'onek.php'; 

    $pesan = "";

    // Cek pesan dari parameter GET
    if (isset($_GET['id'])) {
        if ($_GET['id'] == 'false') {
            $pesan = "<div class='alert alert-danger text-center'>Username / password salah. Gagal masuk.</div>";
        } else if ($_GET['id'] == 'out') {
            $pesan = "<div class='alert alert-warning text-center'>Anda belum masuk, silakan masuk terlebih dahulu.</div>";
        } else {
            $pesan = "<div class='alert alert-success text-center'>Logout berhasil.</div>";
        }
    }

    // Proses login sebelum render HTML
    if (isset($_POST['submit'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Menggunakan Prepared Statement untuk mencegah SQL Injection
        $stmt = mysqli_prepare($dbcon, "SELECT username FROM admin WHERE username=? AND password=?");
        mysqli_stmt_bind_param($stmt, "ss", $username, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0 ) {
            $_SESSION['username'] = $username;
            $_SESSION['stat'] = 'masuk';
            header("Location: index.php");
            exit; // Pastikan script berhenti setelah redirect
        } else {
            $pesan = "<div class='alert alert-danger text-center'>Username atau password salah!</div>";
        }
        mysqli_stmt_close($stmt);
    }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Pengambilan Keputusan SMART - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="card border-0 shadow-lg rounded-4" style="width: 100%; max-width: 400px;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h3 class="fw-bold text-primary mb-3">SPK SMART</h3>
                <p class="text-muted">Silakan masuk ke akun Administrator</p>
            </div>

            <?= $pesan ?>

            <form role="form" action="" method="POST">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required autofocus>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password">Password</label>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" value="Remember Me">
                    <label class="form-check-label text-muted" for="remember">
                        Ingat Saya
                    </label>
                </div>
                <button type="submit" name="submit" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">Masuk</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>