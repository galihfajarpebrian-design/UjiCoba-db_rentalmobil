<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_pelanggan'];
    $hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    mysqli_query($koneksi, "INSERT INTO pelanggan (nama_pelanggan, no_hp, alamat) VALUES ('$nama', '$hp', '$alamat')");
    header("Location: pelanggan.php");
    exit();
}

if (isset($_POST['update'])) {
    $id = $_POST['id_pelanggan'];
    $nama = $_POST['nama_pelanggan'];
    $hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    mysqli_query($koneksi, "UPDATE pelanggan SET nama_pelanggan='$nama', no_hp='$hp', alamat='$alamat' WHERE id_pelanggan='$id'");
    header("Location: pelanggan.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan='$id'");
    header("Location: pelanggan.php");
    exit();
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $res = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan='$id'");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Pelanggan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f0f2f5; padding: 30px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
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
    </style>
</head>
<body>

<div class="container">
    <div class="nav">
        <a href="index.php">&larr; Kembali ke Dashboard</a>
    </div>

    <h2><?= $edit_data ? 'Edit Data Pelanggan' : 'Tambah Data Pelanggan' ?></h2>
    <form action="pelanggan.php" method="post">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_pelanggan" value="<?= $edit_data['id_pelanggan'] ?>">
        <?php endif; ?>
        <input type="text" name="nama_pelanggan" placeholder="Nama Lengkap" class="form-control" value="<?= $edit_data['nama_pelanggan'] ?? '' ?>" required>
        <input type="text" name="no_hp" placeholder="Nomor Telepon/HP" class="form-control" value="<?= $edit_data['no_hp'] ?? '' ?>" required>
        <textarea name="alamat" placeholder="Alamat Lengkap" class="form-control" required><?= $edit_data['alamat'] ?? '' ?></textarea>
        <button type="submit" name="<?= $edit_data ? 'update' : 'simpan' ?>" class="btn <?= $edit_data ? 'btn-success' : 'btn-primary' ?>">
            <?= $edit_data ? 'Update Pelanggan' : 'Simpan Pelanggan' ?>
        </button>
    </form>

    <h2 style="margin-top: 30px;">Daftar Pelanggan</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>No HP</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
        <?php
        $data = mysqli_query($koneksi, "SELECT * FROM pelanggan");
        while ($row = mysqli_fetch_assoc($data)):
        ?>
        <tr>
            <td><?= $row['id_pelanggan'] ?></td>
            <td><?= $row['nama_pelanggan'] ?></td>
            <td><?= $row['no_hp'] ?></td>
            <td><?= $row['alamat'] ?></td>
            <td class="action-links">
                <a href="pelanggan.php?edit=<?= $row['id_pelanggan'] ?>" class="edit">Edit</a> | 
                <a href="pelanggan.php?hapus=<?= $row['id_pelanggan'] ?>" class="delete" onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>