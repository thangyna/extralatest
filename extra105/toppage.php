<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>モード選択画面</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="header">
        <h2>ようこそ、<?php echo htmlspecialchars($username); ?> さん</h2>
        <a href="mypage.php" class="mypage-button">マイページ</a>
    </div>
    <h2>モード選択</h2>
    <div class="mode-buttons">
        <!-- 各モードへのボタン -->
        <button onclick="location.href='game.php'">タイピング</button>
        <button onclick="location.href='mikiri.php'">刹那の見切り - キーボード版</button>

        <!-- ボタンを追加したい場合は以下にコピー -->
        <!--
        <button onclick="location.href='ここにURLを入力'">ここにモードの名前を入力</button>
        -->
    </div>
</body>
</html>