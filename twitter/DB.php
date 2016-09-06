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
    public function queryWord($word) {

        $prepare = $this->instance->prepare("SELECT * FROM `t_tweet` WHERE `text` LIKE :word ORDER BY `t_tweet`.`retweet_count` DESC");
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
INSERT INTO `t_tweet`(`id`, `user_id`, `text`, `retweet_count`, `favorite_count`, `media`, `created_at`, `update_at`) VALUES (
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