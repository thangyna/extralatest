document.addEventListener('DOMContentLoaded', function () {
    var enabled = true;
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
            document.getElementById('privacy').checked = data.privacy;
            document.getElementById('keyboardLayout').checked = data.convertLayout;
            document.getElementById('keyboardLayout-dropdown').value = data.layout;
            document.getElementById('homeHighlight').checked = data.homeHighlight;

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
                childsContainer.style.height = childsContainerHeight + 'px';
                for (const key in childs) {
                    childs[key].disabled = false;
                }
            }
            else {
                childsContainer.style.height = '0px';
                for (const key in childs) {
                    childs[key].style.display = 'none';
                }
            }
        });

    /*------------------------------------------------
        最少スコアにかかわる処理
    ------------------------------------------------*/
    var useMinScoreCheckbox = document.getElementById('useMinScore');
    var minScoreInput = document.getElementById('minScore');

    // 最少スコアの入力欄の有効/無効をチェックボックスの状態に応じて切り替え
    useMinScoreCheckbox.addEventListener('change', function () {
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
    var childsContainer = document.getElementById('childs-container');
    var childsContainerHeight = 80;
    var showKeyboardCheckbox = document.getElementById('showKeyboard');
    var childs = {
        missHighlight: document.getElementById('missHighlight-tooltip'),
        homeHighlight: document.getElementById('homeHighlight-tooltip'),
    }

    // キーボードハイライトのチェックボックスの表示/非表示をチェックボックスの状態に応じて切り替え
    showKeyboardCheckbox.addEventListener('change', function () {
        if (showKeyboardCheckbox.checked) {
            childsContainer.style.height = childsContainerHeight + 'px';
            for (const key in childs) {
                childs[key].style.display = 'block';
                childs[key].style.animation = 'openContainer 0.5s'
                childs[key].style.animation = 'slideinTop 0.5s';
            }
            enabled = true;
            setTimeout(() => {
                if (enabled) {
                    for (const key in childs) {
                        // childs[key].disabled = false;
                        childs[key].style.opacity = '1';
                    }
                }
            }, 485);
        } else {
            childsContainer.style.height = '0px';
            for (const key in childs) {
                childs[key].style.animation = 'closeContainer 0.5s'
                childs[key].style.animation = 'slideoutTop 0.5s';
            }
            enabled = false;
            setTimeout(() => {
                if (!enabled) {
                    for (const key in childs) {
                        //childs[key].disabled = true;
                        //childs[key].style.display = 'none';
                        childs[key].style.opacity = '0';
                    }
                }
            }, 485);
        }
    });

    /*------------------------------------------------
        公開設定にかかわる処理
    ------------------------------------------------*/
    var privacy = document.getElementById("privacy");

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

    // ドロップダウンメニューの内容を取得
    const dropdown = document.getElementById('keyboardLayout-dropdown');
    dropdown.addEventListener('change', function () {
        const selectedValue = dropdown.value;
        console.log('Selected layout:', selectedValue);
        sendFormData(); // ドロップダウンの変更時にもフォームデータを送信
    });
});