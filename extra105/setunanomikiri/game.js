let gameState = 'waiting';
let gameId = null;
let startTime;
let username = '<?php echo $_SESSION["username"]; ?>';

function initGame() {
    gameState = 'waiting';
    $('#status').text('マッチメイキングを開始してください。');
    $('#game-display').hide();
    $('#input').hide();
    $('#timer').hide();

    $('#matchmaking-button').on('click', function() {
        $('#status').text('対戦相手を探しています...');
        $('#matchmaking-button').hide();
        startMatchmaking();
    });
}

function startMatchmaking() {
    $.ajax({
        url: 'matchmaking.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.error) {
                $('#status').text('エラー: ' + data.error);
                return;
            }
            gameId = data.match_id;
            checkGameState();
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            $('#status').text('エラーが発生しました。再度お試しください。');
        }
    });
}

function checkGameState() {
    $.ajax({
        url: 'game_state.php',
        method: 'GET',
        data: { game_id: gameId },
        dataType: 'json',
        success: function(data) {
            handleGameState(data);
            if (gameState !== 'finished') {
                setTimeout(checkGameState, 1000);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            setTimeout(checkGameState, 1000);
        }
    });
}

function handleGameState(data) {
    switch(data.state) {
        case 'waiting':
            $('#status').text('対戦相手を待っています...');
            break;
        case 'matched':
            gameState = 'matched';
            $('#status').text('対戦相手が見つかりました！');
            $('#message').text('いざ勝負');
            $('#game-display').show();
            setTimeout(startGame, Math.random() * 5000 + 1000); // 1秒から6秒の間でランダム
            break;
        case 'letter':
            gameState = 'playing';
            $('#letter').text(data.letter);
            $('#input').show().focus();
            startTime = new Date().getTime();
            break;
        case 'result':
            gameState = 'finished';
            $('#status').text(data.winner === username ? '勝利！' : '敗北...');
            $('#timer').text(`反応時間: ${data.reaction_time}秒`).show();
            $('#input').hide();
            setTimeout(function() {
                if (confirm('もう一度プレイしますか？')) {
                    initGame();
                } else {
                    window.location.href = '../toppage.php';
                }
            }, 2000);
            break;
    }
}

function startGame() {
    $.ajax({
        url: 'start_game.php',
        method: 'POST',
        data: { game_id: gameId },
        dataType: 'json',
        success: function(data) {
            if (data.success) {
                $('#letter').text(data.letter);
                $('#input').show().focus();
                startTime = new Date().getTime();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
}

$('#input').on('input', function(e) {
    if (gameState === 'playing') {
        let endTime = new Date().getTime();
        let reactionTime = (endTime - startTime) / 1000;
        $.ajax({
            url: 'submit_input.php',
            method: 'POST',
            data: {
                game_id: gameId,
                letter: e.target.value,
                reaction_time: reactionTime
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    console.log('Input submitted successfully');
                    checkGameState();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
});

$(document).ready(function() {
    initGame();
});