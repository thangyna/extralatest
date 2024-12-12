/*================================================== 以下変数 ==================================================*/
// キーボードの表示
const keyboard = document.getElementById('keyboard');
const key = {
    "a": document.getElementById("A"),
    "b": document.getElementById("B"),
    "c": document.getElementById("C"),
    "d": document.getElementById("D"),
    "e": document.getElementById("E"),
    "f": document.getElementById("F"),
    "g": document.getElementById("G"),
    "h": document.getElementById("H"),
    "i": document.getElementById("I"),
    "j": document.getElementById("J"),
    "k": document.getElementById("K"),
    "l": document.getElementById("L"),
    "m": document.getElementById("M"),
    "n": document.getElementById("N"),
    "o": document.getElementById("O"),
    "p": document.getElementById("P"),
    "q": document.getElementById("Q"),
    "r": document.getElementById("R"),
    "s": document.getElementById("S"),
    "t": document.getElementById("T"),
    "u": document.getElementById("U"),
    "v": document.getElementById("V"),
    "w": document.getElementById("W"),
    "x": document.getElementById("X"),
    "y": document.getElementById("Y"),
    "z": document.getElementById("Z"),
    "-": document.getElementById("hyphen")
};

// ボタンの宣言
const playButton = document.getElementById('playButton');
const quitButton = document.getElementById('quitButton');
const cancelButton = document.getElementById('cancelButton');

// テキストの宣言
const gameStatus = document.getElementById('game-status');

// サーバ、ユーザ管理及びマッチ管理
let matchId = null;
let gameStarted = false;
let startTime;
let opponent = null;
let username = "<?php echo $username; ?>"; // PHPの変数をJavaScriptで使用できるようにする

/*------------------------------------------------
    マッチングを開始
------------------------------------------------*/
function startMatching() {
    // ボタンの表示を切替
    playButton.style.display = "none";
    quitButton.style.display = "none";
    cancelButton.style.display = "block";
    $('#game-status').text('マッチング中...');

    $.ajax({
        url: 'match.php',
        method: 'POST',
        data: { action: 'start_match' },
        dataType: 'json',
        success: function(data) {
            console.log('Start match response:', data); // デバッグ用ログ
            if (data.status === 'joined' || data.status === 'created') {
                matchId = data.match_id;
                waitForOpponent(matchId);
            } else {
                console.error('Unexpected response:', data); // デバッグ用ログ
                $('#game-status').text('エラーが発生しました。もう一度お試しください。');
                playButton.show();
                quitButton.show();
                cancelButton.hide();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', status, error);
            $('#game-status').text('通信エラーが発生しました。もう一度お試しください。');
            playButton.show();
            quitButton.show();
            cancelButton.hide();
        }
    });
}

/*------------------------------------------------
    マッチングをキャンセル
------------------------------------------------*/
function cancelMatching() {
    if (matchId) {
        $.ajax({
            url: 'match.php',
            method: 'POST',
            data: { action: 'cancel_match', match_id: matchId },
            dataType: 'json',
            success: function(data) {
                if (data.status === 'cancelled') {
                    $('#game-status').text('マッチングをキャンセルしました。');
                    playButton.show();
                    quitButton.show();
                    cancelButton.hide();
                    matchId = null;
                } else {
                    $('#game-status').text('キャンセルに失敗しました。');
                }
            },
            error: function(xhr, status, error) {
                console.error('Ajax error:', status, error);
                $('#game-status').text('通信エラーが発生しました。');
            }
        });
    }
}

/*------------------------------------------------
    相手役を探す
------------------------------------------------*/
function waitForOpponent(id) {
    $('#game-status').text('対戦相手を待っています...');
    $.ajax({
        url: 'match.php',
        method: 'POST',
        data: { action: 'check_match', match_id: id },
        dataType: 'json',
        success: function(data) {
            if (data.status === 'ready') {
                $('#cancelButton').hide();
                opponent = data.opponent;
                if (opponent === username) {
                    $('#game-status').text('エラーが発生しました。もう一度お試しください。');
                    playButton.show();
                    quitButton.show();
                    return;
                }
                $('#game-status').html(`${username} vs ${opponent}<br>対戦相手が見つかりました。`);
                setTimeout(function() {
                    startGame(id);
                }, 3000); // 3秒間表示してからゲームを開始
            } else if (data.status === 'waiting') {
                setTimeout(function() { waitForOpponent(id); }, 1000);
            } else {
                $('#game-status').text('エラーが発生しました。もう一度お試しください。');
                playButton.show();
                quitButton.show();
                cancelButton.hide();
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', status, error);
            $('#game-status').text('通信エラーが発生しました。もう一度お試しください。');
            playButton.show();
            quitButton.show();
            cancelButton.hide();
        }
    });
}

/*------------------------------------------------
    ゲームの開始
------------------------------------------------*/
function startGame(id) {
    $('#game-status').append('<br>ゲームを開始します。');
    // キーボードのハイライトを非表示
    for (_key in key) {
        key[_key].style.display = 'none';
    }
    
    $.ajax({
        url: 'match.php',
        method: 'POST',
        data: { action: 'get_letter', match_id: id },
        dataType: 'json',
        success: function(data) {
            if (data.status === 'success' && data.letter) {
                $('#game-status').append('<br>いざ勝負');
                setTimeout(function() {
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
            
            // キーボードのハイライトを表示
            key[displayedLetter].style.display = 'block';
            setAnimationState('attack');

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

/*------------------------------------------------
    リザルト関係の処理
------------------------------------------------*/
function checkGameResult() {
    $.ajax({
        url: 'match.php',
        method: 'POST',
        data: { action: 'check_result', match_id: matchId },
        dataType: 'json',
        success: function(data) {
            if (data.status === 'completed') {
                let resultMessage = ''
                if (data.you_won) {
                    resultMessage = '勝利！'
                } else if (data.your_time === data.opponent_time) {
                    resultMessage = '引き分け！'
                } else {
                    resultMessage = '敗北...'
                }
                $('#result').html(`結果: ${resultMessage}<br>あなたの時間: ${data.your_time.toFixed(3)}秒<br>相手の時間: ${data.opponent_time.toFixed(3)}秒`)
                playButton.show().text('もう一度マッチング')
                quitButton.show()
            } else if (data.status === 'waiting') {
                setTimeout(checkGameResult, 1000)
            } else {
                $('#result').text('エラーが発生しました。')
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', status, error)
            $('#result').text('結果の取得中にエラーが発生しました。')
        }
    })
}

function checkResult(matchId) {
    $.ajax({
        url: 'match.php',
        method: 'POST',
        data: { action: 'check_result', match_id: matchId },
        dataType: 'json',
        success: function(data) {
            if (data.status === 'completed') {
                let resultMessage = '';
                if (data.you_won) {
                    resultMessage = '勝利！';
                } else if (data.your_time === data.opponent_time) {
                    resultMessage = '引き分け！';
                } else {
                    resultMessage = '敗北...';
                }
                $('#result').html(`結果: ${resultMessage}<br>あなたの時間: ${data.your_time.toFixed(3)}秒<br>相手の時間: ${data.opponent_time.toFixed(3)}秒`);
                gameEnded = true;
            } else if (data.status === 'waiting') {
                setTimeout(function() { checkResult(matchId); }, 1000);
            } else {
                $('#result').text('エラーが発生しました。');
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax error:', status, error);
            $('#result').text('通信エラーが発生しました。');
        }
    });
}

/*------------------------------------------------
    ボタン入力の処理
------------------------------------------------*/
playButton.addEventListener("click", function () {
    console.log("PLAY BUTTON has pressed!!!");
    startMatching();
    $('#result').text('');
    $('#letter-display').text('');
});

quitButton.addEventListener("click", function () {
    console.log("QUIT BUTTON has pressed!!!");
    window.location.href = 'toppage.php';
});

cancelButton.addEventListener("click", function () {
    console.log("CANCEL BUTTON has pressed!!!");
    cancelMatching();
});
