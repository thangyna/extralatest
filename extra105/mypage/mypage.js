var minScore = 1000000; // ここで特定の値を設定
var keyboardPath;
var maxCount;

document.addEventListener('DOMContentLoaded', function () {
    // チャートの表示の初期化
    document.getElementById('scoreChart').style.display = '';
    document.getElementById('mistakesChart').style.display = '';

    // カールセルの動作
    const carouselInner = document.querySelector('.carousel-inner');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const indicators = document.querySelectorAll('.indicator');
    let currentIndex = 0;
    // スライダーの動作
    const displayCountSlider = document.getElementById('displayCountSlider');
    const displayCountValue = document.getElementById('displayCountValue');
    let displayCount = parseInt(displayCountSlider.value);

    function updateCarousel() {
        const width = carouselInner.clientWidth;
        carouselInner.style.transform = `translateX(${-currentIndex * width}px)`;
        updateIndicators();
    }

    function updateIndicators() {
        indicators.forEach((indicator, index) => {
            if (index === currentIndex) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });
    }

    prevBtn.addEventListener('click', function () {
        currentIndex = (currentIndex > 0) ? currentIndex - 1 : 0;
        updateCarousel();
    });

    nextBtn.addEventListener('click', function () {
        const items = document.querySelectorAll('.carousel-item');
        currentIndex = (currentIndex < items.length - 1) ? currentIndex + 1 : items.length - 1;
        updateCarousel();
    });

    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', function () {
            currentIndex = index;
            updateCarousel();
        });
    });

    displayCountSlider.addEventListener('input', function () {
        displayCount = parseInt(displayCountSlider.value);
        displayCountValue.textContent = displayCount;
        updateCharts();
        keyboardHighlight();
    });

    window.addEventListener('resize', updateCarousel);
    /*------------------------------------------------
        データを取得してフォームに反映
    ------------------------------------------------*/
    fetch('../user_settings/user_settings.php')
        .then(response => response.json())
        .then(data => {
            minScore = data.useMinScore ? data.minScore : 0;
            console.log('Use Min Score:', data.useMinScore);
            console.log('Min Score:', minScore);
            keyboardPath = '../assets/keyboards/' + data.layout + '.html';
            return fetch(keyboardPath);
        })
        .then(response => response.text())
        .then(html => {
            const keyboardContainer = document.getElementById('keyboard-container');
            keyboardContainer.innerHTML = html;
            // ホームキーのハイライトをリセット
            homeKeys = document.getElementsByClassName('home');
            for (let homeKey of homeKeys) {
                homeKey.style.borderColor = '#ccc';
            }

            return fetch('mypage.php');
        })
        .then(response => response.json())
        .then(data => {
            // スコアが特定の値以上のデータのみをフィルタリング
            window.data = data;
            window.filteredData = data.records.filter(record => record.score > minScore);
            maxCount = window.filteredData.length;
            displayCountSlider.max = maxCount;
            displayCountSlider.value = maxCount; // スライダーの初期値を最大値に設定
            displayCountValue.textContent = maxCount; // 表示件数の初期値を最大値に設定
            displayCount = maxCount; // 表示件数の変数も更新
            updateCharts();
            keyboardHighlight();
        });

    function isHighScore(score, index, scores) {
        return score === 0 || score > Math.max(...scores.slice(0, index));
    }

    function updateCharts() {
        // 表示件数に基づいてデータを制限
        const limitedData = window.filteredData.slice(-displayCount);

        // フィルタリングされたデータを使用してラベルとデータを準備
        const labels = limitedData.map(record => {
            const date = new Date(record.date);
            return date.toLocaleDateString(); // 日付のみを表示
        });

        const scores = limitedData.map(record => record.score);
        const correctChars = limitedData.map(record => record.correct_chars);
        const mistakes = limitedData.map(record => record.mistakes);
        const accuracy = limitedData.map(record => record.accuracy); // 正確度をパーセンテージに変換

        const higtScores = scores.filter((score, index) => isHighScore(score, index, scores));

        const ctx1 = document.getElementById('scoreChart').getContext('2d');
        const ctx2 = document.getElementById('mistakesChart').getContext('2d');
        const ctx3 = document.getElementById('highScoreChart').getContext('2d');

        if (window.scoreAccuracyChart) {
            window.scoreAccuracyChart.destroy();
        }
        if (window.correctMistakesChart) {
            window.correctMistakesChart.destroy();
        }
        if (window.higtScoreChart) {
            // window.higtScoreChart.destroy();
        }

        window.scoreAccuracyChart = new Chart(ctx1, {
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
                            callback: function (value) {
                                return value + '%'; // パーセンテージ表示
                            }
                        }
                    }
                }
            }
        });
        window.correctMistakesChart = new Chart(ctx2, {
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
        window.higtScoreChart = new Chart(ctx3, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'スコア',
                        data: higtScores,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: false
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
                    }
                }
            }
        });
    }
    /*------------------------------------------------
        ミスのキーハイライト
    ------------------------------------------------*/
    function keyboardHighlight() {
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
        const mistakesKey = window.data.mistakes;
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
        multiplier = 1 / maxValue;
        console.log('maxValue:', maxValue);
        console.log('Multiplier:', multiplier);
        for (const _key in mistakesKey) {
            if (key[_key]) {
                key[_key].style.backgroundColor = `rgba(255, 0, 0, ${Math.sqrt(multiplier * mistakesPercentage[_key])})`;

                // ツールチップを作成
                const tooltip = document.createElement('span');
                tooltip.className = 'tooltip';
                tooltip.innerText = `ミス率: ${(mistakesPercentage[_key] * 100).toFixed(2)}%`;
                key[_key].appendChild(tooltip);
            }
        }
        console.log('Mistakes Percentage:', mistakesPercentage);
    }
});