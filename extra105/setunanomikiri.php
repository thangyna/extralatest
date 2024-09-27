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
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>刹那の見切り - キーボード版</h1>
    <div id="game-area">
        <button id="start-match">マッチングを開始</button>
        <button id="cancel-match" style="display: none;">マッチングをキャンセル</button>
        <div id="game-status"></div>
        <div id="letter-display"></div>
        <div id="result"></div>
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
</body>
</html>