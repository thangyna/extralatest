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
        * {
            box-sizing: border-box;
        }
        body {
            overflow: hidden;
            background-color: #73A2AD;
        }
    </style>
</head>
<body>
    <div class="sky-container">
    <div id="background" style="background-image:url(asset/sky.png)">
    <div id="background" style="background-image:url(asset/forest_4.png)">
    <div id="background" style="background-image:url(asset/forest_3.png)">
    <div id="background" style="background-image:url(asset/forest_2.png)">
    <div id="background" style="background-image:url(asset/forest_1.png)">
    <div id="ground" style="background-image:url(asset/ground_big.png)">
        <h1>刹那の見切り - キーボード版</h1>
        <button id="playButton">
            <img id="playButtonImage" src="button/play.png" alt="プレイ">
        </button>
        <button id="quitButton">
            <img id="quitButtonImage" src="button/quit.png" alt="終了">
        </button>
        <script src="mikiri_button.js"></script>
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

        <script>
        $(document).ready(function() {
            let matchId = null;
            let gameStarted = false;
            let matchingInProgress = false;

            $('#start-match').click(function() {
                startMatching();
                $('#result').text(''); // 前の試合の記録をクリア
                $('#letter-display').text(''); // 前回のアルファベットを非表示
            });

            $('#cancel-match').click(function() {
                cancelMatching();
            });

            function startMatching() {
                $('#start-match').hide();
                $('#cancel-match').show();
                $('#game-status').text('マッチング中...');
                matchingInProgress = true;

                $.ajax({
                    url: 'match.php',
                    method: 'POST',
                    data: { action: 'start_match' },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'waiting') {
                            pollForMatch();
                        } else if (data.status === 'matched') {
                            startGame(data.match_id);
                        }
                    }
                });
            }

            function cancelMatching() {
                matchingInProgress = false;
                $('#start-match').show();
                $('#cancel-match').hide();
                $('#game-status').text('');

                $.ajax({
                    url: 'match.php',
                    method: 'POST',
                    data: { action: 'cancel_match' },
                    success: function(response) {
                        console.log('Matching cancelled');
                    }
                });
            }

            function pollForMatch() {
                if (!matchingInProgress) return;

                $.ajax({
                    url: 'match.php',
                    method: 'POST',
                    data: { action: 'check_match' },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'matched') {
                            startGame(data.match_id);
                        } else if (matchingInProgress) {
                            setTimeout(pollForMatch, 1000);
                        }
                    }
                });
            }

            function startGame(id) {
                matchId = id;
                $('#game-status').text('いざ勝負');
                $('#cancel-match').hide();
                gameStarted = true;

                $.ajax({
                    url: 'match.php',
                    method: 'POST',
                    data: { action: 'get_letter', match_id: matchId },
                    success: function(response) {
                        const data = JSON.parse(response);
                        setTimeout(function() {
                            $('#letter-display').text(data.letter.toUpperCase()).css('font-size', '72px');
                            listenForKeyPress();
                        }, 3000); // 3秒後に文字を表示
                    }
                });
            }

            function listenForKeyPress() {
                $(document).on('keypress', function(e) {
                    if (gameStarted) {
                        const pressedKey = String.fromCharCode(e.which).toLowerCase();
                        const displayedLetter = $('#letter-display').text().toLowerCase();

                        if (pressedKey === displayedLetter) {
                            gameStarted = false;
                            $.ajax({
                                url: 'match.php',
                                method: 'POST',
                                data: { action: 'submit_time', match_id: matchId },
                                success: function(response) {
                                    const data = JSON.parse(response);
                                    $('#result').text(`あなたの反応時間: ${data.your_time}秒`);
                                    $('#letter-display').text('');
                                    checkGameResult();
                                }
                            });
                        }
                    }
                });
            }

            // ウィンドウがアクティブでない場合も秒数をカウントするために、
            // ページの可視性が変更されたときのイベントリスナーを追加
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden && gameStarted) {
                    // ページが表示されたときに、経過時間を送信
                    $.ajax({
                        url: 'match.php',
                        method: 'POST',
                        data: { action: 'update_time', match_id: matchId },
                        success: function(response) {
                            console.log('Time updated');
                        }
                    });
                }
            });

            function checkGameResult() {
                $.ajax({
                    url: 'match.php',
                    method: 'POST',
                    data: { action: 'check_result', match_id: matchId },
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.status === 'completed') {
                            if (data.winner === '<?php echo $username; ?>') {
                                $('#result').append('<br>あなたの勝ち！');
                            } else if (data.winner === 'draw') {
                                $('#result').append('<br>引き分け！');
                            } else {
                                $('#result').append('<br>あなたの負け...');
                            }
                            $('#result').append(`<br>相手の反応時間: ${data.opponent_time}秒`);
                            $('#start-match').show().text('もう一度マッチング');
                        } else {
                            setTimeout(checkGameResult, 1000);
                        }
                    }
                });
            }
        });
        </script>
    </div>
    </div>
    </div>
    </div>
    </div>
</body>
</html>