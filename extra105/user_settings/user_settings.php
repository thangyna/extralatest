<?php
/*------------------------------------------------
    セッションを開始
------------------------------------------------*/
session_start();
include("../db.php");

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
    データ取得のsqlを構築, 実行
------------------------------------------------*/
function createLoadSql($_username, $_col) {
    global $conn;
    // sql文を構築
    $sql = "SELECT $_col FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    // 値をバインド
    $stmt->bind_param("s", $_username);

    // クエリの実行
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/*------------------------------------------------
    データ送信のsqlを構築, 実行
------------------------------------------------*/
function createSaveSql($_username, $_col, $_type, $_value) {
    global $conn;
    // sql文を構築
    $sql = "UPDATE users SET $_col = ? WHERE username = ?";
    $stmt = $conn->prepare($sql);
    // 値をバインド
    $stmt->bind_param($_type . 's', $_value, $_username);

    // クエリの実行
    if ($stmt->execute()) {
        // echo "更新しました";
    } else {
        // echo "更新に失敗しました";
    }
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
// デバッグモードの設定
$debug = false;
$whereToRedirect = "user_settings.html";

// ユーザ情報の取得
$username = getUserInfo();

// データの更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 最低スコアの使用
    if (isset($_POST["useMinScore"])){
        createSaveSql($username, "useMinScore", "i", $_POST["useMinScore"] == "on");
    }
    else {
        createSaveSql($username, "useMinScore", "i", 0);
    }

    // 最低スコア
    if (isset($_POST["minScore"])){
        createSaveSql($username, "minScore", "i", $_POST["minScore"]);
    }

    // キーボードの表示
    if (isset($_POST["showKeyboard"])){
        createSaveSql($username, "showKeyboard", "i", $_POST["showKeyboard"] == "on");
    }
    else {
        createSaveSql($username, "showKeyboard", "i", 0);
    }

    if (isset($_POST["missHighlight"])){
        createSaveSql($username, "missHighlight", "i", $_POST["missHighlight"] == "on");
    }
    else {
        createSaveSql($username, "missHighlight", "i", 0);
    }

    // デバッグモードの使用に応じてリダイレクト
    if (!$debug) {
        header("Location: " . $whereToRedirect);
        exit();
    }
}

// データの取得, 出力
$settings = createLoadSql($username, "username, admin, useMinScore, minScore, showKeyboard, exp, missHighlight");
header('Content-Type: application/json');
echo json_encode($settings);
?>