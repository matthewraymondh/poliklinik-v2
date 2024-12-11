<?php
include '../config/koneksi.php';
$id_pasien = 1; // contoh ID pasien yang login

$riwayats = mysqli_query($conn, "
    SELECT p.*, d.nama AS nama_dokter, poli.nama_poli
    FROM periksa p
    JOIN daftar_poli dp ON p.id_daftar_poli = dp.id
    JOIN dokter d ON dp.id_poli = d.id_poli
    JOIN poli ON d.id_poli = poli.id
    WHERE dp.id_pasien = $id_pasien
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-2xl font-bold mb-4">Riwayat Pemeriksaan</h1>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border p-2">Tanggal</th>
                <th class="border p-2">Dokter</th>
                <th class="border p-2">Poli</th>
                <th class="border p-2">Catatan</th>
                <th class="border p-2">Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($riwayat = mysqli_fetch_assoc($riwayats)): ?>
                <tr>
                    <td class="border p-2"><?php echo $riwayat['tgl_periksa']; ?></td>
                    <td class="border p-2"><?php echo $riwayat['nama_dokter']; ?></td>
                    <td class="border p-2"><?php echo $riwayat['nama_poli']; ?></td>
                    <td class="border p-2"><?php echo $riwayat['catatan']; ?></td>
                    <td class="border p-2"><?php echo $riwayat['biaya_periksa']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
