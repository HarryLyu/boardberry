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
            [
                'explainer' =>
                [
                    'id' => $explainerId,
                    'name' => 'hui2'
                ],
                'activeTeamId' => $activeTeamId,
                'words' => $wordSet]
        );
    }

    public function gameStarted($teams)
    {
        $teamsRaw = [];
        foreach ($teams as $team) {
            $playersRaw = [];
            foreach ($team->players as $playerId) {
                $playersRaw[] = ['id' => $playerId, 'name' => 'aga' . $playerId];
            }
            $teamsRaw[] = ['id' => $team->id, 'players' => $playersRaw];
        }

        $this->sendEvent('gameStarted', ['teams' => $teamsRaw]);
    }

    public function turnStarted($explainerId, $activeTeamId)
    {
        $this->sendEvent('turnStarted', ['explainer' => ['id' => $explainerId, 'name' => 'hui'], 'activeTeamId' => $activeTeamId]);
    }

    public function explanationFinished($explainerId, $tempResults, $words, $activeTeamId)
    {
        $wordsRaw = [];
        foreach ($tempResults as $wordId => $wordResult) {
            $wordsRaw[] = ['id' => $wordId, 'text' => $words[$wordId], 'result' => $wordResult];
        }

        $this->sendEvent(
            'explanationFinished',
            [
                'words' => $wordsRaw,
                'activeTeamId' => $activeTeamId,
                'explainer' =>
                [
                    'id' => $explainerId,
                    'name' => 'hui'
                ]
            ]
        );
    }

    public function resultUpdated($wordId, $value)
    {
        $this->sendEvent('resultUpdated', ['id' => $wordId, 'result' => $value]);
    }
}