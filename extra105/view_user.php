<?php
session_start();
include('db.php');

if (!isset($_SESSION['username']) || $_SESSION['admin'] != 1) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['username'])) {
    header("Location: admin.php");
    exit();
}

$username = $_GET['username'];

// データ範囲の設定（デフォルトは全期間）
$date_range = isset($_GET['date_range']) && $_GET['date_range'] === 'recent' ? 'recent' : 'all';

// 表示する指標の設定（デフォルトはスコア）
$metric = isset($_GET['metric']) ? $_GET['metric'] : 'score';

// SQLクエリの作成
if ($date_range === 'recent') {
    $sql = "SELECT score, correct_chars, mistakes, accuracy, top_mistakes, recorded_at 
            FROM game_results 
            WHERE username = ? 
            ORDER BY recorded_at DESC 
            LIMIT 5";
} else {
    $sql = "SELECT score, correct_chars, mistakes, accuracy, top_mistakes, recorded_at 
            FROM game_results 
            WHERE username = ? 
            ORDER BY recorded_at ASC";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
$dates = [];
$mistakes_data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row[$metric];
    $dates[] = date('Y-m-d', strtotime($row['recorded_at']));
    
    // 間違えたキーのデータを処理
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

if ($date_range === 'recent') {
    // 直近5回のデータを古い順に並べ替え
    $data = array_reverse($data);
    $dates = array_reverse($dates);
}

// 間違えたキーの上位5つを取得
arsort($mistakes_data);
$top_5_mistakes = array_slice($mistakes_data, 0, 5, true);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($username); ?>のマイページ</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="header">
        <span>管理者: <?php echo $_SESSION['username']; ?></span>
        <a href="admin.php">管理者ページに戻る</a>
        <a href="game.php">ゲームに戻る</a>
        <a href="logout.php">ログアウト</a>
    </div>

    <h2><?php echo htmlspecialchars($username); ?>のマイページ</h2>

    <div>
        <label>
            <input type="checkbox" id="dateRangeToggle" <?php echo $date_range === 'all' ? 'checked' : ''; ?>>
            全期間表示
        </label>
    </div>

    <div>
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

    <div id="chartContainer" style="width: 80%;">
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
            datasets: [{
                label: '<?php echo ucfirst($metric); ?>',
                data: <?php echo json_encode($data); ?>,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
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
                        text: '<?php echo ucfirst($metric); ?>'
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

    document.getElementById('dateRangeToggle').addEventListener('change', function() {
        updateChart();
    });

    document.getElementsByName('metricToggle').forEach(function(radio) {
        radio.addEventListener('change', function() {
            updateChart();
        });
    });

    function updateChart() {
        var dateRange = document.getElementById('dateRangeToggle').checked ? 'all' : 'recent';
        var metric = document.querySelector('input[name="metricToggle"]:checked').value;
        window.location.href = 'view_user.php?username=<?php echo urlencode($username); ?>&date_range=' + dateRange + '&metric=' + metric;
    }
    </script>
</body>
</html>