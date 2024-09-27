<?php
include('db.php');

// データの取得
$sql = "SELECT record_date, avg_score FROM daily_statistics ORDER BY record_date ASC";
$result = $conn->query($sql);

$dates = [];
$scores = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row['record_date'];
        $scores[] = $row['avg_score'];
    }
}

echo json_encode([
    'dates' => $dates,
    'scores' => $scores
]);
?>
