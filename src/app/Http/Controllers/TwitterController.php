<?php

namespace App\Http\Controllers;

use App\Http\Vender\CallTwitterApi;

class TwitterController extends Controller
{
    private CallTwitterApi $t;

    public function __construct()
    {
        $this->t = new CallTwitterApi();
    }

    /**
     * ユーザーリストを取得
     *
     * @return array
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    public function getUsers(): array
    {
        return $this->t->getUsers(1570706044879511553);
    }
}
