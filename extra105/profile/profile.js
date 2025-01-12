document.addEventListener('DOMContentLoaded', function() {
    /*------------------------------------------------
        設定を取得してフォームに反映
    ------------------------------------------------*/
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            // ログ
            console.log("username:" + data.username);
            console.log("admin:" + data.admin);
            console.log("showKeyboard:" + data.showKeyboard);
            // フォームにデータを反映
            document.getElementById('page-title').textContent = data.username;
            document.getElementById('username').textContent = "ログイン中:" + data.username;
            document.getElementById('admin').style.display = data.admin ? '' : 'none';
            // body
            document.getElementById('titleUsername').textContent = data.username;
        });
});