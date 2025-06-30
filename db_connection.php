<?php
// db_connection.php
// File ini digunakan untuk mengatur koneksi ke database

$servername = "localhost";  // Server database
$username = "root";         // Username database
$password = "";             // Password database
$database = "project_sig";  // Nama database

// Membuat koneksi ke database menggunakan mysqli
$conn = new mysqli($servername, $username, $password, $database);

// Cek apakah koneksi berhasil
if ($conn->connect_error) {
    // Menangani error koneksi dengan pesan yang lebih informatif
    die("Connection failed: " . $conn->connect_error);
}

// Mengatur encoding ke UTF-8 setelah koneksi berhasil
if (!$conn->set_charset("utf8")) {
    // Menangani error pada pengaturan karakter set
    die("Error loading character set utf8: " . $conn->error);
}

// Menggunakan prepared statements (jika diperlukan) untuk keamanan terhadap SQL Injection

// Fungsi untuk menutup koneksi
function closeConnection() {
    global $conn;
    if ($conn) {
        $conn->close(); // Menutup koneksi
    }
}

// Menangani potensi masalah dengan koneksi database
try {
    // Verifikasi sederhana untuk memastikan koneksi berjalan lancar
    if ($conn->ping()) {
        // Koneksi berhasil, Anda bisa melanjutkan dengan operasi database lainnya
        // echo "Connected successfully"; // Uncomment jika ingin memastikan koneksi berhasil (untuk debugging)
    } else {
        throw new Exception("Database connection lost.");
    }
} catch (Exception $e) {
    // Menangani kesalahan umum dengan try-catch untuk keamanan dan debugging
    die("Connection failed: " . $e->getMessage());
}

// Menambahkan koneksi ke dalam session jika diperlukan
// session_start();
// $_SESSION['db_connection'] = $conn;

// Pastikan koneksi database selalu ditutup setelah operasi selesai
// closeConnection(); // Uncomment setelah selesai mengoperasikan koneksi
?>
