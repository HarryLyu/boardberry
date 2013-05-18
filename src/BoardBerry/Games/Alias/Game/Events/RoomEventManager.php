<?php

namespace BoardBerry\Games\Alias\Game\Events;

class RoomEventManager
{

    const ROOM_CHANNEL_PREFIX = 'BCAST_ROOM_';

    /**
     * @var $comet \Dklab_Realplexor
     */
    private $comet;

    private $roomId;

    public function __construct($comet, $roomId)
    {
        $this->comet = $comet;
        $this->roomId = $roomId;
        $this->channelName = $this->getRoomChannelName();
    }

    public function getRoomChannelName()
    {
        return self::ROOM_CHANNEL_PREFIX . $this->roomId;
    }

    private function sendEvent($name, $data)
    {
        $this->comet->send($this->channelName, ['eventName' => $name, 'data' => $data]);
    }


    public function playerJoinedToRoom($playerId, $playerCount)
    {
        $this->sendEvent('playerJoinedToRoom', ['playerId' => $playerId, 'playerCount' => $playerCount]);
    }

    public function playerJoinedToTeam($teamId, $playerId)
    {
        $this->sendEvent('playerJoinedToTeam', ['teamId' => $teamId, 'playerId' => $playerId]);
    }

    public function teamAdded($teamId)
    {
        $this->sendEvent('teamAdded', ['teamId' => $teamId]);
    }

    public function explanationStarted($explainerId, $activeTeamId, $wordSet)
    {
        $this->sendEvent(
            'explanationStarted',
            ['explainerId' => $explainerId, 'activeTeamId' => $activeTeamId, 'words' => $wordSet]
        );
    }
}