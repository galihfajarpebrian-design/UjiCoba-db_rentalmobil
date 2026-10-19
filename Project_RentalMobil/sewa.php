<?php
include 'koneksi.php';

$pesan_error = "";

if (isset($_POST['simpan'])) {
    $id_mobil = $_POST['id_mobil'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tgl_sewa = $_POST['tgl_sewa'];
    $lama_sewa = $_POST['lama_sewa'];

    // Cek ulang status mobil di database untuk keamanan ganda
    $cek_status = mysqli_query($koneksi, "SELECT status, nama_mobil FROM mobil WHERE id_mobil='$id_mobil'");
    $data_cek = mysqli_fetch_assoc($cek_status);

    if ($data_cek['status'] == 'Disewa') {
        $pesan_error = "Peringatan: Mobil <b>" . $data_cek['nama_mobil'] . "</b> masih belum tersedia (sedang disewa)!";
    } else {
        // Ambil harga sewa mobil
        $query_mobil = mysqli_query($koneksi, "SELECT harga_sewa FROM mobil WHERE id_mobil='$id_mobil'");
        $data_mobil = mysqli_fetch_assoc($query_mobil);
        $total_bayar = $data_mobil['harga_sewa'] * $lama_sewa;

        // Simpan transaksi
        mysqli_query($koneksi, "INSERT INTO sewa (id_mobil, id_pelanggan, tgl_sewa, lama_sewa, total_bayar) VALUES ('$id_mobil', '$id_pelanggan', '$tgl_sewa', '$lama_sewa', '$total_bayar')");
        
        // Ubah status mobil jadi Disewa secara paksa
        mysqli_query($koneksi, "UPDATE mobil SET status='Disewa' WHERE id_mobil='$id_mobil'");

        header("Location: sewa.php");
        exit();
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id_sewa'];
    $id_mobil_baru = $_POST['id_mobil'];
    $id_pelanggan = $_POST['id_pelanggan'];
    $tgl_sewa = $_POST['tgl_sewa'];
    $lama_sewa = $_POST['lama_sewa'];

    $q_lama = mysqli_query($koneksi, "SELECT id_mobil FROM sewa WHERE id_sewa='$id'");
    $d_lama = mysqli_fetch_assoc($q_lama);
    $id_mobil_lama = $d_lama['id_mobil'];

    $query_mobil = mysqli_query($koneksi, "SELECT harga_sewa FROM mobil WHERE id_mobil='$id_mobil_baru'");
    $data_mobil = mysqli_fetch_assoc($query_mobil);
    $total_bayar = $data_mobil['harga_sewa'] * $lama_sewa;

    mysqli_query($koneksi, "UPDATE sewa SET id_mobil='$id_mobil_baru', id_pelanggan='$id_pelanggan', tgl_sewa='$tgl_sewa', lama_sewa='$lama_sewa', total_bayar='$total_bayar' WHERE id_sewa='$id'");

    if ($id_mobil_lama != $id_mobil_baru) {
        mysqli_query($koneksi, "UPDATE mobil SET status='Tersedia' WHERE id_mobil='$id_mobil_lama'");
        mysqli_query($koneksi, "UPDATE mobil SET status='Disewa' WHERE id_mobil='$id_mobil_baru'");
    }

    header("Location: sewa.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    $get_sewa = mysqli_query($koneksi, "SELECT id_mobil FROM sewa WHERE id_sewa='$id'");
    $data_sewa = mysqli_fetch_assoc($get_sewa);
    $id_mobil = $data_sewa['id_mobil'];

    mysqli_query($koneksi, "DELETE FROM sewa WHERE id_sewa='$id'");
    
    // Kembalikan status mobil jadi Tersedia
    mysqli_query($koneksi, "UPDATE mobil SET status='Tersedia' WHERE id_mobil='$id_mobil'");

    header("Location: sewa.php");
    exit();
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM sewa WHERE id_sewa='$id'");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Transaksi Sewa</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f0f2f5; padding: 30px; }
        .container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        .nav { margin-bottom: 20px; }
        .nav a { color: #2b72d8; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block; transition: color 0.2s ease; }
        .nav a:hover { color: #1a51a4; text-decoration: underline; }

        h2 { margin-bottom: 15px; color: #1a1a1a; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 6px; }
        .btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; color: white; font-weight: bold; width: 100%; }
        .btn-primary { background-color: #2b72d8; }
        .btn-success { background-color: #28a745; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #e1e1e1; padding: 12px; text-align: left; }
        th { background-color: #2b72d8; color: white; }
        .action-links a { text-decoration: none; font-weight: 600; margin-right: 8px; }
        .action-links a.edit { color: #2b72d8; }
        .action-links a.edit:hover { text-decoration: underline; }
        .action-links a.delete { color: #dc3545; }
        .action-links a.delete:hover { text-decoration: underline; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 12px; border: 1px solid #f5c6cb; border-radius: 6px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav">
        <a href="index.php">&larr; Kembali ke Dashboard</a>
    </div>

    <h2><?= $edit_data ? 'Edit Transaksi Sewa' : 'Tambah Transaksi Sewa' ?></h2>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert-danger"><?= $pesan_error ?></div>
    <?php endif; ?>

    <form action="sewa.php" method="post">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_sewa" value="<?= $edit_data['id_sewa'] ?>">
        <?php endif; ?>
        
        <select name="id_mobil" class="form-control" required>
            <option value="">-- Pilih Mobil --</option>
            <?php
            if ($edit_data) {
                $m = mysqli_query($koneksi, "SELECT * FROM mobil WHERE status='Tersedia' OR id_mobil='" . $edit_data['id_mobil'] . "'");
            } else {
                $m = mysqli_query($koneksi, "SELECT * FROM mobil WHERE status='Tersedia'");
            }

            while ($row = mysqli_fetch_assoc($m)):
                $sel = ($edit_data && $edit_data['id_mobil'] == $row['id_mobil']) ? 'selected' : '';
                echo "<option value='".$row['id_mobil']."' $sel>".$row['nama_mobil']." (Rp ".number_format($row['harga_sewa'])."/hari)</option>";
            endwhile;
            ?>
        </select>

        <select name="id_pelanggan" class="form-control" required>
            <option value="">-- Pilih Pelanggan --</option>
            <?php
            $p = mysqli_query($koneksi, "SELECT * FROM pelanggan");
            while ($row = mysqli_fetch_assoc($p)):
                $sel = ($edit_data && $edit_data['id_pelanggan'] == $row['id_pelanggan']) ? 'selected' : '';
                echo "<option value='".$row['id_pelanggan']."' $sel>".$row['nama_pelanggan']."</option>";
            endwhile;
            ?>
        </select>

        <input type="date" name="tgl_sewa" class="form-control" value="<?= $edit_data['tgl_sewa'] ?? '' ?>" required>
        <input type="number" name="lama_sewa" placeholder="Lama Sewa (Hari)" class="form-control" value="<?= $edit_data['lama_sewa'] ?? '' ?>" required>

        <button type="submit" name="<?= $edit_data ? 'update' : 'simpan' ?>" class="btn <?= $edit_data ? 'btn-success' : 'btn-primary' ?>">
            <?= $edit_data ? 'Update Transaksi' : 'Simpan Transaksi' ?>
        </button>
    </form>

    <h2 style="margin-top: 30px;">Daftar Transaksi Sewa</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Pelanggan</th>
            <th>Mobil</th>
            <th>Tanggal Sewa</th>
            <th>Lama</th>
            <th>Total Bayar</th>
            <th>Aksi</th>
        </tr>
        <?php
        $query_relasi = "SELECT sewa.*, mobil.nama_mobil, pelanggan.nama_pelanggan 
                         FROM sewa 
                         JOIN mobil ON sewa.id_mobil = mobil.id_mobil 
                         JOIN pelanggan ON sewa.id_pelanggan = pelanggan.id_pelanggan";
        $data = mysqli_query($koneksi, $query_relasi);
        while ($row = mysqli_fetch_assoc($data)):
        ?>
        <tr>
            <td><?= $row['id_sewa'] ?></td>
            <td><?= $row['nama_pelanggan'] ?></td>
            <td><?= $row['nama_mobil'] ?></td>
            <td><?= $row['tgl_sewa'] ?></td>
            <td><?= $row['lama_sewa'] ?> Hari</td>
            <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
            <td class="action-links">
                <a href="sewa.php?edit=<?= $row['id_sewa'] ?>" class="edit">Edit</a> | 
                <a href="sewa.php?hapus=<?= $row['id_sewa'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus transaksi ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>