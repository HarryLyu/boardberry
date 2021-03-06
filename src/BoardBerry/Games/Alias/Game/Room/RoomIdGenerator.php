<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vitkovskii
 * Date: 5/18/13
 * Time: 5:34 PM
 * To change this template use File | Settings | File Templates.
 */

namespace BoardBerry\Games\Alias\Game\Room;

class RoomIdGenerator
{
    public $redis;

    public function __construct($redis) {
        $this->redis = $redis;
    }

    public function getRoomKey($id){
        return PROJECT_NAME . ':ROOM:' . $id;
    }

    public function getId()
    {
        do {
            $id = rand(1000, 9999);
        } while ($this->redis->exists($this->getRoomKey($id)));

        return $id;
    }
}