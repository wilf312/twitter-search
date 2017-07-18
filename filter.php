<?


$db = new DB();
if (!$db->instance) {
    exit('DBの接続失敗');
}


try {

    // ツイートユーザの登録
    $data = $db->queryFilterUser();

} catch (PDOException $e) {
    $error = $e->getMessage();

    error_log($error);
}

// JSONファイルのheader
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

exit;