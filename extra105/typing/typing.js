document.addEventListener('DOMContentLoaded', function() {
    /*------------------------------------------------
        ランキングを取得
    ------------------------------------------------*/
    fetch('typing_game.php')
        .then(response => response.json())
        .then(data => {
            // フォームにデータを反映
            // body
            rankingsList = document.getElementById('rankings');
            rankingsList.innerHTML = '';
            // ログ
            console.log(data);

            /*------------------------------------------------
                ランキングを表示
            ------------------------------------------------*/
            // ランキングがない場合
            if (data.ranking.length === 0) {
                const noDataItem = document.createElement('li');
                noDataItem.textContent = 'ランキングはありません';
                rankingsList.appendChild(noDataItem);
            }
            // ランキングがある場合
            else {
                data.ranking.forEach((item, index) => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `${item.username}: ${item.score}`;
                    rankingsList.appendChild(listItem);
                });
            }
        });
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            // ログ
            console.log("username:" + data.username);
            console.log("admin:" + data.admin);
            console.log("showKeyboard:" + data.showKeyboard);
            // フォームにデータを反映
            document.getElementById('username').textContent = "ログイン中:" + data.username;
            document.getElementById('admin').style.display = data.admin ? '' : 'none';
            document.getElementById('keyboard-container').style.display = data.showKeyboard ? '' : 'none';
        });
});