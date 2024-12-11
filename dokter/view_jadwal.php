<?php
include '../config/koneksi.php';

$jadwals = mysqli_query($conn, "
    SELECT j.*, d.nama AS nama_dokter, p.nama_poli
    FROM jadwal_periksa j
    JOIN dokter d ON j.id_dokter = d.id
    JOIN poli p ON d.id_poli = p.id
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <h1 class="text-2xl font-bold mb-4">Jadwal Periksa</h1>
    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr>
                <th class="border p-2">Hari</th>
                <th class="border p-2">Jam Mulai</th>
                <th class="border p-2">Jam Selesai</th>
                <th class="border p-2">Dokter</th>
                <th class="border p-2">Poli</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($jadwal = mysqli_fetch_assoc($jadwals)): ?>
                <tr>
                    <td class="border p-2"><?php echo $jadwal['hari']; ?></td>
                    <td class="border p-2"><?php echo $jadwal['jam_mulai']; ?></td>
                    <td class="border p-2"><?php echo $jadwal['jam_selesai']; ?></td>
                    <td class="border p-2"><?php echo $jadwal['nama_dokter']; ?></td>
                    <td class="border p-2"><?php echo $jadwal['nama_poli']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
