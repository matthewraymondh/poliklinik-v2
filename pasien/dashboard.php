<?php
session_start();
include '../config/koneksi.php';

// Check if logged in as Patient
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pasien') {
    header('Location: login.php');
    exit;
}

// Fetch available poli
$poli_query = "SELECT * FROM poli";
$poli_result = mysqli_query($conn, $poli_query);

// Fetch patient's appointments
$appointments_query = "SELECT daftar_poli.*, poli.nama_poli, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai 
                        FROM daftar_poli
                        JOIN jadwal_periksa ON daftar_poli.id_jadwal = jadwal_periksa.id
                        JOIN poli ON jadwal_periksa.id_dokter = poli.id
                        WHERE daftar_poli.id_pasien = " . $_SESSION['user_id'];
$appointments_result = mysqli_query($conn, $appointments_query);

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $poli_id = $_POST['poli_id'];
    $complaint = $_POST['complaint'];
    $jadwal_id = $_POST['jadwal_id'];

    // Get the next queue number for the selected schedule
    $queue_query = "SELECT COALESCE(MAX(no_antrian), 0) + 1 AS next_queue 
                    FROM daftar_poli 
                    WHERE id_jadwal = $jadwal_id";
    $queue_result = mysqli_query($conn, $queue_query);
    $next_queue = mysqli_fetch_assoc($queue_result)['next_queue'];

    // Insert into daftar_poli
    $insert_query = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) 
                     VALUES (" . $_SESSION['user_id'] . ", $jadwal_id, '$complaint', $next_queue)";
    if (mysqli_query($conn, $insert_query)) {
        header('Location: dashboard.php?success=1');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasien Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function openRegisterModal(poliId, poliName) {
            document.getElementById('registerModal').classList.remove('hidden');
            document.getElementById('poliId').value = poliId;
            document.getElementById('modalTitle').innerText = `Register for ${poliName}`;

            // Fetch schedules for the selected poli
            fetch(`dashboard.php?fetch_jadwal=1&poli_id=${poliId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const jadwalSelect = document.getElementById('jadwal_id');
                    jadwalSelect.innerHTML = ''; // Clear previous options

                    if (data.error) {
                        console.error('Error fetching jadwal:', data.error);
                        jadwalSelect.innerHTML = '<option value="" disabled>No schedules available</option>';
                        return;
                    }

                    if (data.length === 0) {
                        jadwalSelect.innerHTML = '<option value="" disabled>No schedules available</option>';
                        return;
                    }

                    data.forEach(jadwal => {
                        const option = document.createElement('option');
                        option.value = jadwal.id;
                        option.textContent = `${jadwal.hari}, ${jadwal.jam_mulai} - ${jadwal.jam_selesai} (Dr. ${jadwal.dokter_name})`;
                        jadwalSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching jadwal:', error);
                    const jadwalSelect = document.getElementById('jadwal_id');
                    jadwalSelect.innerHTML = '<option value="" disabled>Error fetching schedules</option>';
                });
        }



        function closeRegisterModal() {
            document.getElementById('registerModal').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-700 text-white min-h-screen p-6">
            <h1 class="text-2xl font-bold mb-6">Pasien Dashboard</h1>
            <ul>
                <li><a href="dashboard.php" class="text-white block py-2">Dashboard</a></li>
                <li><a href="../public/logout.php" class="text-white block py-2">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Available Poli -->
            <h2 class="text-3xl font-bold mb-6">Available Poli</h2>
            <table class="min-w-full bg-white border border-gray-300 mb-8">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Poli</th>
                        <th class="px-4 py-2">Description</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($poli = mysqli_fetch_assoc($poli_result)) : ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($poli['nama_poli']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($poli['keterangan']); ?></td>
                            <td class="px-4 py-2">
                                <button onclick="openRegisterModal(<?php echo $poli['id']; ?>, '<?php echo htmlspecialchars($poli['nama_poli']); ?>')" class="text-blue-600 hover:underline">Register</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Your Appointments -->
            <h2 class="text-3xl font-bold mt-8 mb-6">Your Appointments</h2>
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">Poli</th>
                        <th class="px-4 py-2">Complaint</th>
                        <th class="px-4 py-2">Schedule</th>
                        <th class="px-4 py-2">Queue Number</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = mysqli_fetch_assoc($appointments_result)) : ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appointment['nama_poli']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appointment['keluhan']); ?></td>
                            <td class="px-4 py-2">
                                <?php echo htmlspecialchars($appointment['hari']) . ', ' . htmlspecialchars($appointment['jam_mulai']) . ' - ' . htmlspecialchars($appointment['jam_selesai']); ?>
                            </td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($appointment['no_antrian']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
            <h2 id="modalTitle" class="text-2xl font-bold mb-4">Register for Poli</h2>
            <form id="registerForm" action="dashboard.php" method="POST">
                <input type="hidden" name="poli_id" id="poliId">

                <!-- Dropdown for selecting jadwal_periksa -->
                <div class="mb-4">
                    <label for="jadwal_id" class="block text-sm font-medium text-gray-700 mb-1">Select Schedule</label>
                    <select name="jadwal_id" id="jadwal_id" class="w-full p-2 border border-gray-300 rounded" required></select>
                </div>

                <div class="mb-4">
                    <label for="complaint" class="block text-sm font-medium text-gray-700 mb-1">Complaint</label>
                    <textarea name="complaint" id="complaint" rows="4" class="w-full p-2 border border-gray-300 rounded" required></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="closeRegisterModal()" class="mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
if (isset($_GET['fetch_jadwal']) && isset($_GET['poli_id'])) {
    header('Content-Type: application/json');
    $poli_id = intval($_GET['poli_id']); // Sanitasi input untuk keamanan

    // Query untuk mendapatkan jadwal berdasarkan poli_id
    $jadwal_query = "
        SELECT jadwal_periksa.id, jadwal_periksa.hari, jadwal_periksa.jam_mulai, jadwal_periksa.jam_selesai, dokter.nama AS dokter_name
        FROM jadwal_periksa
        JOIN dokter ON jadwal_periksa.id_dokter = dokter.id
        WHERE dokter.id_poli = $poli_id
    ";

    $jadwal_result = mysqli_query($conn, $jadwal_query);

    if ($jadwal_result) {
        $jadwal_data = [];
        while ($jadwal = mysqli_fetch_assoc($jadwal_result)) {
            $jadwal_data[] = $jadwal;
        }

        // Kirim data jadwal dalam format JSON
        echo json_encode($jadwal_data);
    } else {
        // Tangani kesalahan query
        echo json_encode(['error' => 'Failed to fetch schedules: ' . mysqli_error($conn)]);
    }
    exit;
} else {
    // Tangani jika parameter tidak lengkap
    echo json_encode(['error' => 'Invalid request: Missing poli_id']);
    exit;
}
?>

