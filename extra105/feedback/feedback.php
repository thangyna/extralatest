<?php
session_start();
include('../db.php');

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
    データ送信のsqlを構築, 実行
------------------------------------------------*/
function createSendSql($_username, $_message) {
    global $conn;
    // sql文を構築
    $stmt = "INSERT INTO Inquiry (username, message) VALUES (?, ?)";
    $stmt = $conn->prepare($stmt);
    // 値をバインド
    $stmt->bind_param("ss", $_username, $_message);

    // クエリの実行
    $stmt->execute();
    $stmt->close();

}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
// デバッグモードの設定
$debug = false;
$whereToRedirect = "../typing/typing_game.html";

// ユーザ情報の取得
$username = getUserInfo();

// データの更新
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    createSendSql($username, $message);
    header('Location: ' . $whereToRedirect);
    exit();
}
?>
