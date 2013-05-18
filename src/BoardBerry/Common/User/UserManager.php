<?php

namespace BoardBerry\Common\User;

class UserManager
{

    const VERSION_CACHE = 1;

    private function generateKey($uid, $data)
    {
        return 'USER:' . $uid . ':' . $data . ':' . self::VERSION_CACHE;
    }

    private function getAuthFields(){
        return [
            'accessToken',
            'userID',
            'signedRequest'
        ];
    }

    private function getUserFields(){
        $fileds = [
            'name',
            'first_name',
            'last_name',
            'link',
            'username'
        ];

        $ret = [];
        foreach($fileds as )
    }

    private function getAllFields() {
        return self::getUserFields()+self::getAuthFields();
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

        //check exists
        if($exist = $this->redis->get(self::generateKey($userID,'userID'))) {
            throw new \Exception('User exists');
        }

        $toSave = [];
        foreach (self::getAuthFields() as $field) {
            if (isset($fbAuth['authResponse'][$field])) {
                $toSave[self::generateKey($userID, $field)] = $fbAuth['authResponse'][$field];
            }
        }

        foreach (self::getUserFields() as $field) {
            if (isset($fbUser[$field])) {
                $toSave[self::generateKey($userID, $field)] = $fbUser[$field];
            }
        }

        $this->redis->mset($toSave);
    }


    public function get($fbId)
    {
        return $this->redis->mget();
    }

    public function set($fbId, $data)
    {
        return true;
    }

}