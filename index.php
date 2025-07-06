<?php
include 'db_connection.php';

/* hitung total layanan & ambil data marker */
$count = $conn->query("SELECT COUNT(*) total FROM health_services")->fetch_assoc()['total'];
$svc   = $conn->query("SELECT name, latitude, longitude FROM health_services");
$services = [];
while ($row = $svc->fetch_assoc()) $services[] = $row;

/* data lansia per kecamatan */
$res = $conn->query("SELECT nama_kecamatan, jumlah_penduduk, jumlah_lansia FROM penduduk");
$lansia = [];
$lansia = [];
while ($row = $res->fetch_assoc()) {
    $lansia[$row['nama_kecamatan']] = [
        'jumlah_penduduk' => (int)$row['jumlah_penduduk'],
        'jumlah_lansia' => (int)$row['jumlah_lansia']
    ];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>UAS SIG Alfiah-22EO10052</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Leaflet & plugin CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/leaflet-extra-markers@1.2.1/dist/css/leaflet.extra-markers.min.css">

    <style>
    /* ———— GAYA ASLI ———— */
    <?php ?>#map {
        height: 400px;
    }

    /* tingginya nanti dioverride 500px oleh style inline */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box
    }

    body {
        font-family: Arial, sans-serif;
        display: flex;
        height: 100vh
    }

    #sidebar {
        width: 250px;
        background: #2c3e50;
        color: #fff;
        padding: 20px
    }

    #sidebar h2 {
        margin-bottom: 20px;
        font-size: 20px;
        border-bottom: 1px solid #555;
        padding-bottom: 10px
    }

    #sidebar button {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        border: none;
        background: #3498db;
        color: #fff;
        font-size: 16px;
        cursor: pointer
    }

    #sidebar button:hover {
        background: #2980b9
    }

    #main-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        height: 100%
    }

    #header {
        padding: 15px 25px;
        background: #ecf0f1;
        border-bottom: 1px solid #ccc
    }

    #header h1 {
        font-size: 24px;
        color: #2c3e50
    }

    #stats {
        padding: 10px 25px;
        background: #f9f9f9;
        border-bottom: 1px solid #ddd
    }

    #map {
        flex-grow: 1;
        width: 100%;
        height: 500px
    }

    #data-table {
        padding: 20px 25px;
        background: #fff;
        overflow-x: auto
    }
    </style>

    <!-- Library JS (HARUS sebelum skrip utama) -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/leaflet-extra-markers@1.2.1/dist/js/leaflet.extra-markers.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</head>

<body>
    <!-- ———— SIDEBAR ———— -->
    <div id="sidebar">
        <h2>SIG Banyumas</h2>
        <button onclick="window.open('crud.php','_blank')">Data Layanan</button>
    </div>

    <!-- ———— MAIN ———— -->
    <div id="main-content" style="position:relative;display:flex;flex-direction:column;height:100%;">
        <div id="header">
            <h1>Peta Demografi Lansia di Kabupaten Banyumas</h1>
        </div>

        <div id="stats">
            <p>Total Layanan Kesehatan: <strong><?= $count ?></strong></p>
        </div>

        <div id="map" style="height:500px;"></div>

        <div id="data-table">
            <h2 style="margin-bottom:10px;">Data Penduduk Lansia Kecamatan</h2>
            <table id="kecamatan-table" style="width:100%;border-collapse:collapse;display:none;">
                <thead>
                    <tr style="background:#f1f1f1;">
                        <th>Nama Kecamatan</th>
                        <th>Jumlah Lansia (Jiwa)</th>
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
            <div id="no-data" style="color:#777;">Klik salah satu kecamatan untuk melihat datanya.</div>
        </div>
    </div>

    <!-- ———— LEGEND (HTML tetap) ———— -->
    <div id="legend" style="
    position: absolute;
    top: 110px;
    right: 20px;
    background: white;
    padding: 10px 15px;
    border-radius: 6px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #333;
    z-index: 1000;
    width: 180px;
  ">
        <h4 style="margin:0 0 8px 0;">Jumlah Penduduk Lansia</h4>
        <div><span
                style="background:#800026;width:20px;height:14px;display:inline-block;margin-right:8px;border:1px solid #ccc"></span>Sangat
            Tinggi</div>
        <div><span
                style="background:#BD0026;width:20px;height:14px;display:inline-block;margin-right:8px;border:1px solid #ccc"></span>Tinggi
        </div>
        <div><span
                style="background:#E31A1C;width:20px;height:14px;display:inline-block;margin-right:8px;border:1px solid #ccc"></span>Sedang
        </div>
        <div><span
                style="background:#FC4E2A;width:20px;height:14px;display:inline-block;margin-right:8px;border:1px solid #ccc"></span>Rendah
        </div>
        <div><span
                style="background:#FD8D3C;width:20px;height:14px;display:inline-block;margin-right:8px;border:1px solid #ccc"></span>Sangat
            Rendah</div>
    </div>

    <script>
    /* -------- DATA PHP ke JS -------- */
    const services = <?= json_encode($services, JSON_NUMERIC_CHECK) ?>;
    const lansia = <?= json_encode($lansia,   JSON_NUMERIC_CHECK) ?>;

    /* -------- INISIALISASI PETA -------- */
    const map = L.map('map').setView([-7.450161992561026, 109.16218062235068], 10);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 17,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    /* -------- MARKER LAYANAN -------- */
    const hospitalIcon = L.ExtraMarkers.icon({
        icon: 'fa-hospital',
        markerColor: 'blue',
        shape: 'circle',
        prefix: 'fa'
    });
    services.forEach(s => {
        L.marker([s.latitude, s.longitude], {
                icon: hospitalIcon
            })
            .addTo(map)
            .bindPopup(`<b>${s.name}</b>`);
    });

    /* -------- CHOROPLETH LANSIA -------- */
    function getColor(d) {
        return d > 1100 ? '#800026' :
            d > 800 ? '#BD0026' :
            d > 500 ? '#E31A1C' :
            d > 300 ? '#FC4E2A' :
            d > 0 ? '#FD8D3C' : '#ffffe5';
    }

    function style(feature) {
        const nama = feature.properties.Name || feature.properties.nama_kecamatan;
        const row = lansia[nama] || {
            jumlah_penduduk: 0,
            jumlah_lansia: 0
        };
        return {
            fillColor: getColor(row.jumlah_lansia),
            weight: 2,
            color: '#FFF',
            dashArray: '3',
            fillOpacity: 0.7
        };
    }

    /* --- Pemrosesan klik & tabel --- */
    function bukaTabel(nama, val) {
        document.getElementById('table-body').innerHTML =
            `<tr><td style="padding:8px 12px;border:1px solid #ddd;">${nama}</td>
      <td style="padding:8px 12px;border:1px solid #ddd;">${val.toLocaleString()}</td></tr>`;
        document.getElementById('kecamatan-table').style.display = 'table';
        document.getElementById('no-data').style.display = 'none';
    }

    /* -------- LOAD GEOJSON BENAR -------- */
    let geoLayer = null,
        clicked = null;
    fetch('data/kecamatan.json')
        .then(r => r.json())
        .then(json => {
            geoLayer = L.geoJSON(json, {
                style,
                onEachFeature: (feature, layer) => {
                    const nama = feature.properties.Name || feature.properties.nama_kecamatan;
                    const data = lansia[nama] || {
                        jumlah_penduduk: 0,
                        jumlah_lansia: 0
                    };

                    layer.bindPopup(
                        `<b>${nama}</b><br>Penduduk: ${data.jumlah_penduduk.toLocaleString()}<br>Lansia: ${data.jumlah_lansia.toLocaleString()}`
                    );

                    layer.on('click', () => {
                        if (clicked) layer.resetStyle(clicked);
                        clicked = layer;
                        layer.setStyle({
                            fillColor: '#662506',
                            weight: 3,
                            color: '#000',
                            fillOpacity: 1
                        });
                        bukaTabel(nama, data.jumlah_lansia);
                    });
                    layer.on('mouseout', e => {
                        if (clicked !== layer) layer.resetStyle(feature);
                    });
                }
            }).addTo(map);

            /* gambar legenda */
            buildLegend();
        });

    function buildLegend() {
        const grades = [0, 300, 500, 800, 1100];
        let html = '<h4>Jumlah Lansia</h4>';
        for (let i = grades.length - 1; i >= 0; i--) {
            const from = grades[i];
            const to = grades[i + 1];
            html += `<div><span class="swatch" style="background:${getColor(from+1)}"></span> ${
      to ? (from+1)+'–'+to : '≥ '+from
    }</div>`;
        }
        document.getElementById('legend').innerHTML = html;
    }
    </script>
</body>

</html>
