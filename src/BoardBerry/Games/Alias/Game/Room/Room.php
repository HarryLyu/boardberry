<?php
namespace BoardBerry\Games\Alias\Game\Room;

use BoardBerry\Games\Alias\Game\Room\Players\Player;
use BoardBerry\Games\Alias\Game\Room\Teams\Team;

class Room
{
    public $redis;

    /** @var RoomIdGenerator */
    public $roomIdGenerator;

    protected $roomKey;
    protected $roomPlayersKey;
    protected $playerRoomKey;
    protected $teamKey;
    protected $roomWordSetKey;

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
        $this->playerRoomKey = PROJECT_NAME . ':PLAYER-ROOM';
        $this->teamKey = $this->roomKey . ':TEAMS';
        $this->roomWordSetKey = $this->roomKey . ':WORDSET';
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

    public function joinPlayerToRoom($playerId)
    {
        $result = $this->redis->sadd($this->roomPlayersKey, $playerId);

        if ($result) {
            $this->redis->hset($this->playerRoomKey, $playerId, $this->roomId);

            $this->playerCount = $this->redis->hincrby($this->roomKey, 'playerCount', 1);
            $this->players[] = new Player($playerId);
        }

        return $result;
    }

    public function addTeam()
    {
        $this->teamCount = $this->redis->hincrby($this->roomKey, 'teamCount', 1);
        $this->teams[] = new Team($this->teamCount - 1);

        return $this->teamCount - 1;
    }

    public function joinPlayerToTeam($teamId, $playerId)
    {
        $prevTeam = $this->redis->hget($this->teamKey, $playerId);
        if ($prevTeam !== false) {
            $this->teams[$prevTeam]->removePlayer($playerId);
        }

        $this->redis->hset($this->teamKey, $playerId, $teamId);
        $this->teams[$teamId]->addPlayer($playerId);
    }

    public function saveWordSet($words)
    {
        $this->redis->rpush($this->roomWordSetKey, $words);
    }

    public function getWordSetForTurn()
    {
        return $this->redis->lrange(0, 50);
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

        $players = $this->redis->hgetall($this->teamKey);
        foreach($players as $playerId => $teamId) {
            $this->teams[$teamId]->addPlayer($playerId);
        }
    }
}