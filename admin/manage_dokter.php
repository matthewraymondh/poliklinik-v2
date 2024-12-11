<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $id_poli = $_POST['id_poli'];

    $query = "INSERT INTO dokter (nama, alamat, no_hp, id_poli) VALUES ('$nama', '$alamat', '$no_hp', '$id_poli')";
    mysqli_query($conn, $query);
    header('Location: manage_dokter.php');
}

$dokters = mysqli_query($conn, "SELECT d.*, p.nama_poli FROM dokter d JOIN poli p ON d.id_poli = p.id");
$polis = mysqli_query($conn, "SELECT * FROM poli");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-2xl font-bold mb-4">Manajemen Dokter</h1>
    <form action="" method="POST" class="mb-6">
        <input type="text" name="nama" placeholder="Nama Dokter" class="p-2 border rounded mb-2">
        <input type="text" name="alamat" placeholder="Alamat" class="p-2 border rounded mb-2">
        <input type="text" name="no_hp" placeholder="Nomor HP" class="p-2 border rounded mb-2">
        <select name="id_poli" class="p-2 border rounded mb-2">
            <?php while ($poli = mysqli_fetch_assoc($polis)): ?>
                <option value="<?php echo $poli['id']; ?>"><?php echo $poli['nama_poli']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="bg-blue-500 text-white p-2 rounded">Tambah Dokter</button>
    </form>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Alamat</th>
                <th class="border p-2">No HP</th>
                <th class="border p-2">Poli</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($dokter = mysqli_fetch_assoc($dokters)): ?>
                <tr>
                    <td class="border p-2"><?php echo $dokter['id']; ?></td>
                    <td class="border p-2"><?php echo $dokter['nama']; ?></td>
                    <td class="border p-2"><?php echo $dokter['alamat']; ?></td>
                    <td class="border p-2"><?php echo $dokter['no_hp']; ?></td>
                    <td class="border p-2"><?php echo $dokter['nama_poli']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
