document.addEventListener('DOMContentLoaded', function () {
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            // フォームにデータを反映
            // header
            document.getElementById('username').textContent = "ログイン中:" + data.username;
            document.getElementById('admin').style.display = data.admin ? '' : 'none';
        });

    fetch('mypage.php')
        .then(response => response.json())
        .then(data => {
            // スコアが特定の値以上のデータのみをフィルタリング
            const minScore = 1000000; // ここで特定の値を設定
            const filteredData = data.filter(record => record.score > minScore);

            // フィルタリングされたデータを使用してラベルとデータを準備
            const labels = filteredData.map(record => {
                const date = new Date(record.date);
                return date.toLocaleDateString(); // 日付のみを表示
            });
            const scores = filteredData.map(record => record.score);
            const correctChars = filteredData.map(record => record.correct_chars);
            const mistakes = filteredData.map(record => record.mistakes);
            const accuracy = filteredData.map(record => record.accuracy); // 正確度をパーセンテージに変換

            const ctx1 = document.getElementById('scoreChart').getContext('2d');
            const ctx2 = document.getElementById('mistakesChart').getContext('2d');
            const scoreAccuracyChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'スコア',
                            data: scores,
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: false
                        },
                        {
                            label: '正確度 (%)',
                            data: accuracy,
                            borderColor: 'rgb(255, 159, 64)',
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            fill: false,
                            yAxisID: 'y-axis-accuracy' // 別のY軸を使用
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: '日付'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'スコア'
                            }
                        },
                        'y-axis-accuracy': {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            min: 0,
                            max: 100,
                            title: {
                                display: true,
                                text: '正確度 (%)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%'; // パーセンテージ表示
                                }
                            }
                        }
                    }
                }
            });
            const correctMistakesChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: '正解文字数',
                            data: correctChars,
                            borderColor: 'rgb(144, 238, 144)',
                            backgroundColor: 'rgba(144, 238, 144, 0.2)',
                            fill: false
                        },
                        {
                            label: 'ミス数',
                            data: mistakes,
                            borderColor: 'rgb(255, 64, 0)',
                            backgroundColor: 'rgba(255, 64, 0, 0.2)',
                            fill: false
                        },
                        {
                            label: '正確度 (%)',
                            data: accuracy,
                            borderColor: 'rgb(255, 159, 64)',
                            backgroundColor: 'rgba(255, 159, 64, 0.2)',
                            fill: false,
                            yAxisID: 'y-axis-accuracy' // 別のY軸を使用
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: '日付'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: '文字数'
                            }
                        },
                    },
                    'y-axis-accuracy': {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        title: {
                            display: true,
                            text: '正確度 (%)'
                        },
                        ticks: {
                            callback: function (value) {
                                return value + '%'; // パーセンテージ表示
                            }
                        }
                    }
                }
            });
        })
        .catch(error => console.error('Error fetching data:', error));
});