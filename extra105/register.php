<?php
include('db.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザー名の重複チェック
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $error = 'このユーザー名は既に使用されています。';
    } else {
        // ユーザーを追加
        $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
        if ($conn->query($sql) === TRUE) {
            header('Location: login.php');
            exit();
        } else {
            $error = 'アカウント作成に失敗しました。';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>アカウント作成</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">アカウント作成</h2>
        <form method="POST" action="register.php">
            <div class="mb-4">
                <input type="text" name="username" placeholder="ユーザー名" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <input type="password" name="password" placeholder="パスワード" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <input type="submit" value="登録" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
            </div>
            <?php if ($error): ?>
                <p class="text-red-500 text-center"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p class="text-center mt-4"><a href="login.php" class="text-blue-500 hover:underline">ログインページへ</a></p>
    </div>
</body>
</html>