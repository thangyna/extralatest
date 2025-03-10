document.addEventListener('DOMContentLoaded', function () {
    const deleteGuestDataButton = document.getElementById('deleteGuestDataButton');
    const modal = document.getElementById('modal');
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    const cancelButton = document.getElementById('cancelButton');
    const userDropdown = document.getElementById('userDropdown');
    const loginButton = document.getElementById('loginButton');
    const showFeedbackButton = document.getElementById('showFeedbackButton');
    const feedbackModal = document.getElementById('feedbackModal');
    const feedbackContent = document.getElementById('feedbackContent');
    const closeFeedbackButton = document.getElementById('closeFeedbackButton');

    // ユーザーデータを取得してドロップダウンに追加
    fetch('get_users.php')
        .then(response => response.json())
        .then(data => {
            data.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.username;
                userDropdown.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error:', error);
        });

    deleteGuestDataButton.addEventListener('click', function () {
        modal.classList.add('show');
    });

    confirmDeleteButton.addEventListener('click', function () {
        modal.classList.remove('show');
        // ゲストデータ削除処理を実行
        fetch('delete_guest_data.php', {
            method: 'POST',
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    cancelButton.addEventListener('click', function () {
        modal.classList.remove('show');
    });

    window.addEventListener('click', function (event) {
        if (event.target === modal) {
            modal.classList.remove('show');
        }
    });

    loginButton.addEventListener('click', function () {
        const selectedUserId = userDropdown.value;
        fetch('login_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ userId: selectedUserId })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('ログインに成功しました。');
                    window.location.href = data.redirect; // リダイレクト
                } else {
                    alert('ログインに失敗しました。');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    showFeedbackButton.addEventListener('click', function () {
        fetch('get_feedback.php')
            .then(response => response.json())
            .then(data => {
                feedbackContent.innerHTML = '';
                if (data.feedback.length > 0) {
                    const table = document.createElement('table');
                    table.classList.add('feedback-table');
                    const thead = document.createElement('thead');
                    const tbody = document.createElement('tbody');
                    const headerRow = document.createElement('tr');
                    ['ユーザー名', 'フィードバック'].forEach(text => {
                        const th = document.createElement('th');
                        th.textContent = text;
                        headerRow.appendChild(th);
                    });
                    thead.appendChild(headerRow);
                    data.feedback.forEach(item => {
                        const row = document.createElement('tr');
                        ['username', 'message'].forEach(key => {
                            const td = document.createElement('td');
                            td.textContent = item[key];
                            row.appendChild(td);
                        });
                        tbody.appendChild(row);
                    });
                    table.appendChild(thead);
                    table.appendChild(tbody);
                    feedbackContent.appendChild(table);
                } else {
                    feedbackContent.textContent = 'フィードバックはありません。';
                }
                feedbackModal.classList.add('show');
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    closeFeedbackButton.addEventListener('click', function () {
        feedbackModal.classList.remove('show');
    });

    window.addEventListener('click', function (event) {
        if (event.target === feedbackModal) {
            feedbackModal.classList.remove('show');
        }
    });
});