<?php
session_start();
include('db.php');

if (!isset($_SESSION['username']) || $_SESSION['admin'] != 1) {
    header("Location: login.php");
    exit();
}

// 今日の日付を取得
$today = date('Y-m-d');

// 日付選択用の処理
$selected_date = isset($_GET['date']) ? $_GET['date'] : $today;

// メトリック選択用の処理
$metrics = ['score', 'correct_chars', 'mistakes', 'accuracy'];
$selected_metric = isset($_GET['metric']) && in_array($_GET['metric'], $metrics) ? $_GET['metric'] : 'score';

// 選択された日付とメトリックに基づいてランキングを取得
$sql_ranking = "
    SELECT username, 
           CASE 
               WHEN ? = 'mistakes' THEN MIN($selected_metric)
               ELSE MAX($selected_metric)
           END AS metric_value
    FROM game_results
    WHERE DATE(recorded_at) = ?
    GROUP BY username
    ORDER BY metric_value " . ($selected_metric == 'mistakes' ? 'ASC' : 'DESC') . "
    LIMIT 10
";

$stmt = $conn->prepare($sql_ranking);
$stmt->bind_param("ss", $selected_metric, $selected_date);
$stmt->execute();
$result_ranking = $stmt->get_result();

// ユーザー一覧を取得
$sql_users = "SELECT username FROM users ORDER BY username";
$result_users = $conn->query($sql_users);

// 新しいクエリを追加してInquiryテーブルからデータを取得
$sql_inquiries = "SELECT username, message, created_at FROM Inquiry ORDER BY created_at DESC";
$result_inquiries = $conn->query($sql_inquiries);
?>

<!DOCTYPE html>
<html>
<head>
    <title>管理者ページ</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="header">
        <span>ログイン中: <?php echo $_SESSION['username']; ?></span>
        <a href="game.php">ゲームに戻る</a>
        <a href="mypage.php">マイページ</a>
        <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
            <a href="admin.php">管理者ページ</a>
        <?php endif; ?>
        <a href="logout.php">ログアウト</a>
    </div>

    <h2>管理者ページ</h2>

    <div id="ranking">
        <h3>日別ランキング（ユーザーごとの最高記録）</h3>
        <form action="" method="get">
            <input type="date" name="date" value="<?php echo $selected_date; ?>">
            <select name="metric">
                <option value="score" <?php echo $selected_metric == 'score' ? 'selected' : ''; ?>>スコア</option>
                <option value="correct_chars" <?php echo $selected_metric == 'correct_chars' ? 'selected' : ''; ?>>正しく打てた文字数</option>
                <option value="mistakes" <?php echo $selected_metric == 'mistakes' ? 'selected' : ''; ?>>間違えた文字数</option>
                <option value="accuracy" <?php echo $selected_metric == 'accuracy' ? 'selected' : ''; ?>>正確性</option>
            </select>
            <input type="submit" value="表示">
        </form>
        <ol>
            <?php
            if ($result_ranking->num_rows > 0) {
                while ($row = $result_ranking->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['username']) . ": " . htmlspecialchars($row['metric_value']) . "</li>";
                }
            } else {
                echo "<li>データなし</li>";
            }
            ?>
        </ol>
    </div>

    <div id="inquiries">
        <h3>ユーザーからのご意見</h3>
        <ol>
            <?php
            if ($result_inquiries->num_rows > 0) {
                while ($row = $result_inquiries->fetch_assoc()) {
                    echo "<li>" . htmlspecialchars($row['username']) . ": " . htmlspecialchars($row['message']) . " (送信日時: " . htmlspecialchars($row['created_at']) . ")</li>";
                }
            } else {
                echo "<li>データなし</li>";
            }
            ?>
        </ol>
    </div>

    <div id="user-select">
        <h3>ユーザーのマイページを表示</h3>
        <form action="view_user.php" method="get">
            <select name="username">
                <?php
                while ($row = $result_users->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['username']) . "'>" . htmlspecialchars($row['username']) . "</option>";
                }
                ?>
            </select>
            <input type="submit" value="表示">
        </form>
    </div>
</body>
</html>