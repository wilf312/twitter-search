<?/**
 * クラス
 *
 * @author  岡田玄哉 <g.okada.wilf@gmail.com>
 * @create  2013/05/15
 * @version v 0.5 2013/05/15 17:17:38 Okada
 **/

use mpyw\Co\Co;
use mpyw\Co\CURLException;
use mpyw\Cowitter\Client;
use mpyw\Cowitter\HttpException;

Class Twitter {
    private $client;
    private $SEARCH_NUM = 100;
    private $LIMIT_NUM = 5;
    private $tweetList = [];
    public $formattedList = [];

    /**
    * __constructの説明
    *
    * @param string $arg 第1引数
    * @return array 戻り値
    */
    public function __construct() {
        $this->client = new Client([CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET]);
    }

    public function fetchSearch($wordSearch) {

        $params = [
            'q' => $wordSearch,
            'count' => $this->SEARCH_NUM,
        ];


        for ($i = 0; $i < $this->LIMIT_NUM; $i++) {


            // ツイート検索実行
            // echo $params['count'] * $i + 1 . " - " . $params['count'] * ($i + 1) . " 件目取得中\n";

            $res = $this->client->get('search/tweets', $params);
            $statuses = $res->statuses;
            $search_metadata = $res->search_metadata;


            $this->tweetList = array_merge($this->tweetList, $statuses);


            // next_results が無ければ処理を終了
            if (!isset($search_metadata->next_results)) {
                break;
            }

            // 先頭の「?」を除去
            $next_results = preg_replace('/^\?/', '', $search_metadata->next_results);

            // next_results が無ければ処理を終了
            if (!$next_results) {
                break;
            }

            // パラメータに変換
            parse_str($next_results, $params);
        }

        return $this->tweetList;
    }

    public function formatList() {

        $statuses = $this->tweetList;
        $data = [];
        foreach($statuses as $key => $val){
            array_push($data, [
                'id' => $val->id_str,
                'created_at' => (new DateTime())->setTimestamp(strtotime($val->created_at))->format('Y-m-d H:i:s'),
                'text' => $val->text,
                'retweet_count' => $val->retweet_count,
                'favorite_count' => $val->favorite_count,
                'media' => ( isset($val->extended_entities) ? $val->extended_entities->media[0]->media_url_https : '' ),
                'user' => [
                    'user_id' => $val->user->id_str,
                    'name' => $val->user->name,
                    'screen_name' => $val->user->screen_name,
                    'location' => $val->user->location,
                    'description' => $val->user->description,
                    'url' => $val->user->url,
                    'created_at' => (new DateTime())->setTimestamp(strtotime($val->user->created_at))->format('Y-m-d H:i:s'),
                    'profile_image_url_https' => $val->user->profile_image_url_https,
                    'followers_count' => $val->user->followers_count,
                    'friends_count' => $val->user->friends_count,
                    'listed_count' => $val->user->listed_count,
                ],
                ]);
        }

        $this->tweetList = null;
        $this->formattedList = $data;

        return $this->formattedList;
    }

    public function save() {

        $db = new DB();
        if (!$db->instance) {
            exit('DBの接続失敗');
        }


        try {

            foreach($this->formattedList as $key => $val){

                // ツイートユーザの登録
                $db->registerTwitterUser($val['user']);

                // ツイート内容の登録
                $db->registerTweet($val);
            }

        } catch (PDOException $e) {
            $error = $e->getMessage();
        }
    }

    public function sendJSON() {
        // JSONファイルのheader
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=utf-8");

        echo json_encode($this->formattedList, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

        exit;
    }

}