<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$doctors_query = "SELECT * FROM dokter";
$doctors_result = mysqli_query($conn, $doctors_query);

$patients_query = "SELECT * FROM pasien";
$patients_result = mysqli_query($conn, $patients_query);

$poli_query = "SELECT * FROM poli";
$poli_result = mysqli_query($conn, $poli_query);

$schedules_query = "SELECT * FROM jadwal_periksa";
$schedules_result = mysqli_query($conn, $schedules_query);

$medications_query = "SELECT * FROM obat";
$medications_result = mysqli_query($conn, $medications_query);

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Operations
    if (isset($_POST['add_doctor'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $id_poli = (int)$_POST['id_poli'];

        $insert_query = "INSERT INTO dokter (nama, alamat, no_hp, id_poli) VALUES ('$nama', '$alamat', '$no_hp', $id_poli)";
        mysqli_query($conn, $insert_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['add_patient'])) {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_ktp = mysqli_real_escape_string($conn, $_POST['no_ktp']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

        $insert_query = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp) VALUES ('$nama', '$alamat', '$no_ktp', '$no_hp')";
        mysqli_query($conn, $insert_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['add_poli'])) {
        $nama_poli = mysqli_real_escape_string($conn, $_POST['nama_poli']);
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

        $insert_query = "INSERT INTO poli (nama_poli, keterangan) VALUES ('$nama_poli', '$keterangan')";
        mysqli_query($conn, $insert_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['add_schedule'])) {
        $id_dokter = (int)$_POST['id_dokter'];
        $hari = mysqli_real_escape_string($conn, $_POST['hari']);
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);

        $insert_query = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai) VALUES ($id_dokter, '$hari', '$jam_mulai', '$jam_selesai')";
        mysqli_query($conn, $insert_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['add_medication'])) {
        $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
        $kemasan = mysqli_real_escape_string($conn, $_POST['kemasan']);
        $harga = (int)$_POST['harga'];

        $insert_query = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES ('$nama_obat', '$kemasan', $harga)";
        mysqli_query($conn, $insert_query);
        header('Location: dashboard.php');
        exit;
    }

    // Update Operations
    if (isset($_POST['update_doctor'])) {
        $id = (int)$_POST['id'];
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $id_poli = (int)$_POST['id_poli'];

        $update_query = "UPDATE dokter SET nama = '$nama', alamat = '$alamat', no_hp = '$no_hp', id_poli = $id_poli WHERE id = $id";
        mysqli_query($conn, $update_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['update_patient'])) {
        $id = (int)$_POST['id'];
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        $no_ktp = mysqli_real_escape_string($conn, $_POST['no_ktp']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);

        $update_query = "UPDATE pasien SET nama = '$nama', alamat = '$alamat', no_ktp = '$no_ktp', no_hp = '$no_hp' WHERE id = $id";
        mysqli_query($conn, $update_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['update_poli'])) {
        $id = (int)$_POST['id'];
        $nama_poli = mysqli_real_escape_string($conn, $_POST['nama_poli']);
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);

        $update_query = "UPDATE poli SET nama_poli = '$nama_poli', keterangan = '$keterangan' WHERE id = $id";
        mysqli_query($conn, $update_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['update_schedule'])) {
        $id = (int)$_POST['id'];
        $id_dokter = (int)$_POST['id_dokter'];
        $hari = mysqli_real_escape_string($conn, $_POST['hari']);
        $jam_mulai = mysqli_real_escape_string($conn, $_POST['jam_mulai']);
        $jam_selesai = mysqli_real_escape_string($conn, $_POST['jam_selesai']);

        $update_query = "UPDATE jadwal_periksa SET id_dokter = $id_dokter, hari = '$hari', jam_mulai = '$jam_mulai', jam_selesai = '$jam_selesai' WHERE id = $id";
        mysqli_query($conn, $update_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['update_medication'])) {
        $id = (int)$_POST['id'];
        $nama_obat = mysqli_real_escape_string($conn, $_POST['nama_obat']);
        $kemasan = mysqli_real_escape_string($conn, $_POST['kemasan']);
        $harga = (int)$_POST['harga'];

        $update_query = "UPDATE obat SET nama_obat = '$nama_obat', kemasan = '$kemasan', harga = $harga WHERE id = $id";
        mysqli_query($conn, $update_query);
        header('Location: dashboard.php');
        exit;
    }

    // Delete Operations
    if (isset($_POST['delete_doctor'])) {
        $id = (int)$_POST['id'];

        $delete_query = "DELETE FROM dokter WHERE id = $id";
        mysqli_query($conn, $delete_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['delete_patient'])) {
        $id = (int)$_POST['id'];

        $delete_query = "DELETE FROM pasien WHERE id = $id";
        mysqli_query($conn, $delete_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['delete_poli'])) {
        $id = (int)$_POST['id'];

        $delete_query = "DELETE FROM poli WHERE id = $id";
        mysqli_query($conn, $delete_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['delete_schedule'])) {
        $id = (int)$_POST['id'];

        $delete_query = "DELETE FROM jadwal_periksa WHERE id = $id";
        mysqli_query($conn, $delete_query);
        header('Location: dashboard.php');
        exit;
    }

    if (isset($_POST['delete_medication'])) {
        $id = (int)$_POST['id'];

        $delete_query = "DELETE FROM obat WHERE id = $id";
        mysqli_query($conn, $delete_query);
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white min-h-screen">
            <div class="p-4 text-center font-bold text-xl border-b border-blue-700">
                Admin Dashboard
            </div>
            <nav class="mt-4">
                <a href="#doctors" class="block py-2 px-4 hover:bg-blue-700">Manage Doctors</a>
                <a href="#patients" class="block py-2 px-4 hover:bg-blue-700">Manage Patients</a>
                <a href="#poli" class="block py-2 px-4 hover:bg-blue-700">Manage Poli</a>
                <a href="#schedules" class="block py-2 px-4 hover:bg-blue-700">Manage Schedules</a>
                <a href="#medications" class="block py-2 px-4 hover:bg-blue-700">Manage Medications</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Manage Doctors Section -->
            <section id="doctors" class="mb-8 bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Manage Doctors</h2>
                <form method="POST" action="" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <input type="text" name="nama" placeholder="Name" required class="border p-2 rounded">
                    <input type="text" name="alamat" placeholder="Address" required class="border p-2 rounded">
                    <input type="text" name="no_hp" placeholder="Phone" required class="border p-2 rounded">
                    <select name="id_poli" required class="border p-2 rounded">
                        <?php while ($poli = mysqli_fetch_assoc($poli_result)) : ?>
                            <option value="<?php echo $poli['id']; ?>"><?php echo $poli['nama_poli']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button type="submit" name="add_doctor" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Doctor</button>
                </form>

                <table class="w-full mt-6 border">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Address</th>
                            <th class="border px-4 py-2">Phone</th>
                            <th class="border px-4 py-2">Poli</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($doctor = mysqli_fetch_assoc($doctors_result)) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $doctor['id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $doctor['nama']; ?></td>
                                <td class="border px-4 py-2"><?php echo $doctor['alamat']; ?></td>
                                <td class="border px-4 py-2"><?php echo $doctor['no_hp']; ?></td>
                                <td class="border px-4 py-2"><?php echo $doctor['id_poli']; ?></td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $doctor['id']; ?>">
                                        <button type="submit" name="delete_doctor" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Delete</button>
                                    </form>
                                    <button onclick="editDoctor(<?php echo $doctor['id']; ?>)" class="bg-yellow-600 text-white px-4 py-1 rounded hover:bg-yellow-700">Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Manage Patients Section -->
            <section id="patients" class="mb-8 bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Manage Patients</h2>
                <form method="POST" action="" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <input type="text" name="nama" placeholder="Name" required class="border p-2 rounded">
                    <input type="text" name="alamat" placeholder="Address" required class="border p-2 rounded">
                    <input type="text" name="no_ktp" placeholder="National ID" required class="border p-2 rounded">
                    <input type="text" name="no_hp" placeholder="Phone" required class="border p-2 rounded">
                    <button type="submit" name="add_patient" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Patient</button>
                </form>

                <table class="w-full mt-6 border">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Address</th>
                            <th class="border px-4 py-2">National ID</th>
                            <th class="border px-4 py-2">Phone</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($patient = mysqli_fetch_assoc($patients_result)) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $patient['id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $patient['nama']; ?></td>
                                <td class="border px-4 py-2"><?php echo $patient['alamat']; ?></td>
                                <td class="border px-4 py-2"><?php echo $patient['no_ktp']; ?></td>
                                <td class="border px-4 py-2"><?php echo $patient['no_hp']; ?></td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $patient['id']; ?>">
                                        <button type="submit" name="delete_patient" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Delete</button>
                                    </form>
                                    <button onclick="editPatient(<?php echo $patient['id']; ?>)" class="bg-yellow-600 text-white px-4 py-1 rounded hover:bg-yellow-700">Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Manage Poli Section -->
            <section id="poli" class="mb-8 bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Manage Poli</h2>
                <form method="POST" action="" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <input type="text" name="nama_poli" placeholder="Poli Name" required class="border p-2 rounded">
                    <input type="text" name="keterangan" placeholder="Description" required class="border p-2 rounded">
                    <button type="submit" name="add_poli" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Poli</button>
                </form>

                <table class="w-full mt-6 border">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Poli Name</th>
                            <th class="border px-4 py-2">Description</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($poli = mysqli_fetch_assoc($poli_result)) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $poli['id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $poli['nama_poli']; ?></td>
                                <td class="border px-4 py-2"><?php echo $poli['keterangan']; ?></td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $poli['id']; ?>">
                                        <button type="submit" name="delete_poli" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Delete</button>
                                    </form>
                                    <button onclick="editPoli(<?php echo $poli['id']; ?>)" class="bg-yellow-600 text-white px-4 py-1 rounded hover:bg-yellow-700">Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Manage Schedules Section -->
            <section id="schedules" class="mb-8 bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Manage Schedules</h2>
                <form method="POST" action="" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <select name="doctor_id" required class="border p-2 rounded">
                        <?php while ($doctor = mysqli_fetch_assoc($doctors_result)) : ?>
                            <option value="<?php echo $doctor['id']; ?>"><?php echo $doctor['nama']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="date" name="schedule_date" required class="border p-2 rounded">
                    <input type="time" name="schedule_time" required class="border p-2 rounded">
                    <button type="submit" name="add_schedule" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Schedule</button>
                </form>

                <table class="w-full mt-6 border">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Doctor</th>
                            <th class="border px-4 py-2">Date</th>
                            <th class="border px-4 py-2">Time</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($schedule = mysqli_fetch_assoc($schedules_result)) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $schedule['id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $schedule['doctor_id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $schedule['schedule_date']; ?></td>
                                <td class="border px-4 py-2"><?php echo $schedule['schedule_time']; ?></td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $schedule['id']; ?>">
                                        <button type="submit" name="delete_schedule" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Delete</button>
                                    </form>
                                    <button onclick="editSchedule(<?php echo $schedule['id']; ?>)" class="bg-yellow-600 text-white px-4 py-1 rounded hover:bg-yellow-700">Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>

            <!-- Manage Medications Section -->
            <section id="medications" class="mb-8 bg-white p-6 rounded shadow">
                <h2 class="text-2xl font-bold mb-4">Manage Medications</h2>
                <form method="POST" action="" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <input type="text" name="medication_name" placeholder="Medication Name" required class="border p-2 rounded">
                    <input type="text" name="description" placeholder="Description" required class="border p-2 rounded">
                    <input type="number" name="quantity" placeholder="Quantity" required class="border p-2 rounded">
                    <button type="submit" name="add_medication" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Medication</button>
                </form>

                <table class="w-full mt-6 border">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">Name</th>
                            <th class="border px-4 py-2">Description</th>
                            <th class="border px-4 py-2">Quantity</th>
                            <th class="border px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($medication = mysqli_fetch_assoc($medications_result)) : ?>
                            <tr>
                                <td class="border px-4 py-2"><?php echo $medication['id']; ?></td>
                                <td class="border px-4 py-2"><?php echo $medication['medication_name']; ?></td>
                                <td class="border px-4 py-2"><?php echo $medication['description']; ?></td>
                                <td class="border px-4 py-2"><?php echo $medication['quantity']; ?></td>
                                <td class="border px-4 py-2 flex gap-2">
                                    <form method="POST" action="">
                                        <input type="hidden" name="id" value="<?php echo $medication['id']; ?>">
                                        <button type="submit" name="delete_medication" class="bg-red-600 text-white px-4 py-1 rounded hover:bg-red-700">Delete</button>
                                    </form>
                                    <button onclick="editMedication(<?php echo $medication['id']; ?>)" class="bg-yellow-600 text-white px-4 py-1 rounded hover:bg-yellow-700">Edit</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
    <!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 id="editModalTitle" class="text-xl font-bold mb-4">Edit Item</h2>
        <form id="editForm" method="POST" action="">
            <input type="hidden" id="editId" name="id">
            
            <div id="editFields" class="space-y-4">
                <!-- Edit fields will be dynamically inserted here -->
            </div>
            
            <div class="flex justify-end gap-4 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Cancel</button>
                <button type="submit" name="edit_item" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add JavaScript functionality -->
<script>
    function openEditModal(entity, id, data) {
        // Set modal title
        document.getElementById('editModalTitle').textContent = `Edit ${entity}`;

        // Populate the form fields
        const fieldsContainer = document.getElementById('editFields');
        fieldsContainer.innerHTML = ''; // Clear previous fields

        // Insert fields based on data
        for (const key in data) {
            if (key !== 'id') {
                fieldsContainer.innerHTML += `
                    <div>
                        <label for="edit-${key}" class="block text-sm font-medium text-gray-700">${capitalize(key)}</label>
                        <input type="text" id="edit-${key}" name="${key}" value="${data[key]}" class="w-full border p-2 rounded">
                    </div>
                `;
            }
        }

        // Set hidden input for ID
        document.getElementById('editId').value = id;

        // Show the modal
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function capitalize(string) {
        return string.charAt(0).toUpperCase() + string.slice(1).replace('_', ' ');
    }

    // Example usage
    function editDoctor(id) {
        // Replace with actual data fetched from your backend
        const doctorData = {
            id: id,
            nama: "Dr. Example",
            alamat: "Example Address",
            no_hp: "123456789",
            id_poli: "1"
        };
        openEditModal('Doctor', id, doctorData);
    }

    function editPatient(id) {
        const patientData = {
            id: id,
            nama: "John Doe",
            alamat: "Example Address",
            no_ktp: "987654321",
            no_hp: "987654321"
        };
        openEditModal('Patient', id, patientData);
    }

    function editPoli(id) {
        const poliData = {
            id: id,
            nama_poli: "General",
            keterangan: "General description"
        };
        openEditModal('Poli', id, poliData);
    }

    function editSchedule(id) {
        const scheduleData = {
            id: id,
            doctor_id: "1",
            schedule_date: "2024-12-11",
            schedule_time: "09:00"
        };
        openEditModal('Schedule', id, scheduleData);
    }

    function editMedication(id) {
        const medicationData = {
            id: id,
            medication_name: "Paracetamol",
            description: "Pain reliever",
            quantity: "50"
        };
        openEditModal('Medication', id, medicationData);
    }
</script>

</body>
</html>

