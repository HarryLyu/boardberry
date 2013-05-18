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


    public function playerAdded($playerId, $playerCount)
    {
        $this->sendEvent('playerAdded', ['playerId' => $playerId, 'playerAdded' => $playerCount]);
    }
}