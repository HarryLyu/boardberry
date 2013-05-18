<?php
namespace BoardBerry\Games\Alias\Game;

use BoardBerry\Games\Alias\Game\Events\RoomEventManager;
use BoardBerry\Games\Alias\Game\Room\Room;
use BoardBerry\Games\Alias\Game\Room\RoomResponseFormatter;

class GameLogic {
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

    public function addPlayerToTeam($teamId, $playerId) {
        $this->room->joinPlayerToTeam($teamId, $playerId);
        $this->eventManager->playerJoinedToTeam($teamId, $playerId);
    }

    public function addTeam($playerId)
    {
        $teamId = $this->room->addTeam();
        $this->eventManager->teamAdded($teamId);

        $this->addPlayerToTeam($teamId, $playerId);
    }

}