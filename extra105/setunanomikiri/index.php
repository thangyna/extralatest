<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>刹那の見切り - キーボード版</title>
    <link rel="stylesheet" type="text/css" href="/mikiri_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="header">
        <span>ログイン中: <?php echo htmlspecialchars($username); ?></span>
        <a href="../toppage.php">トップページに戻る</a>
    </div>
    <h1>刹那の見切り - キーボード版</h1>
    <div id="game-area">
        <div id="status">マッチメイキングを開始してください。</div>
        <button id="matchmaking-button">マッチメイキング開始</button>
        <div id="game-display" style="display:none;">
            <div id="message"></div>
            <div id="letter" style="font-size: 48px;"></div>
            <input type="text" id="input" maxlength="1" style="display:none;">
        </div>
        <div id="timer" style="display:none;"></div>
    </div>
    <script src="game.js"></script>
</body>
</html>