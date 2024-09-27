<?php
session_start();
include('db.php');

// ログインフォームが送信されたとき
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ユーザーが存在するかどうかを確認
    $sql = "SELECT username, password, is_admin FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // パスワードが一致しているか確認
        if ($row['password'] === $password) {
            // 管理者かどうか確認
            if ($row['is_admin'] == 1) {
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = true;
                header('Location: admin.php'); // 管理ページへリダイレクト
                exit();
            } else {
                $error_message = "アクセス権限がありません。";
            }
        } else {
            $error_message = "パスワードが間違っています。";
        }
    } else {
        $error_message = "ユーザー名が存在しません。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン</title>
</head>
<body>
    <h1>管理者ログイン</h1>

    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="admin_login.php" method="POST">
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required><br><br>

        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="ログイン">
    </form>
</body>
</html>
