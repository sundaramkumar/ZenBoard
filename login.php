<?php
require_once 'db/dbconn.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['userId'] = $user['id'];
        $_SESSION['userName'] = $user['username'];
        $_SESSION['userRole'] = $user['role'];
        $_SESSION['lastLogin'] = $user['lastlogin'];

        $stmt = $pdo->prepare("UPDATE users SET loginip = ?, lastLogin = ? WHERE id = ?");
        $stmt->execute([$_SERVER['REMOTE_ADDR'], date('Y-m-d H:i:s'), $user['id']]);

        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <link href="./css/style.css" rel="stylesheet">

</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
      <div class="text-center">
        <div class="inline-block"><img src="./images/logo.png" style="width: 50px; height: 50px;"/></div>
        <h1 class="inline-block antialiased heading">ZenBoard</h1>
        <h4 class="antialiased subtitle text-purple-700">A calm and organized approach to task management</h4>
      </div>
        <!-- <h1 class="text-2xl font-bold mb-6 text-center">ZenBoard</h1> -->
        <h1 class="text-2xl font-bold mb-6 text-center">Login</h1>
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="login.php" class="space-y-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Login</button>
        </form>
    </div>
</body>
</html>
