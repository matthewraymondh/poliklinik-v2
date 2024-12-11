<?php
session_start();
include '../config/koneksi.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } elseif ($user['role'] === 'dokter') {
                header('Location: ../dokter/dashboard.php');
            } elseif ($user['role'] === 'pasien') {
                header('Location: ../pasien/dashboard.php');
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const passwordToggle = document.getElementById('password-toggle');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordToggle.innerText = '🙈';
            } else {
                passwordField.type = 'password';
                passwordToggle.innerText = '👁️';
            }
        }
    </script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded shadow-lg w-full max-w-sm">
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan username" required>
            </div>

            <div class="mb-4 relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" class="w-full p-2 border border-gray-300 rounded" placeholder="Masukkan password" required>
                <button type="button" id="password-toggle" class="absolute right-2 top-8 text-gray-600" onclick="togglePassword()">👁️</button>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                Login
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-4">
            Belum punya akun? <a href="register.php" class="text-blue-500">Daftar sekarang</a>.
        </p>
    </div>
</body>
</html>
