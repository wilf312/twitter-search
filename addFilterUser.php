<?

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/twitter/config.php';
require __DIR__ . '/twitter/DB.php';

$db = new DB();
if (!$db->instance) {
    renderJSON([
        'success' => false,
        'error' => 'DBコネクションエラー',
        ]);
}



$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;

if ($id === null) {
    renderJSON([
        'success' => false,
        'error' => 'エラー',
        ]);
}


try {
    // ツイートユーザの登録
    $data = $db->addFilterUser($id);
}
catch (PDOException $e) {
    $error = $e->getMessage();

    error_log($error);

    renderJSON([
        'success' => false,
        'error' => 'DBエラー',
        ]);
}


renderJSON([
        'success' => true,
        'data' => $data,
        ]);
