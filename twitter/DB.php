<?/**
 * クラス
 *
 * @author  岡田玄哉 <g.okada.wilf@gmail.com>
 * @create  2013/05/15
 * @version v 0.5 2013/05/15 17:17:38 Okada
 **/

Class DB {

    public $instance = null;

    /**
    * DBへ接続
    */
    public function __construct() {
        $this->instance = new PDO(
            DB_NAME,
            DB_USER,
            DB_PASSWORD
        );
        // エラー設定
        $this->instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // DBの型に合わせてオブジェクトを生成する
        $this->instance->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    /**
    * 除外ワードの取得
    */
    public function queryFilterWord() {

        $prepare = $this->instance->prepare("SELECT * FROM m_filter_word");

        $prepare->execute();
        return $prepare->fetchAll();
    }
    /**
    * 除外ユーザの追加
    */
    public function addFilterWord($word) {

        $prepare = $this->instance->prepare("INSERT INTO `m_filter_word` (`word`, `status`) VALUES (:word, '1');");
        $prepare->bindValue(':word', $word, PDO::PARAM_STR);

        $prepare->execute();
        return $prepare->fetchAll();
    }
    /**
    * 除外ユーザの取得
    */
    public function queryFilterUser() {

        $prepare = $this->instance->prepare("SELECT * FROM m_filter_user INNER JOIN mt_user ON m_filter_user.user_id = mt_user.user_id");

        $prepare->execute();
        return $prepare->fetchAll();
    }
    /**
    * 除外ユーザの追加
    */
    public function addFilterUser($user_id) {

        $prepare = $this->instance->prepare("INSERT INTO `m_filter_user` (`id`, `user_id`, `status`) VALUES (NULL, :user_id, 1);");
        $prepare->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $prepare->execute();
        return $prepare->fetchAll();
    }
    /**
    * 除外ユーザの除外ステータスを更新
    * 1: 除外対象 0: 除外ユーザーではない
    */
    public function updateFilterUser($user_id, $status) {

        $prepare = $this->instance->prepare("UPDATE `m_filter_user` SET `status` = :status WHERE `m_filter_user`.`id` = :user_id;");
        $prepare->bindValue(':user_id', "{$user_id}", PDO::PARAM_INT);
        $prepare->bindValue(':status', "{$status}", PDO::PARAM_INT);

        $prepare->execute();
        return $prepare->fetchAll();
    }
    /**
    * 除外ワードの除外ステータスを更新
    * 1: 除外対象 0: 除外ワードーではない
    */
    public function updateFilterWord($word, $status) {

        $prepare = $this->instance->prepare("UPDATE `m_filter_word` SET `status` = :status WHERE `m_filter_word`.`id` = :word;");
        $prepare->bindValue(':word', "{$word}", PDO::PARAM_INT);
        $prepare->bindValue(':status', "{$status}", PDO::PARAM_INT);

        $prepare->execute();
        return $prepare->fetchAll();
    }

    /**
    * 検索
    */
    public function queryWord($word) {

        $prepare = $this->instance->prepare("SELECT t_tweet.text, t_tweet.id, t_tweet.user_id, t_tweet.created_at, t_tweet.retweet_count, t_tweet.favorite_count, t_tweet.media, mt_user.name, mt_user.screen_name, mt_user.profile_image_url, mt_user.followers_count, mt_user.friends_count FROM `t_tweet` INNER JOIN mt_user ON t_tweet.user_id = mt_user.user_id WHERE `text` LIKE :word ORDER BY `id` DESC LIMIT 100");
        $prepare->bindValue(':word', "%{$word}%", PDO::PARAM_STR);

        $prepare->execute();
        return $prepare->fetchAll();
    }

    /**
    * 検索
    * 画像のユニークの値を取る
    *
    */
    public function queryWordDistinct($word) {

        $prepare = $this->instance->prepare("SELECT DISTINCT (t_tweet.media), t_tweet.text, t_tweet.id, t_tweet.user_id, t_tweet.created_at, t_tweet.retweet_count, t_tweet.favorite_count, mt_user.name, mt_user.screen_name, mt_user.profile_image_url, mt_user.followers_count, mt_user.friends_count FROM `t_tweet` INNER JOIN mt_user ON t_tweet.user_id = mt_user.user_id WHERE `text` LIKE :word ORDER BY `id` DESC LIMIT 500");
        $prepare->bindValue(':word', "%{$word}%", PDO::PARAM_STR);

        $prepare->execute();
        return $prepare->fetchAll();
    }

    /**
    * twitterユーザの登録
    */
    public function registerTwitterUser($user) {

        $sql = '';
        $sql = <<<SQL
INSERT IGNORE INTO `mt_user`
(`user_id`,
`name`,
`screen_name`,
`location`,
`description`,
`url`,
`profile_image_url`,
`followers_count`,
`friends_count`,
`listed_count`,
`created_at`,
`updated_at`)
VALUES (:user_id,
:name,
:screen_name,
:location,
:description,
:url,
:profile_image_url_https,
:followers_count,
:friends_count,
:listed_count,
:created_at,
:updated_at);
SQL;

        $prepare = $this->instance->prepare($sql);
        $prepare->bindValue(':user_id', $user['user_id'], PDO::PARAM_INT);
        $prepare->bindValue(':name', $user['name']);
        $prepare->bindValue(':screen_name', $user['screen_name']);
        $prepare->bindValue(':location', $user['location']);
        $prepare->bindValue(':description', $user['description']);
        $prepare->bindValue(':url', $user['url']);
        $prepare->bindValue(':profile_image_url_https', $user['profile_image_url_https']);
        $prepare->bindValue(':followers_count', $user['followers_count'], PDO::PARAM_INT);
        $prepare->bindValue(':friends_count', $user['friends_count'], PDO::PARAM_INT);
        $prepare->bindValue(':listed_count', $user['listed_count'], PDO::PARAM_INT);
        $prepare->bindValue(':created_at', $user['created_at']);
        $prepare->bindValue(':updated_at', (new DateTime())->format('Y-m-d H:i:s'));

        $prepare->execute();
    }

    /**
    * ツイートの登録
    */
    public function registerTweet($tweet) {

        $sql = '';
        $sql = <<<SQL2
INSERT IGNORE INTO `t_tweet`(`id`, `user_id`, `text`, `retweet_count`, `favorite_count`, `media`, `created_at`, `updated_at`) VALUES (
:id,
:user_id,
:text,
:retweet_count,
:favorite_count,
:media,
:created_at,
:updated_at
);
SQL2;

        $prepare = $this->instance->prepare($sql);
        $prepare->bindValue(':id', $tweet['id'], PDO::PARAM_INT);
        $prepare->bindValue(':user_id', $tweet['user']['user_id'], PDO::PARAM_INT);
        $prepare->bindValue(':retweet_count', $tweet['retweet_count'], PDO::PARAM_INT);
        $prepare->bindValue(':favorite_count', $tweet['favorite_count'], PDO::PARAM_INT);
        $prepare->bindValue(':text', $tweet['text']);
        $prepare->bindValue(':media', $tweet['media']);
        $prepare->bindValue(':created_at', $tweet['created_at']);
        $prepare->bindValue(':updated_at', (new DateTime())->format('Y-m-d H:i:s'));
        $prepare->execute();
    }

}