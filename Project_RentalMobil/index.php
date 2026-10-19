<?php
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Mobil</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f0f2f5; padding: 40px 20px; }
        .container { max-width: 800px; margin: 0 auto; text-align: center; }
        h1 { color: #1a1a1a; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .card { background: #fff; padding: 30px 20px; border-radius: 12px; text-decoration: none; color: #333; box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.12); }
        .card h3 { color: #2b72d8; margin-bottom: 8px; }
        .card p { font-size: 13px; color: #777; margin: 0; }
        .info-box { margin-top: 30px; background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); text-align: left; }
        .info-box h3 { color: #2b72d8; margin-bottom: 10px; font-size: 18px; }
        .info-box p { color: #333; font-size: 15px; margin: 0; }
    </style>
</head>
<body>

<div class="container">
    <h1>Rental Mobil</h1>


    <div class="grid">
        <a href="mobil.php" class="card">
            <h3>Data Mobil</h3>
            <p>Kelola data kendaraan</p>
        </a>
        <a href="pelanggan.php" class="card">
            <h3>Data Pelanggan</h3>
            <p>Kelola data penyewa</p>
        </a>
        <a href="sewa.php" class="card">
            <h3>Transaksi Sewa</h3>
            <p>Kelola proses rental</p>
        </a>
        <a href="rekap.php" class="card">
            <h3>Rekap Bulanan</h3>
            <p>Lihat laporan pendapatan</p>
        </a>
    </div>


    <div class="info-box">
        <h3>Mobil Paling Sering Disewa</h3>
        <?php
        $query_populer = mysqli_query($koneksi, "SELECT mobil.nama_mobil, COUNT(sewa.id_mobil) as total_sewa 
                                                 FROM sewa 
                                                 JOIN mobil ON sewa.id_mobil = mobil.id_mobil 
                                                 GROUP BY sewa.id_mobil 
                                                 ORDER BY total_sewa DESC 
                                                 LIMIT 1");
        $populer = mysqli_fetch_assoc($query_populer);

        if ($populer) {
            echo "<p>Jenis mobil yang paling sering disewa adalah <b>{$populer['nama_mobil']}</b>.</p>";
        } else {
            echo "<p style='color: #777;'>Belum ada data transaksi penyewaan.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>