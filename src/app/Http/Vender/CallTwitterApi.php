<?php

namespace App\Http\Vender;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use Illuminate\Support\Facades\Log;

class CallTwitterApi
{
    private TwitterOAuth $t;

    private array $users = [];

    /**
     * ユーザーリストを取得
     *
     * @param $tweetId
     * @return array
     * @throws TwitterOAuthException
     */
    public function getUsers($tweetId): array
    {
        foreach ($this->getReTweetUserIdList($tweetId) as $reTweetUserId) {
            if (!in_array($reTweetUserId, $this->users, true)) {
                foreach ($this->getTweetsFromUserId($reTweetUserId) as $tweet) {
                    if ($this->getTweetFavoriteFromId($tweet->id) >= 10) {
                        $this->users[] = $reTweetUserId;
                        break;
                    }
                }
            }
        }

        return $this->users;
    }

    /**
     * 特定ツイートに対してリツイートをしたユーザーIDのみを抽出
     *
     * @param $id
     * @return mixed
     */
    private function getReTweetUserIdList($id): mixed
    {
        $this->t = $this->generateOAuth();

        $d = $this->t->get('statuses/retweeters/ids', [
            'id' => $id
        ]);

        return array_unique($d->ids);
    }

    /**
     * UserIdから該当ユーザーのツイートを取得
     *
     * @param $userId
     * @return mixed
     * @throws TwitterOAuthException
     */
    public function getTweetsFromUserId($userId): mixed
    {
        $this->t = $this->generateOAuth();
        $this->setApiVersion2();
        return $this->t->get("users/".$userId."/tweets", ['max_results' => 30])->data;
    }

    /**
     * TweetIdからTweetのいいね数を取得
     *
     * @param $id
     * @return mixed
     */
    private function getTweetFavoriteFromId($id): mixed
    {
        $this->t = $this->generateOAuth();
        return $this->t->get("statuses/show/".$id)->favorite_count;
    }

    /**
     * API2をセットする
     *
     * @return void
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    private function setApiVersion2(): void
    {
        $this->t->setApiVersion(2);
    }

    /**
     * TwitterOAuthインスタンスを取得
     *
     * @return TwitterOAuth
     */
    private function generateOAuth(): TwitterOAuth
    {
        return new TwitterOAuth(
            env('TWITTER_CLIENT_ID'),
            env('TWITTER_CLIENT_SECRET'),
            env('TWITTER_CLIENT_ID_ACCESS_TOKEN'),
            env('TWITTER_CLIENT_ID_ACCESS_TOKEN_SECRET'));
    }
}
