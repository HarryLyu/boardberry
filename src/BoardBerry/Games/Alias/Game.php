<?php
namespace BoardBerry\Games\Alias;

use BoardBerry\Games\Alias\Room\Room;

class Game {
    const STATE_TEAM_SELECT = 'teams';

    /** @var Room */
    protected $room;

    public function __construct($room, $responseFormatter)
    {
        $this->room = $room;
        $this->responseFormatter = $responseFormatter;
    }

    public function init($ownerId)
    {
        $this->room->addPlayer($ownerId);
        $this->room->addTeam();
        $this->room->addTeam();
        $this->room->setState(self::STATE_TEAM_SELECT);
    }

}