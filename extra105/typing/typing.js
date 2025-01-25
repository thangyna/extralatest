document.addEventListener('DOMContentLoaded', function() {
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            // フォームにデータを反映
            document.getElementById('keyboard').style.display = data.showKeyboard ? '' : 'none';
        });
});