<?php
session_start();
include '../config/koneksi.php';

// Check if logged in as Doctor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header('Location: login.php');
    exit;
}

$doctor_id = $_SESSION['user_id'];

// Fetch doctor's data
$doctor_query = "SELECT * FROM dokter WHERE id = $doctor_id";
$doctor_result = mysqli_query($conn, $doctor_query);
$doctor = mysqli_fetch_assoc($doctor_result);

// Fetch doctor's schedule
$schedule_query = "SELECT * FROM jadwal_periksa WHERE id_dokter = $doctor_id";
$schedule_result = mysqli_query($conn, $schedule_query);

// Fetch patient appointments for the doctor
$appointments_query = "SELECT dp.*, p.nama AS nama_pasien FROM daftar_poli dp
                      JOIN pasien p ON dp.id_pasien = p.id
                      WHERE dp.id_jadwal IN (SELECT id FROM jadwal_periksa WHERE id_dokter = $doctor_id)";
$appointments_result = mysqli_query($conn, $appointments_query);

// Update doctor data
if (isset($_POST['update_doctor'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    $update_query = "UPDATE dokter SET nama = '$nama', alamat = '$alamat', no_hp = '$no_hp' WHERE id = $doctor_id";
    mysqli_query($conn, $update_query);
    header('Location: dashboard.php');
    exit;
}

// Add schedule
if (isset($_POST['add_schedule'])) {
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];

    $schedule_query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai) VALUES ($doctor_id, '$hari', '$jam_mulai', '$jam_selesai')";
    mysqli_query($conn, $schedule_query);
    header('Location: dashboard.php');
    exit;
}

// Perform examination
if (isset($_POST['examine_patient'])) {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $tgl_periksa = date('Y-m-d');
    $catatan = $_POST['catatan'];
    $biaya_periksa = $_POST['biaya_periksa'];

    $examination_query = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) VALUES ($id_daftar_poli, '$tgl_periksa', '$catatan', $biaya_periksa)";
    mysqli_query($conn, $examination_query);
    header('Location: dashboard.php');
    exit;
}

// Fetch patient history
$history_query = "SELECT p.nama AS nama_pasien, periksa.tgl_periksa, periksa.catatan, periksa.biaya_periksa FROM periksa
                  JOIN daftar_poli dp ON periksa.id_daftar_poli = dp.id
                  JOIN pasien p ON dp.id_pasien = p.id
                  WHERE dp.id_jadwal IN (SELECT id FROM jadwal_periksa WHERE id_dokter = $doctor_id)";
$history_result = mysqli_query($conn, $history_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokter Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-700 text-white min-h-screen p-6">
            <h1 class="text-2xl font-bold mb-6">Dokter Dashboard</h1>
            <ul>
                <li><a href="dashboard.php" class="text-white block py-2">Dashboard</a></li>
                <li><a href="../public/logout.php" class="text-white block py-2">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <h2 class="text-3xl font-bold mb-6">Your Profile</h2>
            <form method="POST">
                <label class="block">Name:</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($doctor['nama']); ?>" class="border p-2 w-full mb-4">

                <label class="block">Address:</label>
                <input type="text" name="alamat" value="<?php echo htmlspecialchars($doctor['alamat']); ?>" class="border p-2 w-full mb-4">

                <label class="block">Phone Number:</label>
                <input type="text" name="no_hp" value="<?php echo htmlspecialchars($doctor['no_hp']); ?>" class="border p-2 w-full mb-4">

                <button type="submit" name="update_doctor" class="bg-blue-500 text-white py-2 px-4">Update</button>
            </form>

            <h2 class="text-3xl font-bold my-6">Add Schedule</h2>
            <form method="POST">
                <label class="block">Day:</label>
                <select name="hari" class="border p-2 w-full mb-4">
                    <option value="Senin">Senin</option>
                    <option value="Selasa">Selasa</option>
                    <option value="Rabu">Rabu</option>
                    <option value="Kamis">Kamis</option>
                    <option value="Jumat">Jumat</option>
                    <option value="Sabtu">Sabtu</option>
                </select>

                <label class="block">Start Time:</label>
                <input type="time" name="jam_mulai" class="border p-2 w-full mb-4">

                <label class="block">End Time:</label>
                <input type="time" name="jam_selesai" class="border p-2 w-full mb-4">

                <button type="submit" name="add_schedule" class="bg-blue-500 text-white py-2 px-4">Add</button>
            </form>

            <h2 class="text-3xl font-bold my-6">Patient Appointments</h2>
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Patient</th>
                        <th class="px-4 py-2">Complaint</th>
                        <th class="px-4 py-2">Queue Number</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = mysqli_fetch_assoc($appointments_result)) : ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appointment['nama_pasien']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appointment['keluhan']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appointment['no_antrian']); ?></td>
                            <td class="px-4 py-2">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="id_daftar_poli" value="<?php echo $appointment['id']; ?>">
                                    <textarea name="catatan" placeholder="Notes" class="border p-2 w-full mb-2"></textarea>
                                    <input type="number" name="biaya_periksa" placeholder="Cost" class="border p-2 w-full mb-2">
                                    <button type="submit" name="examine_patient" class="bg-green-500 text-white py-2 px-4">Examine</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2 class="text-3xl font-bold my-6">Patient History</h2>
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Patient</th>
                        <th class="px-4 py-2">Date</th>
                        <th class="px-4 py-2">Notes</th>
                        <th class="px-4 py-2">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($history = mysqli_fetch_assoc($history_result)) : ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($history['nama_pasien']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($history['tgl_periksa']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($history['catatan']); ?></td>
                            <td class="px-4 py-2">Rp<?php echo number_format($history['biaya_periksa'], 0, ',', '.'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
