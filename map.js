/* map.js – Leaflet setup for Dashboard SIG Banyumas
 * ------------------------------------------------
 * 1. Inisialisasi peta Leaflet pada <div id="map">.
 * 2. Memuat layer geojson kecamatan (kecamatan.json) dan menambahkan
 *    popup berisi jumlah penduduk & jamaah haji.
 * 3. Memuat marker layanan kesehatan (health_services.json).
 * 4. Menangani ukuran ulang peta (invalidateSize) agar tidak error.
 *
 * Catatan :
 *  - Pastikan file kecamatan.json berisi GeoJSON dengan setiap fitur memiliki
 *    properties: nama_kecamatan, jumlah_penduduk, jumlah_jamaah_haji.
 *  - Pastikan Anda menyediakan endpoint health_services.json
 *    yang mereturn array objek { id, name, address, latitude, longitude }.
 *  - Sertakan <script src="map.js" defer></script> di akhir <body>.
 */

(function () {
    'use strict';
  
    document.addEventListener('DOMContentLoaded', function () {
      // 1️⃣  Inisialisasi peta
      const map = L.map('map', {
        center: [-7.434, 109.246], // Titik tengah Banyumas
        zoom: 11,
        zoomControl: true
      });
  
      // 2️⃣  Tambahkan tile layer OpenStreetMap
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(map);
  
      /* ----------------------------------------------------------
       * 3️⃣  Muat GeoJSON kecamatan + data penduduk & haji
       * --------------------------------------------------------*/
      fetch('get_kecamatan.php')
        .then(resp => resp.json())
        .then(geojson => {
          const districtLayer = L.geoJSON(geojson, {
            style: () => ({ color: '#3388ff', weight: 1, fillOpacity: 0.2 }),
            onEachFeature: (feature, layer) => {
              const p = feature.properties || {};
              const popupContent = `
                <strong>${p.nama_kecamatan || 'Kecamatan Tanpa Nama'}</strong><br/>
                Jumlah Penduduk: ${p.jumlah_penduduk?.toLocaleString('id-ID') || 'N/A'}<br/>
                Jamaah Haji: ${p.jumlah_jamaah_haji?.toLocaleString('id-ID') || 'N/A'}`;
              layer.bindPopup(popupContent);
            }
          }).addTo(map);
  
          // Sesuaikan view agar seluruh kecamatan terlihat
          map.fitBounds(districtLayer.getBounds());
  
          // Tangani masalah sizing setelah layer ditambahkan
          setTimeout(() => map.invalidateSize(), 200);
        })
        .catch(err => console.error('Gagal memuat get_kecamatan.php', err));
  
      /* ----------------------------------------------------------
       * 4️⃣  Muat marker layanan kesehatan
       * --------------------------------------------------------*/
      fetch('get_health_services.php')
        .then(resp => resp.json())
        .then(data => {
          data.forEach(item => {
            if (!item.latitude || !item.longitude) return;
  
            const marker = L.marker([item.latitude, item.longitude], {
              title: item.name
            }).addTo(map);
  
            marker.bindPopup(`
              <strong>${item.name}</strong><br/>
              ${item.address || ''}
            `);
          });
        })
        .catch(err => console.error('Gagal memuat get_health_services.php', err));
  
      /* ----------------------------------------------------------
       * 5️⃣  Perbaiki ukuran peta saat jendela diubah ukurannya
       * --------------------------------------------------------*/
      window.addEventListener('resize', () => {
        map.invalidateSize();
      });
  
      // Opsional: Ekspor map ke global jika ingin diakses dari luar
      window.banyumasMap = map;
    });
  })();
  