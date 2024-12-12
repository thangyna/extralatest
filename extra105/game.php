<?php
session_start();
require_once('db.php');  // PDO接続を含むファイルを読み込む

// タイムゾーンを日本時間に設定
date_default_timezone_set('Asia/Tokyo');

// 今日の日付を取得 (日本時間)
$today = date('Y-m-d');

// トップ5位のスコアランキングを取得
$sql_ranking = "
    SELECT username, MAX(score) AS score
    FROM game_results
    WHERE DATE(recorded_at) = :today
    GROUP BY username
    ORDER BY score DESC
    LIMIT 5
";

$stmt = $pdo->prepare($sql_ranking);
$stmt->execute(['today' => $today]);
$ranking_results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>タイピングゲーム</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        .header {
            position: relative;
            padding-top: 10px;
        }
        .page-title {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            font-size: inherit;
            color: inherit;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="page-title">タイピングゲームページ</div>
        <span style="margin-left: 200px;">ログイン中: <?php echo $_SESSION['username']; ?></span>
        <a href="mypage.php">マイページ</a>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
            <a href="admin.php">管理者ページ</a>
        <?php endif; ?>
        <a href="logout.php">ログアウト</a>
        <a href="feedback.php" style="float: right; margin-right: 10px;">ご意見箱</a>
        <a href="toppage.php" style="float: right;">トップページ</a>
    </div>

    <h2>タイピングゲーム</h2>
    

    <div id="gameArea">
        <p id="countdown"></p>
        <p>残り時間: <span id="timer">60</span> 秒</p>
        <div id="time-frame">
            <div id="time-bar"></div>
        </div>
        <p id="japaneseWord"></p>
        <p id="kanaWord" style="color: gray;"></p>
        <p id="romajiWord" style="color: gray;"></p>
        <p id="nextWord"></p>
        <div class="image-container">
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
        <button id="startButton">スタート</button>
    </div>                      
    <div id="ranking" style="float: left; margin-right: 20px;">
        <h3>今日のランキング</h3>        
        <ol>                    
            <?php               
            if (count($ranking_results) > 0) {
                foreach ($ranking_results as $row) {
                    // スコアにカンマを追加
                    echo "<li>" . htmlspecialchars($row['username']) . ": " . number_format($row['score']) . "</li>";
                }               
            } else {            
                echo "<li>データなし</li>";
            }
            ?>
        </ol>
    </div>
    <script src="setKeyboardSize.js"></script>
    <script src="script.js" defer></script>
</body>
</html>
