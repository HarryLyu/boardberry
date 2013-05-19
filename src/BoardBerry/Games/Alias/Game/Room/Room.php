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
    protected $roomTeamTurnsKey;
    protected $roomPlayerTurnsKey;

    protected $simpleLoadFields = ['state', 'ownerId', 'teamCount', 'playerCount'];

    public $state;

    public $roomId;
    public $ownerId;

    public $players;
    public $playerCount;

    public $teams;
    public $teamCount;

    public $activeTeamId;
    public $explainerId;

    public function __construct($redis, $roomIdGenerator, $roomId)
    {
        $this->roomId = $roomId;
        $this->roomIdGenerator = $roomIdGenerator;
        $this->redis = $redis;

        $this->roomKey = $this->roomIdGenerator->getRoomKey($this->roomId);
        $this->roomPlayersKey = $this->roomKey . ':PLAYERS';
        $this->playerRoomKey = PROJECT_NAME . ':PLAYER-ROOM';
        $this->teamKey = $this->roomKey . ':TEAMS';
        $this->roomWordSetKey = $this->roomKey . ':WORDPOOL';
        $this->roomTeamTurnsKey = $this->roomKey . ':TEAM-TURNS';
        $this->roomPlayerTurnsKey = $this->roomKey . ':PLAYER-TURNS';
        $this->roomTurnResultsKey = $this->roomKey . ':TURN-RESULTS';
        $this->roomTeamScoresKey = $this->roomKey . ':SCORES';
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

        $teamId = $this->teamCount - 1;
        $this->redis->hset($this->roomTeamScoresKey, $teamId, 0);
        $this->teams[] = new Team($teamId);

        return $teamId;
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

    public function saveWordPool($words)
    {
        foreach ($words as $word) {
            $this->redis->rpush($this->roomWordSetKey, $word);
        }
    }

    public function addTurnQueues()
    {
        $first = true;
        foreach ($this->teams as $team) {
            if (count($team->players) > 0) {
                if ($first) {
                    $this->activeTeamId = $team->id;
                }

                $this->redis->lpush($this->roomTeamTurnsKey, $team->id);

                foreach ($team->players as $playerId => $_) {
                    if ($first) {
                        $first = false;
                        $this->explainerId = $playerId;
                    }
                    $this->redis->lpush($this->roomPlayerTurnsKey . ":" . $team->id, $playerId);
                }
            }
        }
    }

    public function getWordsForTurn()
    {
        return $this->redis->lrange($this->roomWordSetKey, 0, 100);
    }

    public function deleteWordsFromPool($wordsCount)
    {
        return $this->redis->ltrim($this->roomWordSetKey, $wordsCount, -1);
    }

    public function saveResults($results)
    {
        $this->redis->hmset($this->roomTurnResultsKey, $results);
    }

    public function getResults()
    {
        return $this->redis->hGetAll($this->roomTurnResultsKey);
    }

    public function clearResults()
    {
        return $this->redis->del($this->roomTurnResultsKey);
    }

    public function addTeamScore($teamId, $score)
    {
        $value = $this->redis->hincrby($this->roomTeamScoresKey, $teamId, $score);
        if ($value < 0) {
            $value = $this->redis->hset($this->roomTeamScoresKey, $teamId, 0);
        }

        return $value;
    }

    public function getTeamScore($teamId)
    {
        return $this->redis->hget($this->roomTeamScoresKey, $teamId);
    }

    public function getAllTeamScores()
    {
        $scores = $this->redis->hgetall($this->roomTeamScoresKey);
        $scoresRaw = [];
        foreach ($scores as $teamId => $score) {
            if (count($this->teams[$teamId]->players) > 0) {
                $scoresRaw[$teamId] = $score;
            }
        }

        return $scoresRaw;
    }

    public function editResult($wordId)
    {
        $value = $this->redis->hget($this->roomTurnResultsKey, $wordId);
        $value = $value ? 0 : 1;
        $this->redis->hset($this->roomTurnResultsKey, $wordId, $value);

        return $value;
    }

    public function nextTurn()
    {
        $this->redis->rpoplpush(
            $this->roomPlayerTurnsKey . ":" . $this->activeTeamId,
            $this->roomPlayerTurnsKey . ":" . $this->activeTeamId
        );
        $this->redis->rpoplpush($this->roomTeamTurnsKey, $this->roomTeamTurnsKey);

        $this->activeTeamId = $this->redis->lindex($this->roomTeamTurnsKey, -1);
        $this->explainerId = $this->redis->lindex($this->roomPlayerTurnsKey . ":" . $this->activeTeamId, -1);
    }

    public function restore()
    {
        foreach ($this->simpleLoadFields as $fieldName) {
            $this->$fieldName = $this->redis->hget($this->roomKey, $fieldName);
        }

        if ($this->ownerId === false) {
            throw new \Exception('Room not found');
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
        foreach ($players as $playerId => $teamId) {
            $this->teams[$teamId]->addPlayer($playerId);
        }

        $this->activeTeamId = $this->redis->lindex($this->roomTeamTurnsKey, -1);
        $this->explainerId = $this->redis->lindex($this->roomPlayerTurnsKey . ":" . $this->activeTeamId, -1);
    }
}