<?php
include 'koneksi.php';

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Transaksi Bulanan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f0f2f5; padding: 30px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .nav { margin-bottom: 20px; }
        .nav a { color: #2b72d8; text-decoration: none; font-weight: 600; font-size: 14px; }
        .nav a:hover { text-decoration: underline; }
        h2 { margin-bottom: 15px; color: #1a1a1a; }
        .form-inline { display: flex; gap: 10px; margin-bottom: 20px; align-items: center; }
        .form-control { padding: 10px; border: 1px solid #ccc; border-radius: 6px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; color: white; font-weight: bold; background-color: #2b72d8; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #e1e1e1; padding: 12px; text-align: left; }
        th { background-color: #2b72d8; color: white; }
        .total-box { margin-top: 20px; padding: 15px; background: #e8f4fd; border-radius: 6px; font-weight: bold; color: #1a51a4; font-size: 16px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav">
        <a href="index.php">&larr; Kembali ke Dashboard</a>
    </div>

    <h2>Rekap Transaksi Per Bulan</h2>
    
    <form method="GET" class="form-inline">
        <select name="bulan" class="form-control">
            <?php
            $list_bulan = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            foreach ($list_bulan as $code => $name) {
                $sel = ($bulan == $code) ? 'selected' : '';
                echo "<option value='$code' $sel>$name</option>";
            }
            ?>
        </select>
        
        <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>" style="width: 100px;">
        <button type="submit" class="btn">Filter</button>
    </form>

    <table>
        <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Mobil</th>
            <th>Tanggal Sewa</th>
            <th>Lama</th>
            <th>Total Bayar</th>
        </tr>
        <?php
        $query = "SELECT sewa.*, mobil.nama_mobil, pelanggan.nama_pelanggan 
                  FROM sewa 
                  JOIN mobil ON sewa.id_mobil = mobil.id_mobil 
                  JOIN pelanggan ON sewa.id_pelanggan = pelanggan.id_pelanggan 
                  WHERE MONTH(tgl_sewa) = '$bulan' AND YEAR(tgl_sewa) = '$tahun'";
        
        $data = mysqli_query($koneksi, $query);
        $total_pendapatan = 0;
        
        if (mysqli_num_rows($data) > 0) {
            while ($row = mysqli_fetch_assoc($data)) {
                $total_pendapatan += $row['total_bayar'];
                echo "<tr>
                    <td>{$row['id_sewa']}</td>
                    <td>{$row['nama_pelanggan']}</td>
                    <td>{$row['nama_mobil']}</td>
                    <td>{$row['tgl_sewa']}</td>
                    <td>{$row['lama_sewa']} Hari</td>
                    <td>Rp " . number_format($row['total_bayar'], 0, ',', '.') . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6' align='center'>Tidak ada transaksi pada bulan ini.</td></tr>";
        }
        ?>
    </table>

    <div class="total-box">
        Total Pendapatan Bulan Ini: Rp <?= number_format($total_pendapatan, 0, ',', '.') ?>
    </div>
</div>

</body>
</html>