<?


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/twitter/config.php';
require __DIR__ . '/twitter/DB.php';

$db = new DB();
if (!$db->instance) {
    exit('DBの接続失敗');
}


try {
    // ツイートユーザの登録
    $data = $db->queryFilterWord();
} catch (PDOException $e) {
    $error = $e->getMessage();

    error_log($error);
}


renderJSON($data);
