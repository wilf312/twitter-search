<?


$search = isset($_GET['q']) && trim($_GET['q']) !== '' ? trim($_GET['q']) : '#深夜の真剣お絵描き60分一本勝負';


/********************************
* 宣言部
*/
date_default_timezone_set('Asia/Tokyo');

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}

// ------ Twitter

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/twitter/config.php';
require __DIR__ . '/twitter/Twitter.php';
require __DIR__ . '/twitter/DB.php';

$tw = new Twitter();
// サーチ
$statuses = $tw->fetchSearch($search);
// データ正規化
$data = $tw->formatList();
// データ保存
$tw->save();
// 取得したJSONを返す
$tw->sendJSON();
