<?


$search = isset($_GET['q']) && trim($_GET['q']) !== '' ? trim($_GET['q']) : '#深夜の真剣お絵描き60分一本勝負';

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/twitter/config.php';
require __DIR__ . '/twitter/DB.php';

$db = new DB();
if (!$db->instance) {
    exit('DBの接続失敗');
}


try {
    $tweetList = $db->getMedia($search);

} catch (PDOException $e) {
    $error = $e->getMessage();

    error_log($error);
}

// JSONファイルのheader
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

echo json_encode($tweetList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

exit;