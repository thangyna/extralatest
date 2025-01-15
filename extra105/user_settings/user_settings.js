document.addEventListener('DOMContentLoaded', function() {
    var missHighlightContainer = document.getElementById('missHighlight-container');
    var enabled = true;

    // コンテナの高さを取得して閉じる
    var initialHeight = missHighlightContainer.offsetHeight;
    missHighlightContainer.style.setProperty('--initial-height', initialHeight + 'px');

    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('user_settings.php')
        .then(response => response.json())
        .then(data => {
            // フォームにデータを反映
            // body
            document.getElementById('useMinScore').checked = data.useMinScore;
            document.getElementById('minScore').value = data.minScore;
            document.getElementById('showKeyboard').checked = data.showKeyboard;
            document.getElementById('missHighlight').checked = data.missHighlight;
            console.log("data.missHighlight: " + data.missHighlight);

            /*------------------------------------------------
                初期状態
            ------------------------------------------------*/
            // 最少スコア
            if (!useMinScoreCheckbox.checked) {
                minScoreInput.disabled = true;
                minScoreInput.style.backgroundColor = '#e0e0e0';
            }
            // キーボードハイライト
            if (!showKeyboardCheckbox.checked) {
                document.getElementById('missHighlight').style.display = 'none';
                enabled = false;
            }

            /*------------------------------------------------
                初期状態の設定
            ------------------------------------------------*/
            // 最少スコアの初期状態
            if (!useMinScoreCheckbox.checked) {
                minScoreInput.disabled = true;
                minScoreInput.style.backgroundColor = '#e0e0e0';
            }
            // キーボードハイライトの初期状態
            if (showKeyboardCheckbox.checked) {
                missHighlight.style.display = 'block';
                missHighlight.style.animation = 'slideinTop 0.5s';
            }
            else {
                missHighlight.style.display = 'none';
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

    /*------------------------------------------------
        キーボードにかかわる処理
    ------------------------------------------------*/
    var showKeyboardCheckbox = document.getElementById('showKeyboard');
    var missHighlight = document.getElementById('missHighlight-tooltip');

    // キーボードハイライトのチェックボックスの表示/非表示をチェックボックスの状態に応じて切り替え
    showKeyboardCheckbox.addEventListener('change', function() {
        if (showKeyboardCheckbox.checked) {
            missHighlightContainer.style.animation = 'openContainer 0.5s'
            missHighlight.style.animation = 'slideinTop 0.5s';
            missHighlight.style.display = 'block';
            enabled = true;
            setTimeout(() => {
                if (enabled) {
                    missHighlight.disabled = false;
                }
            }, 485);
        } else {
            missHighlightContainer.style.animation = 'closeContainer 0.5s'
            missHighlight.style.animation = 'slideoutTop 0.5s';
            enabled = false;
            setTimeout(() => {
                if (!enabled) {
                    missHighlight.disabled = true;
                    missHighlight.style.display = 'none';
                }
            }, 485);
        }
    });

    /*------------------------------------------------
        フォームデータを送信
    ------------------------------------------------*/
    function sendFormData() {
        // フォームデータを取得
        const form = document.getElementById('userSettingsForm');
        const formData = new FormData(form);

        // フォームデータを送信
        fetch('user_settings.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log(data);
            // 必要に応じて、送信後の処理をここに追加
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // フォームの各入力要素にchangeイベントリスナーを追加
    document.querySelectorAll('#userSettingsForm input').forEach(input => {
        input.addEventListener('change', sendFormData);
    });
});