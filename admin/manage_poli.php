<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];

    $query = "INSERT INTO poli (nama_poli, keterangan) VALUES ('$nama_poli', '$keterangan')";
    mysqli_query($conn, $query);
    header('Location: manage_poli.php');
}

$polis = mysqli_query($conn, "SELECT * FROM poli");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-2xl font-bold mb-4">Manajemen Poli</h1>
    <form action="" method="POST" class="mb-6">
        <input type="text" name="nama_poli" placeholder="Nama Poli" class="p-2 border rounded mb-2">
        <textarea name="keterangan" placeholder="Keterangan" class="p-2 border rounded mb-2"></textarea>
        <button type="submit" class="bg-blue-500 text-white p-2 rounded">Tambah Poli</button>
    </form>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border p-2">ID</th>
                <th class="border p-2">Nama Poli</th>
                <th class="border p-2">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($poli = mysqli_fetch_assoc($polis)): ?>
                <tr>
                    <td class="border p-2"><?php echo $poli['id']; ?></td>
                    <td class="border p-2"><?php echo $poli['nama_poli']; ?></td>
                    <td class="border p-2"><?php echo $poli['keterangan']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
