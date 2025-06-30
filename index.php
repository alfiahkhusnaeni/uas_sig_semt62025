<?php
// ---------- PHP: Ambil data ----------
include 'db_connection.php';

// data layanan kesehatan → marker
$serviceSql   = "SELECT id, name, latitude, longitude FROM health_services";
$serviceRes   = $conn->query($serviceSql);
$healthSrvArr = [];
while ($row = $serviceRes->fetch_assoc()) $healthSrvArr[] = $row;

// data jamaah haji per kecamatan → choropleth
$hajiSql = "SELECT nama_kecamatan, jumlah_jamaah_haji FROM kecamatan";
$hajiRes = $conn->query($hajiSql);
$hajiArr = [];
while ($row = $hajiRes->fetch_assoc()) $hajiArr[$row['nama_kecamatan']] = (int)$row['jumlah_jamaah_haji'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peta Kecamatan & Jamaah Haji Kabupaten Banyumas</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
    html,
    body {
        height: 100%;
        margin: 0;
        font-family: Arial, Helvetica, sans-serif
    }

    #container {
        display: flex;
        height: 100%
    }

    #sidebar {
        width: 260px;
        background: #2c3e50;
        color: #fff;
        padding: 20px;
        box-sizing: border-box
    }

    #sidebar h2 {
        margin-top: 0
    }

    #sidebar button {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border: none;
        color: #fff;
        background: #3498db;
        cursor: pointer
    }

    #sidebar button:hover {
        background: #2980b9
    }

    #map {
        flex: 1 1 auto;
        min-height: 400px
    }

    #legend {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #fff;
        padding: 10px 15px;
        border-radius: 6px;
        box-shadow: 0 0 8px rgba(0, 0, 0, .3);
        font-size: 14px;
        z-index: 1000;
        line-height: 18px
    }

    .swatch {
        display: inline-block;
        width: 22px;
        height: 14px;
        margin-right: 8px
    }

    #info {
        padding: 15px 0;
        color: #ecf0f1;
        font-size: 14px
    }
    </style>
</head>

<body>
    <div id="container">
        <div id="sidebar">
            <h2>SIG Banyumas</h2>
            <button onclick="window.open('crud.php','_blank')">Kelola Data</button>
            <div id="info">Klik kecamatan untuk melihat jumlah jamaah haji.</div>
        </div>
        <div id="map"></div>
        <div id="legend"></div>
    </div>

    <!--  Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    // ---------- JS: Ambil data PHP ----------
    const healthServices = <?php echo json_encode($healthSrvArr, JSON_NUMERIC_CHECK); ?>;
    const hajiData = <?php echo json_encode($hajiArr, JSON_NUMERIC_CHECK); ?>;

    // ---------- Inisiasi Peta ----------
    const map = L.map('map').setView([-7.45, 109.16], 11);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // ---------- Marker Layanan Kesehatan ----------
    healthServices.forEach(s => {
        L.marker([s.latitude, s.longitude]).addTo(map).bindPopup(`<b>${s.name}</b>`);
    });

    // ---------- Choropleth Jamaah Haji ----------
    function getColorHaji(d) {
        return d > 300 ? '#084594' : d > 200 ? '#2171b5' : d > 100 ? '#4292c6' : d > 50 ? '#6baed6' : d > 0 ?
            '#bdd7e7' : '#eff3ff';
    }

    function style(feature) {
        const nama = feature.properties.Name || feature.properties.nama_kecamatan;
        const val = hajiData[nama] || 0;
        return {
            fillColor: getColorHaji(val),
            weight: 2,
            color: '#fff',
            dashArray: '3',
            fillOpacity: 0.7
        };
    }

    fetch('data/kecamatan.json')
        .then(r => r.json())
        .then(geo => {
            L.geoJSON(geo, {
                style,
                onEachFeature: (feature, layer) => {
                    const nama = feature.properties.Name || feature.properties.nama_kecamatan;
                    const val = hajiData[nama] || 0;
                    layer.bindPopup(
                        `<b>${nama}</b><br>Jamaah Haji 2025: <b>${val.toLocaleString()}</b>`);
                    layer.on({
                        mouseover: e => {
                            e.target.setStyle({
                                weight: 3,
                                color: '#000',
                                fillOpacity: 0.9
                            });
                        },
                        mouseout: e => {
                            geoLayer.resetStyle(e.target);
                        },
                        click: e => {
                            document.getElementById('info').innerHTML =
                                `<b>${nama}</b><br>Jamaah Haji 2025: <b>${val.toLocaleString()}</b>`;
                        }
                    });
                }
            }).addTo(map);
            drawLegend();
        });

    // ---------- Legend ----------
    function drawLegend() {
        const grades = [0, 50, 100, 200, 300];
        let html = '<h4>Jamaah Haji</h4>';
        for (let i = grades.length - 1; i >= 0; i--) {
            const from = grades[i];
            const to = grades[i + 1];
            html += `<div><span class="swatch" style="background:${getColorHaji(from+1)}"></span>
            ${to?from.toLocaleString()+'–'+to.toLocaleString():'≥ '+from.toLocaleString()}</div>`;
        }
        document.getElementById('legend').innerHTML = html;
    }
    </script>
</body>

</html>