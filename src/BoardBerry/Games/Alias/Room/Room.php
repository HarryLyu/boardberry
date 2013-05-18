<?php
namespace BoardBerry\Games\Alias\Room;

use BoardBerry\Games\Alias\Room\Players\Player;
use BoardBerry\Games\Alias\Room\Teams\Team;

class Room
{

    const CHANNEL_NAME = 'BCAST_ROOM_';
    public $redis;

    /** @var RoomIdGenerator */
    public $roomIdGenerator;

    protected $roomKey;
    protected $roomPlayersKey;

    public $state;

    public $roomId;
    public $ownerId;

    public $players;
    public $playerCount;

    public $teams;
    public $teamCount;

    public function __construct($redis, $roomIdGenerator, $roomId)
    {
        $this->roomId = $roomId;
        $this->roomIdGenerator = $roomIdGenerator;
        $this->redis = $redis;

        $this->roomKey = $this->roomIdGenerator->getRoomKey($this->roomId);
        $this->roomPlayersKey = $this->roomKey . ':PLAYERS';
    }

    public function init($ownerId)
    {
        $this->ownerId = $ownerId;
        $this->redis->hset($this->roomKey, 'ownerId', $this->ownerId);
    }

    public function addPlayer($playerId)
    {
        $this->redis->hset($this->roomPlayersKey, 'player:' . $playerId, $this->roomId);
        $this->playerCount = $this->redis->hincrby($this->roomKey, 'playerCount', 1);
        $this->players[] = new Player($playerId);
    }

    public function addTeam()
    {
        $this->teamCount = $this->redis->hincrby($this->roomKey, 'teamCount', 1);
        $this->teams[] = new Team($this->teamCount - 1);
    }

    public function setState($state)
    {
        $this->state = $state;
        $this->redis->hset($this->roomKey, 'state', $state);
    }
}