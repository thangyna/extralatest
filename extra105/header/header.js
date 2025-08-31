document.addEventListener('DOMContentLoaded', function () {
    /*------------------------------------------------
        ヘッダーの表示にかかわる処理
    ------------------------------------------------*/
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


            // 通知の表示
            const notificationButton = document.getElementById('notification-button');
            const notification = document.getElementById('notification');
            var enabled = false;
            notification.style.display = 'none';
            notificationButton.addEventListener('click', function () {
                enabled = !enabled;
                if (enabled) {
                    notification.style.display = 'block';
                    notification.style.animation = 'slidein 0.5s';
                }
                else {
                    notification.style.animation = 'slideout 0.5s';
                    setTimeout(() => {
                        if (!enabled)
                            notification.style.display = 'none';
                    }, 490);
                }
            });
            /*------------------------------------------------
                通知の表示にかかわる処理
            ------------------------------------------------*/
            // 通知の内容を取得して表示
            fetch('../header/notification.json')
                .then(response => response.json())
                .then(notifications => {
                    // 通知の内容を表示
                    const notificationContainer = document.getElementById('notification');
                    notifications.forEach((notification, index) => {
                        const notificationContent = document.createElement('div');
                        notificationContent.className = 'notification-content';

                        const title = document.createElement('h3');
                        title.textContent = notification.title;

                        // typeに基づいてタイトルの色を変更
                        switch (notification.type) {
                            case 'update':
                                title.style.color = 'rgb(59,130,246)';
                                break;
                            default:
                                title.style.color = 'black';
                        }

                        notificationContent.appendChild(title);

                        const text = document.createElement('span');
                        text.textContent = notification.text;
                        notificationContent.appendChild(text);

                        if (index === 0) {
                            notificationContent.classList.add('below-header');
                        }

                        notificationContainer.appendChild(notificationContent);
                    });
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
            console.log("privacy:" + data.privacy);
            console.log("convertLayout:" + data.convertLayout);
            console.log("keyboardLayout:" + data.layout);
            console.log("homeHighlight:" + data.homeHighlight);
            console.log("guest:" + data.guest);
            // console.log("exp:" + data.exp);

            var admin = this.getElementById('admin');
            var logout = this.getElementById('logout');
            if (data.admin) {
                admin.style.display = 'block';
            }
            // 
            //if (data.guest) {
            //    logout.style.display = 'none';
            //}
        });
});
