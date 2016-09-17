<?


$search = isset($_GET['q']) && trim($_GET['q']) !== '' ? trim($_GET['q']) : '#深夜の真剣お絵描き60分一本勝負';
$isDistinct = isset($_GET['distinct']) && trim($_GET['distinct']) === 'on' ? true : false;


require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/twitter/config.php';
require __DIR__ . '/twitter/DB.php';

$db = new DB();
if (!$db->instance) {
    exit('DBの接続失敗');
}


try {
    // ツイートユーザの登録
    if ($isDistinct) {
        $tweetList = $db->queryWordDistinct($search);
    }
    else {
        $tweetList = $db->queryWord($search);
    }

    // 除外ユーザの取得

    $filterUser = $db->queryFilterUser();


    $output = [];
    foreach($tweetList as $key => $tweet) {
        $filtered = false;

        foreach($filterUser as $key => $user) {
            if ($user['status'] === 1 &&
                $tweet['user_id'] === $user['user_id']) {
                $filtered = true;
                break;
            }
        }

        if (!$filtered) {
            array_push($output, $tweet);
        }
    }


} catch (PDOException $e) {
    $error = $e->getMessage();

    error_log($error);
}

// JSONファイルのheader
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");

echo json_encode($output, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

exit;