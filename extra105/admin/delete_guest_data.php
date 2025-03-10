<?php
/*------------------------------------------------
    セッションを開始
------------------------------------------------*/
session_start();
include('../db.php');

/*------------------------------------------------
    ユーザ情報を取得
------------------------------------------------*/
function getUserInfo()
{
    // セッションにユーザ名が保存されていない場合、ログインページにリダイレクト
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }
    // それ以外の場合、ユーザ名を取得
    else {
        return $_SESSION['username'];
    }
}

/*------------------------------------------------
    ゲストデータ削除のsqlを構築, 実行
------------------------------------------------*/
function deleteGuestData()
{
    global $conn;
    // sql文を構築
    $sql = "DELETE FROM game_results WHERE guest = 1";
    $stmt = $conn->prepare($sql);

    // クエリの実行
    if ($stmt->execute()) {
        return ['status' => 'success', 'message' => 'ゲストのプレイデータが削除されました。'];
    } else {
        return ['status' => 'error', 'message' => 'データ削除に失敗しました。'];
    }
}

/*------------------------------------------------
    処理の開始
------------------------------------------------*/
header('Content-Type: application/json');
$username = getUserInfo();

try {
    $response = deleteGuestData();
    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>