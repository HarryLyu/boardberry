<?php

namespace BoardBerry\Common\User;

class UserManager
{

    const VERSION_CACHE = 1;

    private function generateKey($uid)
    {
        return PROJECT_NAME . ':USER:' . $uid . ':' . self::VERSION_CACHE;
    }

    private function getAuthFields()
    {
        return [
            'accessToken',
            'userID',
            'signedRequest'
        ];
    }

    private function getUserFields()
    {
        return [
            'name',
            'first_name',
            'last_name',
            'link',
            'username'
        ];

    }

    /**
     * @var $redis \Redis
     */
    private $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function register($fbAuth, $fbUser)
    {


        if (!isset($fbAuth['authResponse']['userID'])) {
            throw new \Exception('userID failed');
        } else {
            $userID = $fbAuth['authResponse']['userID'];
        }

        $toSave = [];
        foreach (self::getAuthFields() as $field) {
            if (isset($fbAuth['authResponse'][$field])) {
                $toSave[$field] = $fbAuth['authResponse'][$field];
            }
        }

        foreach (self::getUserFields() as $field) {
            if (isset($fbUser[$field])) {
                $toSave[$field] = $fbUser[$field];
            }
        }

        $ok = $this->redis->hMset(self::generateKey($userID), $toSave);
        if($ok) {
            return $toSave;
        }   else {
            throw new \Exception('redis save failed');
        }
    }


    public function get($fbId)
    {

        return $this->redis->hGetAll(self::generateKey($fbId));
    }

    public function set($fbId, $data)
    {
        return true;
    }

}