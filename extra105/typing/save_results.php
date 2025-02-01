<?php
/*------------------------------------------------
    セッションを開始
------------------------------------------------*/
session_start();
include('../db.php'); // PDO接続を含むファイルを読み込む

/*------------------------------------------------
    ユーザ情報を取得
------------------------------------------------*/
function getUserInfo()
{
    // セッションにユーザ名が保存されていない場合、ログインページにリダイレクト
    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit();
    }
    // それ以外の場合、ユーザ名を取得
    else {
        return $_SESSION['username'];
    }
}

/*------------------------------------------------
    sqlを構築, 実行
------------------------------------------------*/
function saveResults($_username, $_data) {
    global $conn;
    // sql文を構築
    $sql = "INSERT INTO game_results (username, score, correct_chars, mistakes, accuracy, typing_speed, top_mistakes, ip_address, recorded_at, isDisplay) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // 値をバインドするための配列を準備
    $params = [
        $_username,
        $_data->score,
        $_data->correct_chars,
        $_data->mistakes,
        $_data->accuracy,
        $_data->typing_speed,
        urldecode($_data->top_mistakes),
        $_SERVER['REMOTE_ADDR'],
        date('Y-m-d H:i:s'),
        $_data->is_display
    ];

    // 型定義を準備
    $types = "siiidssssi";

    // 配列の最初に型定義を追加
    array_unshift($params, $types);

    // call_user_func_arrayを使用して値をバインド
    call_user_func_array([$stmt, 'bind_param'], refValues($params));

    // クエリの実行
    $stmt->execute();
    $debugInfo = [
        'username' => $_username,
        'data' => $_data,
        'params' => $params,
        'sql' => $sql,
        'error' => $stmt->error
    ];
    // デバッグ情報をファイルに書き込む
    // file_put_contents('debug.txt', print_r($debugInfo, true), FILE_APPEND);
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// 配列の参照を返すヘルパー関数
function refValues($arr) {
    $refs = [];
    foreach ($arr as $key => $value) {
        $refs[$key] = &$arr[$key];
    }
    return $refs;
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
$username = $_SESSION['username'];
// POSTの生データを取得
$raw = file_get_contents('php://input');
$data = json_decode($raw);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    saveResults($username, $data);
}
?>