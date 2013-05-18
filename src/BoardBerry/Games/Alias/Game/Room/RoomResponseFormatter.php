<?php
namespace BoardBerry\Games\Alias\Game\Room;

use BoardBerry\Games\Alias\Game\Events\RoomEventManager;
use BoardBerry\Games\Alias\Game\GameLogic;

class RoomResponseFormatter
{
    /** @var  RoomEventManager */
    protected $roomEventManager;

    /** @var  Room */
    protected $room;

    public function __construct($roomEventManager, $room)
    {
        $this->roomEventManager = $roomEventManager;
        $this->room = $room;
    }

    public function format()
    {
        $response['result'] = 'ok';
        $response['state'] = $this->room->state;
        $response['data']['channel'] = $this->roomEventManager->getRoomChannelName();
        $response['data']['channel_time'] = time();
        $response['data']['id'] = $this->room->roomId;
        $response['data']['owner'] = $this->room->ownerId;
        switch ($this->room->state) {
            case GameLogic::STATE_TEAM_SELECT:
                $response['data']['teams'] = $this->formatTeams();

                return $response;
        }
    }

    public function formatTeams()
    {
        $data = [];
        foreach ($this->room->teams as $team) {
            $data[] = ['id' => $team->id, 'users' => array_keys($team->players)];
        }

        return $data;
    }
}