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
    <style>
        .header {
            position: relative;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .page-title {
            font-size: inherit;
            color: inherit;
            font-weight: bold;
            margin: 0;
            text-align: left;
        }
        .welcome-message {
            font-weight: bold;
            margin: 0;
        }
        .mypage-button {
            align-self: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 class="page-title">トップページ</h2>
        <h2 class="welcome-message">ようこそ、<?php echo htmlspecialchars($username); ?> さん</h2>
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