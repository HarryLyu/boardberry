<?php
namespace BoardBerry\Games\Alias\Game;

use BoardBerry\Games\Alias\Game\Events\RoomEventManager;
use BoardBerry\Games\Alias\Game\Room\Room;
use BoardBerry\Games\Alias\Game\Room\Words\WordManager;

class GameLogic
{
    const STATE_TEAM_SELECT = 'teams';

    /** @var RoomEventManager */
    protected $eventManager;

    /** @var Room */
    protected $room;

    public function __construct($eventManager, $room)
    {
        $this->room = $room;
        $this->eventManager = $eventManager;
    }

    public function init($ownerId)
    {
        $this->room->joinPlayerToRoom($ownerId);
        $this->room->addTeam();
        $this->room->addTeam();
        $this->room->setState(self::STATE_TEAM_SELECT);
    }

    public function addPlayer($playerId)
    {
        $this->room->joinPlayerToRoom($playerId);
        $this->eventManager->playerJoinedToRoom($playerId, $this->room->playerCount);
    }

    public function addPlayerToTeam($teamId, $playerId)
    {
        $this->room->joinPlayerToTeam($teamId, $playerId);
        $this->eventManager->playerJoinedToTeam($teamId, $playerId);
    }

    public function addTeam($playerId)
    {
        $teamId = $this->room->addTeam();
        $this->eventManager->teamAdded($teamId);

        $this->addPlayerToTeam($teamId, $playerId);
    }


    public function startExplanation()
    {
        $wordSet = $this->room->getWordsForTurn();

        $this->eventManager->explanationStarted($this->room->explainerId, $this->room->activeTeamId, $wordSet);
    }


    public function finishExplanation($tempResults)
    {
//        $score = 0;
//        foreach($tempResults as $value) {
//            if($value==0) {
//                $score--;
//            } else {
//                $score++;
//            }
//        }

        $this->room->saveResults($tempResults);

        $words = $this->room->getWordsForTurn();
        $this->eventManager->explanationFinished($this->room->explainerId, $tempResults, $words, $this->room->activeTeamId);
        //$this->room->deleteWordsFromPool(sizeof($tempResults));
    }

    public function editResult($wordId)
    {
        $value = $this->room->editResult($wordId);
        $this->eventManager->resultUpdated($wordId, $value);
    }

    /**
     * @param WordManager $wordManager
     */
    public function startGame($wordManager)
    {
        $wordSet = $wordManager->generateWordSet();
        $this->room->saveWordPool($wordSet);
        $this->room->addTurnQueues();
        $this->eventManager->gameStarted($this->room->teams);

        $this->turnStart();
    }

    public function turnStart()
    {
        $this->eventManager->turnStarted($this->room->explainerId, $this->room->activeTeamId);
    }
}