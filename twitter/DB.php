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

    }

    /**
    * twitterユーザの登録
    */
    public function registerTwitterUser($user) {

        $sql = '';
        $sql = <<<SQL
INSERT INTO `mt_user`
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
`update_at`)
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
:created_at, NOW());
SQL;

        $prepare = $this->instance->prepare($sql);
        $prepare->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
        $prepare->bindParam(':name', $user['name']);
        $prepare->bindParam(':screen_name', $user['screen_name']);
        $prepare->bindParam(':location', $user['location']);
        $prepare->bindParam(':description', $user['description']);
        $prepare->bindParam(':url', $user['url']);
        $prepare->bindParam(':profile_image_url_https', $user['profile_image_url_https']);
        $prepare->bindParam(':followers_count', $user['followers_count'], PDO::PARAM_INT);
        $prepare->bindParam(':friends_count', $user['friends_count'], PDO::PARAM_INT);
        $prepare->bindParam(':listed_count', $user['listed_count'], PDO::PARAM_INT);
        $prepare->bindParam(':created_at', $user['created_at']);

        $prepare->execute();
    }

    /**
    * ツイートの登録
    */
    public function registerTweet($tweet) {

        $sql = '';
        $sql = <<<SQL2
INSERT INTO `t_tweet`(`id`, `user_id`, `text`, `retweet_count`, `favorite_count`, `media`, `created_at`, `update_at`) VALUES (
:id,
:user_id,
:text,
:retweet_count,
:favorite_count,
:media,
:created_at,
NOW()
);
SQL2;

        $prepare = $this->instance->prepare($sql);
        $prepare->bindParam(':id', $user['id'], PDO::PARAM_INT);
        $prepare->bindParam(':user_id', $user['user']['user_id'], PDO::PARAM_INT);
        $prepare->bindParam(':retweet_count', $user['retweet_count'], PDO::PARAM_INT);
        $prepare->bindParam(':favorite_count', $user['favorite_count'], PDO::PARAM_INT);
        $prepare->bindParam(':text', $user['text']);
        $prepare->bindParam(':media', $user['media']);
        $prepare->bindParam(':created_at', $user['created_at']);
        $prepare->execute();
    }

}