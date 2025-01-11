<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// データ範囲の設定（デフォルトは直近5回）
$date_range = isset($_GET['date_range']) && $_GET['date_range'] === 'all' ? 'all' : 'recent';

// 表示する指標の設定（デフォルトはスコア）
$metric = isset($_GET['metric']) ? $_GET['metric'] : 'score';

// 表示モードの設定（デフォルトは通常モード）
$display_mode = isset($_GET['display_mode']) ? $_GET['display_mode'] : 'normal';

// SQLクエリの作成
if ($date_range === 'all') {
    if ($display_mode === 'best') {
        $sql = "SELECT DATE(recorded_at) as date, 
                       MAX(score) as score, 
                       MAX(correct_chars) as correct_chars, 
                       MIN(mistakes) as mistakes, 
                       MAX(accuracy) as accuracy
                FROM game_results 
                WHERE username = ? 
                GROUP BY DATE(recorded_at)
                ORDER BY date ASC";
    } else {
        $sql = "SELECT DATE(recorded_at) as date, score, correct_chars, mistakes, accuracy
                FROM game_results 
                WHERE username = ? 
                ORDER BY recorded_at ASC";
    }
} else {
    if ($display_mode === 'best') {
        $sql = "SELECT DATE(recorded_at) as date, 
                       MAX(score) as score, 
                       MAX(correct_chars) as correct_chars, 
                       MIN(mistakes) as mistakes, 
                       MAX(accuracy) as accuracy
                FROM game_results 
                WHERE username = ? 
                GROUP BY DATE(recorded_at)
                ORDER BY date DESC 
                LIMIT 5";
    } else {
        $sql = "SELECT DATE(recorded_at) as date, score, correct_chars, mistakes, accuracy
                FROM game_results 
                WHERE username = ? 
                ORDER BY recorded_at DESC 
                LIMIT 5";
    }
}

// SQLクエリの実行
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$dates = [];
$scores = [];
$correct_chars = [];
$mistakes = [];
$accuracies = [];
$rankings = [];
$avg_scores = [];
$avg_correct_chars = [];
$avg_mistakes = [];
$avg_accuracies = [];
$last_date = null;

while ($row = $result->fetch_assoc()) {
    $current_date = $row['date'];
    if ($current_date !== $last_date) {
        $dates[] = $current_date;
        $last_date = $current_date;
    } else {
        $dates[] = '';
    }
    $scores[] = $row['score'];
    $correct_chars[] = $row['correct_chars'];
    $mistakes[] = $row['mistakes'];
    $accuracies[] = $row['accuracy'];
    
    if ($display_mode === 'best') {
        // ランキングの取得
        $rank_sql = "SELECT COUNT(*) + 1 AS rank FROM game_results 
                     WHERE DATE(recorded_at) = ? AND score > ?";
        $rank_stmt = $conn->prepare($rank_sql);
        $rank_stmt->bind_param("si", $current_date, $row['score']);
        $rank_stmt->execute();
        $rank_result = $rank_stmt->get_result();
        $rank_row = $rank_result->fetch_assoc();
        $rankings[] = $rank_row['rank'];
        $rank_stmt->close();

        // 平均値の取得
        $avg_sql = "SELECT AVG(score) as avg_score, AVG(correct_chars) as avg_correct_chars, 
                    AVG(mistakes) as avg_mistakes, AVG(accuracy) as avg_accuracy 
                    FROM game_results WHERE DATE(recorded_at) = ?";
        $avg_stmt = $conn->prepare($avg_sql);
        $avg_stmt->bind_param("s", $current_date);
        $avg_stmt->execute();
        $avg_result = $avg_stmt->get_result();
        $avg_row = $avg_result->fetch_assoc();
        $avg_scores[] = round($avg_row['avg_score'], 2);
        $avg_correct_chars[] = round($avg_row['avg_correct_chars'], 2);
        $avg_mistakes[] = round($avg_row['avg_mistakes'], 2);
        $avg_accuracies[] = round($avg_row['avg_accuracy'], 2);
        $avg_stmt->close();
    } else {
        $rankings[] = null;
        $avg_scores[] = null;
        $avg_correct_chars[] = null;
        $avg_mistakes[] = null;
        $avg_accuracies[] = null;
    }
}

// 日付の逆順処理を削除（全期間表示の場合は昇順のまま）
if ($date_range !== 'all') {
    $dates = array_reverse($dates);
    $scores = array_reverse($scores);
    $correct_chars = array_reverse($correct_chars);
    $mistakes = array_reverse($mistakes);
    $accuracies = array_reverse($accuracies);
    $rankings = array_reverse($rankings);
    $avg_scores = array_reverse($avg_scores);
    $avg_correct_chars = array_reverse($avg_correct_chars);
    $avg_mistakes = array_reverse($avg_mistakes);
    $avg_accuracies = array_reverse($avg_accuracies);
}

// 間違えたキーのデータを取得
$mistakes_sql = "SELECT top_mistakes FROM game_results WHERE username = ?";
if ($date_range !== 'all') {
    $mistakes_sql .= " ORDER BY recorded_at DESC LIMIT 5";
}
$mistakes_stmt = $conn->prepare($mistakes_sql);
$mistakes_stmt->bind_param("s", $username);
$mistakes_stmt->execute();
$mistakes_result = $mistakes_stmt->get_result();

$mistakes_data = [];

while ($row = $mistakes_result->fetch_assoc()) {
    $top_mistakes = explode(',', $row['top_mistakes']);
    foreach ($top_mistakes as $mistake) {
        $mistake_parts = explode(':', $mistake);
        if (count($mistake_parts) == 2) {
            $key = $mistake_parts[0];
            $count = intval($mistake_parts[1]);
            if (!isset($mistakes_data[$key])) {
                $mistakes_data[$key] = 0;
            }
            $mistakes_data[$key] += $count;
        }
    }
}

// 間違えたキーの上位5つを取得
arsort($mistakes_data);
$top_5_mistakes = array_slice($mistakes_data, 0, 5, true);

$stmt->close();
$mistakes_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>マイページ</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">
        <span>ログイン中: <?php echo $_SESSION['username']; ?></span>
        <a href="typing/typing_game.html">ゲームに戻る</a>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
            <a href="admin.php">管理者ページ</a>
        <?php endif; ?>
        <a href="user_settings/user_settings.html">ユーザ設定</a>
        <a href="logout.php">ログアウト</a>
    </div>

    <h2>マイページ</h2>

    <div class="toggle-container">
        <label>
            <input type="checkbox" id="dateRangeToggle" <?php echo $date_range === 'all' ? 'checked' : ''; ?>>
            全期間表示
        </label>
        <label>
            <input type="checkbox" id="displayModeToggle" <?php echo $display_mode === 'best' ? 'checked' : ''; ?>>
            最高記録表示
        </label>
    </div>

    <div class="toggle-container">
        <label>
            <input type="radio" name="metricToggle" value="score" <?php echo $metric === 'score' ? 'checked' : ''; ?>> スコア
        </label>
        <label>
            <input type="radio" name="metricToggle" value="correct_chars" <?php echo $metric === 'correct_chars' ? 'checked' : ''; ?>> 正しく打てた文字数
        </label>
        <label>
            <input type="radio" name="metricToggle" value="mistakes" <?php echo $metric === 'mistakes' ? 'checked' : ''; ?>> 間違った文字数
        </label>
        <label>
            <input type="radio" name="metricToggle" value="accuracy" <?php echo $metric === 'accuracy' ? 'checked' : ''; ?>> 正確性
        </label>
    </div>

    <div id="chartContainer" style="width: 80%; margin: 0 auto;">
        <canvas id="myChart"></canvas>
    </div>

    <div id="mistakesChartContainer" style="width: 80%; margin: 20px auto;">
        <h3>間違えやすいキー（上位5つ）</h3>
        <canvas id="mistakesChart"></canvas>
    </div>

    <script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($dates); ?>,
            datasets: [
                {
                    label: '<?php echo ucfirst($metric); ?>',
                    data: <?php 
                        switch($metric) {
                            case 'score':
                                echo json_encode($scores);
                                break;
                            case 'correct_chars':
                                echo json_encode($correct_chars);
                                break;
                            case 'mistakes':
                                echo json_encode($mistakes);
                                break;
                            case 'accuracy':
                                echo json_encode($accuracies);
                                break;
                        }
                    ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                {
                    label: '平均 <?php echo ucfirst($metric); ?>',
                    data: <?php 
                        switch($metric) {
                            case 'score':
                                echo json_encode($avg_scores);
                                break;
                            case 'correct_chars':
                                echo json_encode($avg_correct_chars);
                                break;
                            case 'mistakes':
                                echo json_encode($avg_mistakes);
                                break;
                            case 'accuracy':
                                echo json_encode($avg_accuracies);
                                break;
                        }
                    ?>,
                    borderColor: 'rgb(255, 99, 132)',
                    borderDash: [5, 5],
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        afterLabel: function(context) {
                            var rankings = <?php echo json_encode($rankings); ?>;
                            var rank = rankings[context.dataIndex];
                            return rank !== null ? 'Rank: ' + rank : '';
                        }
                    }
                }
            }
        }
    });

    var mistakesCtx = document.getElementById('mistakesChart').getContext('2d');
    var mistakesChart = new Chart(mistakesCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($top_5_mistakes)); ?>,
            datasets: [{
                label: '間違えた回数',
                data: <?php echo json_encode(array_values($top_5_mistakes)); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.getElementById('dateRangeToggle').addEventListener('change', updateChart);
    document.getElementById('displayModeToggle').addEventListener('change', updateChart);
    document.getElementsByName('metricToggle').forEach(function(radio) {
        radio.addEventListener('change', updateChart);
    });

    function updateChart() {
        var dateRange = document.getElementById('dateRangeToggle').checked ? 'all' : 'recent';
        var displayMode = document.getElementById('displayModeToggle').checked ? 'best' : 'normal';
        var metric = document.querySelector('input[name="metricToggle"]:checked').value;
        window.location.href = 'mypage.php?date_range=' + dateRange + '&metric=' + metric + '&display_mode=' + displayMode;
    }
    </script>
</body>
</html>