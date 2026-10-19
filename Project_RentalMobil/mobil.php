<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_mobil'];
    $plat = $_POST['plat_nomor'];
    $harga = $_POST['harga_sewa'];
    // TAMBAHAN: Default status saat mobil baru disimpan adalah 'Tersedia'
    mysqli_query($koneksi, "INSERT INTO mobil (nama_mobil, plat_nomor, harga_sewa, status) VALUES ('$nama', '$plat', '$harga', 'Tersedia')");
    header("Location: mobil.php");
    exit();
}

if (isset($_POST['update'])) {
    $id = $_POST['id_mobil'];
    $nama = $_POST['nama_mobil'];
    $plat = $_POST['plat_nomor'];
    $harga = $_POST['harga_sewa'];
    mysqli_query($koneksi, "UPDATE mobil SET nama_mobil='$nama', plat_nomor='$plat', harga_sewa='$harga' WHERE id_mobil='$id'");
    header("Location: mobil.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM mobil WHERE id_mobil='$id'");
    header("Location: mobil.php");
    exit();
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM mobil WHERE id_mobil='$id'");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Mobil</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f0f2f5; padding: 30px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        .nav { margin-bottom: 20px; }
        .nav a { color: #2b72d8; text-decoration: none; font-weight: 600; font-size: 14px; display: inline-block; transition: color 0.2s ease; }
        .nav a:hover { color: #1a51a4; text-decoration: underline; }

        h2 { margin-bottom: 15px; color: #1a1a1a; }
        .form-control { width: 100%; padding: 12px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 6px; }
        .btn { padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; color: white; font-weight: bold; }
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
        .badge { padding: 4px 8px; border-radius: 4px; color: white; font-size: 12px; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav">
        <a href="index.php">&larr; Kembali ke Dashboard</a>
    </div>

    <h2><?= $edit_data ? 'Edit Data Mobil' : 'Tambah Data Mobil' ?></h2>
    <form action="mobil.php" method="post">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_mobil" value="<?= $edit_data['id_mobil'] ?>">
        <?php endif; ?>
        <input type="text" name="nama_mobil" placeholder="Nama Mobil" class="form-control" value="<?= $edit_data['nama_mobil'] ?? '' ?>" required>
        <input type="text" name="plat_nomor" placeholder="Plat Nomor" class="form-control" value="<?= $edit_data['plat_nomor'] ?? '' ?>" required>
        <input type="number" name="harga_sewa" placeholder="Harga Sewa / Hari (Rp)" class="form-control" value="<?= $edit_data['harga_sewa'] ?? '' ?>" required>
        <button type="submit" name="<?= $edit_data ? 'update' : 'simpan' ?>" class="btn <?= $edit_data ? 'btn-success' : 'btn-primary' ?>">
            <?= $edit_data ? 'Update Mobil' : 'Simpan Mobil' ?>
        </button>
    </form>

    <h2 style="margin-top: 30px;">Daftar Mobil</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama Mobil</th>
            <th>Plat Nomor</th>
            <th>Harga/Hari</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php
        $data = mysqli_query($koneksi, "SELECT * FROM mobil");
        while ($row = mysqli_fetch_assoc($data)):
            $status_mobil = isset($row['status']) ? $row['status'] : 'Tersedia';
            $badge_class = ($status_mobil == 'Disewa') ? 'bg-danger' : 'bg-success';
        ?>
        <tr>
            <td><?= $row['id_mobil'] ?></td>
            <td><?= $row['nama_mobil'] ?></td>
            <td><?= $row['plat_nomor'] ?></td>
            <td>Rp <?= number_format($row['harga_sewa'], 0, ',', '.') ?></td>
            <td><span class="badge <?= $badge_class ?>"><?= $status_mobil ?></span></td>
            <td class="action-links">
                <a href="mobil.php?edit=<?= $row['id_mobil'] ?>" class="edit">Edit</a> | 
                <a href="mobil.php?hapus=<?= $row['id_mobil'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus mobil ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>