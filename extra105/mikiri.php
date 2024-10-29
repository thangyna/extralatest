<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>刹那の見切り - キーボード版</title>
    <link rel="stylesheet" type="text/css" href="mikiri_style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        @font-face {
            font-family: 'DotGothic16';
            src: url('fonts/DotGothic16-Regular.ttf') format('truetype');
        }

        * {
            box-sizing: border-box;
            font-family: 'DotGothic16', monospace;
        }

        body {
            overflow: hidden;
            background-color: #73A2AD;
        }

        #user-info {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.7);
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        /* フォントサイズの調整 */
        h1 {
            font-size: 28px;
        }

        #game-status, #letter-display, #result {
            font-size: 18px;
        }

        /* その他の要素のフォントサイズも必要に応じて調整 */
    </style>
</head>
<body>
    <div class="sky-container">
    <div id="sky" style="background-image:url(asset/sky.png)">
    <div id="background_4" style="background-image:url(asset/forest_4.png)">
    <div id="background_3" style="background-image:url(asset/forest_3.png)">
    <div id="background_2" style="background-image:url(asset/forest_2.png)">
    <div id="background_1" style="background-image:url(asset/forest_1.png)">
    <div id="ground" style="background-image:url(asset/ground_big.png)">
        <div id="game-container">
            <h1>刹那の見切り - キーボード版 test</h1>
            <div id="user-info">ユーザー名: <?php echo htmlspecialchars($username); ?></div>
            <button id="playButton">
                <img id="playButtonImage" src="button/play.png" alt="プレイ">
            </button>
            <button id="quitButton">
                <img id="quitButtonImage" src="button/quit.png" alt="終了">
            </button>
            <button id="cancelButton" style="display: none;">
                <img id="cancelButtonImage" src="button/mini/green_x_up.png" alt="キャンセル">
            </button>
            <div id="game-status"></div>
            <div id="letter-display"></div>
            <div id="result"></div>
            <script src="mikiri_button.js"></script>
            <img src="chara/knight/run/run_0.png" id="character"></div>
            <img src="chara/bandit/run/run_0.png" id="enemy"></div>
            <div class="keyboard-container">
                <img src="keyboard/keyboard.png" id="keyboard" alt="キーボード" style = "height:auto;">
                <img src="keyboard/A.png" id="A" alt="A" style = "display:none;height:auto;">
                <img src="keyboard/B.png" id="B" alt="B" style = "display:none;height:auto;">
                <img src="keyboard/C.png" id="C" alt="C" style = "display:none;height:auto;">
                <img src="keyboard/D.png" id="D" alt="D" style = "display:none;height:auto;">
                <img src="keyboard/E.png" id="E" alt="E" style = "display:none;height:auto;">
                <img src="keyboard/F.png" id="F" alt="F" style = "display:none;height:auto;">
                <img src="keyboard/G.png" id="G" alt="G" style = "display:none;height:auto;">
                <img src="keyboard/H.png" id="H" alt="H" style = "display:none;height:auto;">
                <img src="keyboard/I.png" id="I" alt="I" style = "display:none;height:auto;">
                <img src="keyboard/J.png" id="J" alt="J" style = "display:none;height:auto;">
                <img src="keyboard/K.png" id="K" alt="K" style = "display:none;height:auto;">
                <img src="keyboard/L.png" id="L" alt="L" style = "display:none;height:auto;">
                <img src="keyboard/M.png" id="M" alt="M" style = "display:none;height:auto;">
                <img src="keyboard/N.png" id="N" alt="N" style = "display:none;height:auto;">
                <img src="keyboard/O.png" id="O" alt="O" style = "display:none;height:auto;">
                <img src="keyboard/P.png" id="P" alt="P" style = "display:none;height:auto;">
                <img src="keyboard/Q.png" id="Q" alt="Q" style = "display:none;height:auto;">
                <img src="keyboard/R.png" id="R" alt="R" style = "display:none;height:auto;">
                <img src="keyboard/S.png" id="S" alt="S" style = "display:none;height:auto;">
                <img src="keyboard/T.png" id="T" alt="T" style = "display:none;height:auto;">
                <img src="keyboard/U.png" id="U" alt="U" style = "display:none;height:auto;">
                <img src="keyboard/V.png" id="V" alt="V" style = "display:none;height:auto;">
                <img src="keyboard/W.png" id="W" alt="W" style = "display:none;height:auto;">
                <img src="keyboard/X.png" id="X" alt="X" style = "display:none;height:auto;">
                <img src="keyboard/Y.png" id="Y" alt="Y" style = "display:none;height:auto;">
                <img src="keyboard/Z.png" id="Z" alt="Z" style = "display:none;height:auto;">
                <img src="keyboard/hyphen.png" id="hyphen" alt="hypen" style="display:none;height:auto">
                <img src="keyboard/leter.png" alt="leter" style = "height:auto">
            </div>
            <script src="mikiri_animations.js"></script>
            <script src="mikiri_game.js"></script>
        </div>
    </div>
    </div>
</body>
</html>
