<?php
if (isset($_GET['nama'])) {
    include 'db_connection.php';
    $nama = $_GET['nama'];

    $stmt = $conn->prepare("SELECT nama_kecamatan, jumlah_penduduk, jumlah_lansia FROM penduduk WHERE nama_kecamatan = ?");
    $stmt->bind_param("s", $nama);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    echo json_encode($data);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Parameter nama tidak ditemukan']);
}
?>
