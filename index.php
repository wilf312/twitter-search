<?


$qFlag = isset($_GET['q']) && trim($_GET['q']) !== '';

$search = $qFlag ? trim($_GET['q']) : '深夜の真剣お絵描き60分一本勝負';

$isHash = isset($_GET['isHash']) ? true : false;
$lang = isset($_GET['lang']) ? trim($_GET['lang']) : false;

if ($isHash || !$qFlag) {
    $search = '#'. $search;
}

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

$tw = new Twitter($lang);
// サーチ
$statuses = $tw->fetchSearch($search);
// データ正規化
$data = $tw->formatList();
// データ保存
$tw->save();
// 取得したJSONを返す
$tw->sendJSON();

