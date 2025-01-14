/*------------------------------------------------
    ヘッダーの表示にかかわる処理
------------------------------------------------*/
document.addEventListener('DOMContentLoaded', function () {
    // headerを適応
    fetch('../header/header.html')
        .then(response => response.text())
        .then(html => {
            document.getElementById('header-container').innerHTML = html;
            document.getElementById('page-title').textContent = document.title;
            
            // ユーザーメニューの表示切り替え 
            user.addEventListener('click', function () {
                user.classList.toggle('active');
            });

            // クリックイベントをウィンドウ全体に追加して、メニュー外をクリックしたときにメニューを閉じる
            window.addEventListener('click', function (event) {
                if (!user.contains(event.target)) {
                    user.classList.remove('active');
                }
            });
        });

    // ユーザーネームを取得して表示
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('username').textContent = data.username;
            console.log("username:" + data.username);
            console.log("admin:" + data.admin);
            console.log("useMinScore:" + data.useMinScore);
            console.log("minScore:" + data.minScore);
            console.log("showKeyboard:" + data.showKeyboard);
            console.log("missHighlight:" + data.missHighlight);
            // console.log("exp:" + data.exp);
        });
});