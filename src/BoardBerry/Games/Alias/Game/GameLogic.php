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
        $this->room->addPlayer($ownerId);
        $this->room->addTeam();
        $this->room->addTeam();
        $this->room->setState(self::STATE_TEAM_SELECT);
    }

    public function addPlayer($playerId)
    {
        $this->room->addPlayer($playerId);
        $this->eventManager->playerAdded($playerId);
    }
}