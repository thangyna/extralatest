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
    <div id="sky" style="background-image:url(asset/sky.png)">
    <div id="background_4" style="background-image:url(asset/forest_4.png)">
    <div id="background_3" style="background-image:url(asset/forest_3.png)">
    <div id="background_2" style="background-image:url(asset/forest_2.png)">
    <div id="background_1" style="background-image:url(asset/forest_1.png)">
    <div id="ground" style="background-image:url(asset/ground_big.png)">
        <div id="game-container">
            <h1>刹那の見切り - キーボード版 test</h1>
            <button id="playButton">
                <img id="playButtonImage" src="button/play.png" alt="プレイ">
            </button>
            <button id="quitButton">
                <img id="quitButtonImage" src="button/quit.png" alt="終了">
            </button>
            <div id="game-status"></div>
            <div id="letter-display"></div>
            <div id="result"></div>
            <script src="mikiri_button.js"></script>
            <div id="charactor" src="chara/knight/Run_01.png"></div>
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
            <script>
            $(document).ready(function() {
                let matchId = null;
                let gameStarted = false;
                let startTime;

                $('#playButton').click(function() {
                    startMatching();
                    $('#result').text('');
                    $('#letter-display').text('');
                });

                $('#quitButton').click(function() {
                    window.location.href = 'toppage.php';
                });

                function startMatching() {
                    $('#playButton').hide();
                    $('#quitButton').hide();
                    $('#game-status').text('マッチング中...');

                    $.ajax({
                        url: 'match.php',
                        method: 'POST',
                        data: { action: 'start_match' },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'joined' || data.status === 'created') {
                                matchId = data.match_id;
                                waitForOpponent(matchId);
                            } else {
                                $('#game-status').text('エラーが発生しました。もう一度お試しください。');
                                $('#playButton').show();
                                $('#quitButton').show();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', status, error);
                            $('#game-status').text('通信エラーが発生しました。もう一度お試しください。');
                            $('#playButton').show();
                            $('#quitButton').show();
                        }
                    });
                }

                function waitForOpponent(id) {
                    $('#game-status').text('対戦相手を待っています...');
                    $.ajax({
                        url: 'match.php',
                        method: 'POST',
                        data: { action: 'check_match', match_id: id },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'ready') {
                                startGame(id);
                            } else if (data.status === 'waiting') {
                                setTimeout(function() { waitForOpponent(id); }, 1000);
                            } else {
                                $('#game-status').text('エラーが発生しました。もう一度お試しください。');
                                $('#playButton').show();
                                $('#quitButton').show();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', status, error);
                            $('#game-status').text('通信エラーが発生しました。もう一度お試しください。');
                            $('#playButton').show();
                            $('#quitButton').show();
                        }
                    });
                }

                function startGame(id) {
                    $('#game-status').text('対戦相手が見つかりました。ゲームを開始します。');
                    
                    $.ajax({
                        url: 'match.php',
                        method: 'POST',
                        data: { action: 'get_letter', match_id: id },
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'success' && data.letter) {
                                setTimeout(function() {
                                    $('#game-status').text('いざ勝負');
                                    $('#letter-display').text(data.letter.toUpperCase()).css('font-size', '72px');
                                    startTime = new Date().getTime();
                                    gameStarted = true;
                                    listenForKeyPress();
                                }, 3000);
                            } else {
                                console.error('Failed to get letter:', data);
                                $('#game-status').text('エラーが発生しました。もう一度お試しください。');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', status, error);
                            $('#game-status').text('通信エラーが発生しました。もう一度お試しください。');
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
                                const endTime = new Date().getTime();
                                const reactionTime = (endTime - startTime) / 1000;

                                console.log('Sending reaction time:', reactionTime);

                                $.ajax({
                                    url: 'match.php',
                                    method: 'POST',
                                    data: { 
                                        action: 'submit_time', 
                                        match_id: matchId,
                                        reaction_time: reactionTime
                                    },
                                    dataType: 'json',
                                    success: function(data) {
                                        console.log('Server response:', data);
                                        if (data.status === 'success') {
                                            $('#result').text(`あなたの反応時間: ${reactionTime.toFixed(4)}秒`);
                                            $('#letter-display').text('');
                                            checkGameResult();
                                        } else {
                                            console.error('Error:', data.message);
                                            $('#result').text('エラーが発生しました。もう一度お試しください。');
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Ajax error:', status, error);
                                        $('#result').text('通信エラーが発生しました。もう一度お試しください。');
                                    }
                                });
                            }
                        }
                    });
                }

                document.addEventListener('visibilitychange', function() {
                    if (!document.hidden && gameStarted) {
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
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'completed') {
                                if (data.winner === '<?php echo $username; ?>') {
                                    $('#result').append('<br>あなたの勝ち！');
                                } else if (data.winner === 'draw') {
                                    $('#result').append('<br>引き分け！');
                                } else {
                                    $('#result').append('<br>あなたの負け...');
                                }
                                if (data.opponent_time !== null) {
                                    const opponentTime = parseFloat(data.opponent_time);
                                    $('#result').append(`<br>相手の反応時間: ${opponentTime.toFixed(4)}秒`);
                                } else {
                                    $('#result').append('<br>相手の反応時間: まだ記録されていません');
                                }
                                $('#playButton').show().text('もう一度マッチング');
                                $('#quitButton').show();
                            } else {
                                setTimeout(checkGameResult, 1000);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Ajax error:', status, error);
                            $('#result').text('結果の取得中にエラーが発生しました。');
                        }
                    });
                }
            });
            </script>
        </div>
    </div>
    </div>
</body>
</html>