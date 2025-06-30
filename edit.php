<?php
include 'db_connection.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data untuk ID tersebut
$query = "SELECT * FROM health_services WHERE id=$id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    die("Data tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Layanan Kesehatan</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f8;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px;
        margin: 50px auto;
        background-color: #fff;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 25px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        color: #555;
        font-weight: 500;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    .buttons {
        display: flex;
        justify-content: space-between;
    }

    button {
        background-color: #2f80ed;
        color: white;
        padding: 10px 18px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    button:hover {
        background-color: #1c64d1;
    }

    .back-button {
        background-color: #777;
    }

    .back-button:hover {
        background-color: #555;
    }
    </style>
</head>

<body>

    <div class="container">
        <h1>Edit Data Layanan Kesehatan</h1>
        <form action="crud.php" method="post">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

            <label for="name">Nama:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>

            <label for="address">Alamat:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($data['address']); ?>"
                required>

            <label for="latitude">Latitude:</label>
            <input type="number" step="any" id="latitude" name="latitude"
                value="<?php echo htmlspecialchars($data['latitude']); ?>" required>

            <label for="longitude">Longitude:</label>
            <input type="number" step="any" id="longitude" name="longitude"
                value="<?php echo htmlspecialchars($data['longitude']); ?>" required>

            <div class="buttons">
                <button type="submit" name="update">Simpan</button>
                <a href="crud.php"><button type="button" class="back-button">Kembali</button></a>
            </div>
        </form>
    </div>

</body>

</html>

<?php $conn->close(); ?>