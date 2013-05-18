<?php
namespace BoardBerry\Games\Alias;

use BoardBerry\Games\Alias\Room\Room;
use BoardBerry\Games\Alias\Room\RoomResponseFormatter;

class Game {
    const STATE_TEAM_SELECT = 'teams';

    /** @var Room */
    protected $room;

    /** @var RoomResponseFormatter */
    protected $responseFormatter;

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

    /**
     * @return RoomResponseFormatter
     */
    public function getResponseFormatter()
    {
        $this->responseFormatter->setRoom($this->room);
        return $this->responseFormatter;
    }

}