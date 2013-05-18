<?php
namespace BoardBerry\Games\Alias\Game\Room;

class RoomManager
{
    public $redis;
    /** @var RoomIdGenerator */
    public $roomIdGenerator;

    public function __construct($redis, $roomIdGenerator)
    {
        $this->redis = $redis;
        $this->roomIdGenerator = $roomIdGenerator;
    }

    public function createRoom($ownerId)
    {
        $roomId = $this->roomIdGenerator->getId();

        $room = new Room($this->redis, $this->roomIdGenerator, $roomId);
        $room->init($ownerId);

        return $room;
    }

    public function getRoom($roomId)
    {
        $room = new Room($this->redis, $this->roomIdGenerator, $roomId);
        $room->restore();

        return $room;
    }
}