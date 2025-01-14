<?php
include('db.php');
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT username, admin FROM users WHERE username = ? AND password = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['admin'] = $user['admin'];
        header('Location: typing/typing_game.html'); // toppage.phpからgame.phpに変更
        exit();
    } else {
        $error = 'ユーザー名またはパスワードが間違っています。';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">ログイン</h2>
        <form method="POST" action="login.php">
            <div class="mb-4">
                <input type="text" name="username" placeholder="ユーザー名" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="パスワード" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <input type="submit" value="ログイン" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
            </div>
            <?php if ($error): ?>
                <p class="text-red-500 text-center"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p class="text-center mt-4"><a href="register.php" class="text-blue-500 hover:underline">アカウント作成</a></p>
    </div>
</body>
</html>