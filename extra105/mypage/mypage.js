var minScore = 1000000; // ここで特定の値を設定

document.addEventListener('DOMContentLoaded', function () {
    // トグルスイッチの動作
    const toggleSwitch = document.getElementById('switch');
    toggleSwitch.addEventListener('change', function () {
        if (toggleSwitch.checked) {
            console.log('checked');
            document.getElementById('scoreChart').style.display = '';
            document.getElementById('mistakesChart').style.display = 'none';
        } else {
            console.log('unchecked');
            document.getElementById('scoreChart').style.display = 'none';
            document.getElementById('mistakesChart').style.display = '';
        }
    });
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            minScore = data.useMinScore ? data.minScore : 0;
            console.log('Use Min Score:', data.useMinScore);
            console.log('Min Score:', minScore);
        });

    fetch('mypage.php')
        .then(response => response.json())
        .then(data => {
            // スコアが特定の値以上のデータのみをフィルタリング
            const filteredData = data.records.filter(record => record.score > minScore);

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


            /*------------------------------------------------
                ミスのキーハイライト
            ------------------------------------------------*/
            const keyboard = document.getElementById('keyboard');
            const key = {
                "a": document.getElementById("a"),
                "b": document.getElementById("b"),
                "c": document.getElementById("c"),
                "d": document.getElementById("d"),
                "e": document.getElementById("e"),
                "f": document.getElementById("f"),
                "g": document.getElementById("g"),
                "h": document.getElementById("h"),
                "i": document.getElementById("i"),
                "j": document.getElementById("j"),
                "k": document.getElementById("k"),
                "l": document.getElementById("l"),
                "m": document.getElementById("m"),
                "n": document.getElementById("n"),
                "o": document.getElementById("o"),
                "p": document.getElementById("p"),
                "q": document.getElementById("q"),
                "r": document.getElementById("r"),
                "s": document.getElementById("s"),
                "t": document.getElementById("t"),
                "u": document.getElementById("u"),
                "v": document.getElementById("v"),
                "w": document.getElementById("w"),
                "x": document.getElementById("x"),
                "y": document.getElementById("y"),
                "z": document.getElementById("z"),
                "-": document.getElementById("hyphen")
            };
            const mistakesKey = data.mistakes;
            console.log(mistakesKey);
            
            // 全てのキーのミス数を合計
            const totalMistakes = Object.values(mistakesKey).reduce((acc, val) => acc + val, 0);
            console.log('Total Mistakes:', totalMistakes);
            
            // 各キーのミス数の割合を計算
            const mistakesPercentage = {};
            var maxValue = 0;
            var multiplier = 1;
            // ミス数最大のキーがもっとも濃い色になるようにする
            for (const _key in mistakesKey) {
                mistakesPercentage[_key] = mistakesKey[_key] / totalMistakes;
                if (key[_key] && mistakesPercentage[_key] > maxValue) {
                    maxValue = mistakesPercentage[_key];
                }
            }
            multiplier = 1/maxValue;
            console.log('maxValue:', maxValue);
            console.log('Multiplier:', multiplier);
            for (const _key in mistakesKey) {
                if (key[_key]) {
                    key[_key].style.backgroundColor = `rgba(255, 0, 0, ${Math.sqrt(multiplier*mistakesPercentage[_key])})`;

                    // ツールチップを作成
                    const tooltip = document.createElement('span');
                    tooltip.className = 'tooltip';
                    tooltip.innerText = `ミス率: ${(mistakesPercentage[_key] * 100).toFixed(2)}%`;
                    key[_key].appendChild(tooltip);
                }
            }
            console.log('Mistakes Percentage:', mistakesPercentage);
        })
        .catch(error => console.error('Error fetching data:', error));
});
