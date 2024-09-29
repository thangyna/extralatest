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
        header('Location: toppage.php');
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
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>ログイン</h2>
    <form method="POST" action="login.php">
        <label>ユーザー名:</label><br>
        <input type="text" name="username" required><br><br>
        <label>パスワード:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="ログイン">
    </form>
    <p><?php echo $error; ?></p>
    <a href="register.php">アカウント作成</a>
    <script>
        if ($_SERVER['HTTP_HOST'] != 'localhost') {
            console.log('running on localhost');
        }
    </script>
</body>
</html>