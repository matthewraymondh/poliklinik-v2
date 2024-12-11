<?php
include '../config/koneksi.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $ktp = trim($_POST['ktp']);
    $role = 'pasien'; // Fixed role to 'pasien'

    // Input validation
    if (empty($username) || empty($password) || empty($name) || empty($address) || empty($phone) || empty($ktp)) {
        $message = "Semua field harus diisi!";
    } elseif (strlen($password) < 6) {
        $message = "Password harus minimal 6 karakter!";
    } else {
        // Check if the username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Username sudah digunakan!";
        } else {
            // Generate RM number (e.g., format: RMYYYYMMDDXXXX)
            $currentDate = date('Ymd');
            $rmQuery = "SELECT COUNT(*) AS total FROM pasien WHERE no_rm LIKE 'RM$currentDate%';";
            $rmResult = mysqli_query($conn, $rmQuery);
            $count = mysqli_fetch_assoc($rmResult)['total'] + 1;
            $nomor_rm = "RM" . $currentDate . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Begin transaction
            mysqli_autocommit($conn, false);

            try {
                // Insert new user into users table
                $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $username, $hashedPassword, $role);
                $stmt->execute();

                // Get the inserted user ID
                $user_id = $conn->insert_id;

                // Insert patient details into pasien table
                $stmt = $conn->prepare("INSERT INTO pasien (id, no_rm, nama, alamat, no_hp, no_ktp) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $user_id, $nomor_rm, $name, $address, $phone, $ktp);
                $stmt->execute();

                // Commit transaction
                mysqli_commit($conn);

                $message = "Registrasi berhasil! Silakan login.";
                header('Location: login.php');
                exit;
            } catch (Exception $e) {
                // Rollback transaction on failure
                mysqli_rollback($conn);
                $message = "Gagal mendaftarkan user. Error: " . $e->getMessage();
            } finally {
                mysqli_autocommit($conn, true);
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Pasien</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-lg w-full max-w-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Register Pasien</h1>

        <?php if ($message): ?>
            <div class="bg-<?php echo strpos($message, 'berhasil') !== false ? 'green' : 'red'; ?>-100 border border-<?php echo strpos($message, 'berhasil') !== false ? 'green' : 'red'; ?>-400 text-<?php echo strpos($message, 'berhasil') !== false ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input type="text" name="name" id="name" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan nama" required>
            </div>

            <div class="mb-4">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <input type="text" name="address" id="address" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan alamat" required>
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor HP</label>
                <input type="text" name="phone" id="phone" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan nomor HP" required>
            </div>

            <div class="mb-4">
                <label for="ktp" class="block text-sm font-medium text-gray-700 mb-1">Nomor KTP</label>
                <input type="text" name="ktp" id="ktp" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan nomor KTP" required>
            </div>

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan username" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Register
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Sudah punya akun? <a href="login.php" class="text-blue-500">Login sekarang</a>.
        </p>
    </div>
</body>
</html>
