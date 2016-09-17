<?


function renderJSON($data) {

    // JSONファイルのheader
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=utf-8");

    echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

    exit;
}