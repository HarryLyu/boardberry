<?php
namespace BoardBerry\Games\Alias\Room;

use BoardBerry\Games\Alias\Game;

class RoomResponseFormatter
{
    /** @var  Room */
    protected $room;

    public function setRoom($room)
    {
        $this->room = $room;
    }

    public function getChannel()
    {
        return Room::CHANNEL_NAME . $this->room->roomId;
    }

    public function format()
    {
        $response['result'] = 'ok';
        $response['state'] = $this->room->state;
        $response['channel'] = $this->getChannel();
        $response['data']['id'] = $this->room->roomId;
        $response['data']['owner'] = $this->room->ownerId;
        switch ($this->room->state) {
            case Game::STATE_TEAM_SELECT:
                $response['data']['teams'] = $this->formatTeams();
                return $response;
        }
    }

    public function formatTeams()
    {
        $data = [];
        foreach ($this->room->teams as $team) {
            $data[] = ['id' => $team->id, 'users' => $team->users];
        }

        return $data;
    }
}