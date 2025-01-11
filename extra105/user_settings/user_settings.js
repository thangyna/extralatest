document.addEventListener('DOMContentLoaded', function() {
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('user_settings.php')
        .then(response => response.json())
        .then(data => {
            // フォームにデータを反映
            // header
            document.getElementById('username').textContent = "ログイン中:" + data.username;
            document.getElementById('admin').style.display = data.admin ? '' : 'none';
            // body
            document.getElementById('useMinScore').checked = data.useMinScore;
            document.getElementById('minScore').value = data.minScore;
            document.getElementById('showKeyboard').checked = data.showKeyboard;
            // ログ
            console.log(data);
            console.log("username:" + data.username);
            console.log("useMinScore:" + data.useMinScore);
            console.log("minScore:" + data.minScore);
            console.log("showKeyboard:" + data.showKeyboard);

            /*------------------------------------------------
                最少スコアの初期状態
            ------------------------------------------------*/
            if (!useMinScoreCheckbox.checked) {
                minScoreInput.disabled = true;
                minScoreInput.style.backgroundColor = '#e0e0e0';
            }
        });

    /*------------------------------------------------
        最少スコアにかかわる処理
    ------------------------------------------------*/
    var useMinScoreCheckbox = document.getElementById('useMinScore');
    var minScoreInput = document.getElementById('minScore');

    // 最少スコアの入力欄の有効/無効をチェックボックスの状態に応じて切り替え
    useMinScoreCheckbox.addEventListener('change', function() {
        if (useMinScoreCheckbox.checked) {
            minScoreInput.disabled = false;
            minScoreInput.style.backgroundColor = '';
        } else {
            minScoreInput.disabled = true;
            minScoreInput.style.backgroundColor = '#e0e0e0';
        }
    });
});