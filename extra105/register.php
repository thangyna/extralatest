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
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h2>アカウント作成</h2>
    <form method="POST" action="register.php">
        <label>ユーザー名:</label><br>
        <input type="text" name="username" required><br><br>
        <label>パスワード:</label><br>
        <input type="password" name="password" required><br><br>
        <input type="submit" value="登録">
    </form>
    <p><?php echo $error; ?></p>
    <a href="login.php">ログインページへ</a>
</body>
</html>
