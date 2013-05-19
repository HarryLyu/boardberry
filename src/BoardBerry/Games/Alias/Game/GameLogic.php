<?php
namespace BoardBerry\Games\Alias\Game;

use BoardBerry\Common\User\UserManager;
use BoardBerry\Games\Alias\Game\Events\RoomEventManager;
use BoardBerry\Games\Alias\Game\Room\Room;
use BoardBerry\Games\Alias\Game\Room\Words\WordManager;

class GameLogic
{
    const STATE_TEAM_SELECT = 'teams';

    /** @var RoomEventManager */
    protected $eventManager;

    /** @var UserManager */
    protected $userManager;

    /** @var Room */
    protected $room;

    public function __construct($eventManager, $room, $userManager)
    {
        $this->room = $room;
        $this->eventManager = $eventManager;
        $this->userManager = $userManager;
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

    private function isGameCanBeStarted()
    {
        $c = 0;
        foreach ($this->room->teams as $team) {
            $playerCount = count($team->players);
            if ($playerCount < 2) {
                return false;
            }
            $c += $playerCount;
        }

        return $c >= 4 && $c == $this->room->playerCount;
    }

    public function addPlayerToTeam($teamId, $playerId)
    {
        $this->room->joinPlayerToTeam($teamId, $playerId);
        $this->eventManager->playerJoinedToTeam($teamId, $playerId, $this->isGameCanBeStarted());
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

        $name = $this->userManager->getName($this->room->explainerId);
        $this->eventManager->explanationStarted($this->room->explainerId, $name, $this->room->activeTeamId, $wordSet);
    }


    public function finishExplanation($tempResults)
    {
        $this->room->saveResults($tempResults);

        $words = $this->room->getWordsForTurn();
        $name = $this->userManager->getName($this->room->explainerId);
        $this->eventManager->explanationFinished(
            $this->room->explainerId,
            $name,
            $tempResults,
            $words,
            $this->room->activeTeamId
        );
    }

    public function addScore()
    {
        $wordsResult = $this->room->getResults();

        $score = 0;
        foreach ($wordsResult as $value) {
            if ($value == 0) {
                $score--;
            } else {
                $score++;
            }
        }

        $score = $this->room->addTeamScore($this->room->activeTeamId, $score);

        if ($score < 10) {
            $this->eventManager->turnFinished($this->room->getAllTeamScores());
        } else {
            $this->eventManager->gameFinished($this->room->getAllTeamScores());
        }
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
        $this->eventManager->gameStarted($this->room->teams, $this->userManager);

        $this->turnStart();
    }

    public function turnStart()
    {
        $name = $this->userManager->getName($this->room->explainerId);
        $this->eventManager->turnStarted($this->room->explainerId, $name, $this->room->activeTeamId);
    }

    public function nextTurn()
    {
        $results = $this->room->getResults();
        $this->room->deleteWordsFromPool(sizeof($results) + 1);

        $this->room->nextTurn();
        $name = $this->userManager->getName($this->room->explainerId);
        $this->eventManager->turnStarted($this->room->explainerId, $name, $this->room->activeTeamId);
    }
}