<?php
    // Kredensial Database
    // CATATAN KEAMANAN: Di tahap produksi (live server), hindari menyimpan password langsung di file ini.
    // Gunakan Environment Variables (misal: file .env) agar kredensial tidak terekspos di Github/repositori.
    $s = "localhost";
    $u = "root";
    $p = ""; // Kosongkan jika default XAMPP, isi jika server live memiliki password
    $db = "db_raja";

    $dbcon = mysqli_connect($s, $u, $p, $db);

    if (!$dbcon) {
        die("Koneksi Gagal ke DataBase : " . mysqli_connect_error());
    }
    
    // Set charset agar karakter khusus terbaca dengan baik
    mysqli_set_charset($dbcon, "utf8");
?>