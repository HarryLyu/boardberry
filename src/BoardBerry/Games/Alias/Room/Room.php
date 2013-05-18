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

    protected $simpleLoadFields = ['state', 'ownerId', 'teamCount', 'playerCount'];

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

    public function setState($state)
    {
        $this->state = $state;
        $this->redis->hset($this->roomKey, 'state', $state);
    }

    public function addPlayer($playerId)
    {
        $this->redis->sadd($this->roomPlayersKey, $playerId);
        $this->playerCount = $this->redis->hincrby($this->roomKey, 'playerCount', 1);
        $this->players[] = new Player($playerId);
    }

    public function addTeam()
    {
        $this->teamCount = $this->redis->hincrby($this->roomKey, 'teamCount', 1);
        $this->teams[] = new Team($this->teamCount - 1);
    }

    public function restore()
    {
        foreach ($this->simpleLoadFields as $fieldName) {
            $this->$fieldName = $this->redis->hget($this->roomKey, $fieldName);
        }

        $this->players = [];
        $playerIds = $this->redis->smembers($this->roomPlayersKey);
        foreach ($playerIds as $playerId) {
            $this->players[] = new Player($playerId);
        }

        $this->teams = [];
        for ($i = 0; $i < $this->teamCount; $i++) {
            $this->teams[] = new Team($i);
        }
    }
}