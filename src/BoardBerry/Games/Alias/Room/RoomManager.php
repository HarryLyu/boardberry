<?php
namespace BoardBerry\Games\Alias\Room;


class RoomManager {
    public $redis;

    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function createRoom()
    {

    }
}