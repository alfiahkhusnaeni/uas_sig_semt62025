<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $sql = "INSERT INTO health_services (name, address, latitude, longitude) VALUES ('$name', '$address', $latitude, $longitude)";
    $conn->query($sql);
  } elseif (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM health_services WHERE id=$id";
    $conn->query($sql);
  }
}

$result = $conn->query("SELECT * FROM health_services");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Layanan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', sans-serif;
    }

    body {
        display: flex;
        min-height: 100vh;
        background-color: #f4f6f8;
    }

    #sidebar {
        width: 220px;
        background-color: #2c3e50;
        color: white;
        padding: 20px;
    }

    #sidebar h2 {
        font-size: 18px;
        margin-bottom: 30px;
        border-bottom: 1px solid #555;
        padding-bottom: 10px;
    }

    #sidebar button {
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        border: none;
        background-color: #3498db;
        color: white;
        font-size: 15px;
        cursor: pointer;
        border-radius: 5px;
    }

    #main-content {
        flex: 1;
        padding: 30px;
        display: flex;
        flex-direction: column;
    }

    h1,
    h2 {
        color: #2c3e50;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    th,
    td {
        padding: 12px 14px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    th {
        background-color: #ecf0f1;
    }

    tr:hover {
        background-color: #f2f9ff;
    }

    .btn {
        padding: 8px 14px;
        border: none;
        border-radius: 5px;
        font-size: 13px;
        color: white;
        cursor: pointer;
        margin-right: 5px;
    }

    .btn.edit {
        background-color: #2980b9;
    }

    .btn.delete {
        background-color: #c0392b;
    }

    .btn.edit:hover {
        background-color: #2471a3;
    }

    .btn.delete:hover {
        background-color: #a93226;
    }

    form.inline-form {
        display: inline-block;
    }

    /* Form Tambah Data */
    .form-container {
        margin-top: 30px;
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 800px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }

    .form-group {
        flex: 1;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .form-container button {
        margin-top: 15px;
        padding: 10px 20px;
        font-size: 15px;
        background-color: #27ae60;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
    }

    .form-container button:hover {
        background-color: #229954;
    }
    </style>
    <script>
    function confirmDelete() {
        return confirm("Anda yakin ingin menghapus data ini?");
    }
    </script>
</head>

<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <h2>SIG BANYUMAS</h2>
        <button onclick="window.location.href='index.php'">Kembali ke Peta</button>
    </div>

    <!-- Main -->
    <div id="main-content">
        <h1>Data Layanan</h1>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Lat</th>
                    <th>Lng</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['address']); ?></td>
                    <td><?= $row['latitude']; ?></td>
                    <td><?= $row['longitude']; ?></td>
                    <td>
                        <form action="edit.php" method="get" class="inline-form">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button class="btn edit" type="submit">Edit</button>
                        </form>
                        <form action="crud.php" method="post" class="inline-form" onsubmit="return confirmDelete();">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <button class="btn delete" type="submit" name="delete">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="form-container">
            <h2>Tambah Data Baru</h2>
            <form action="crud.php" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" name="address" id="address" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" required>
                    </div>
                </div>
                <button type="submit" name="add">Tambah</button>
            </form>
        </div>
    </div>

</body>

</html>

<?php $conn->close(); ?>