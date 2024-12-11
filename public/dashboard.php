<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="p-8">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p>Selamat datang, <?php echo $user['username']; ?>!</p>
        <a href="logout.php" class="bg-red-500 text-white p-2 rounded mt-4 inline-block">Logout</a>
    </div>
</body>
</html>
