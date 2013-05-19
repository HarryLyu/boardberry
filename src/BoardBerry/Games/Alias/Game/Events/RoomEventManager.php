<?php

namespace BoardBerry\Games\Alias\Game\Events;

use BoardBerry\Common\User\UserManager;

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

    public function playerJoinedToTeam($teamId, $playerId, $isGameCanBeStarted)
    {
        $this->sendEvent('playerJoinedToTeam', ['teamId' => $teamId, 'playerId' => $playerId, 'isGameCanBeStarted' => $isGameCanBeStarted]);
    }

    public function teamAdded($teamId)
    {
        $this->sendEvent('teamAdded', ['teamId' => $teamId]);
    }

    public function explanationStarted($explainerId, $explainerName, $activeTeamId, $wordSet)
    {
        $this->sendEvent(
            'explanationStarted',
            [
                'explainer' =>
                [
                    'id' => $explainerId,
                    'name' => $explainerName
                ],
                'activeTeamId' => $activeTeamId,
                'words' => $wordSet
            ]
        );
    }


    /**
     * @param $teams
     * @param UserManager $userManager
     */
    public function gameStarted($teams, $userManager)
    {
        $teamsRaw = [];
        foreach ($teams as $team) {
            $playersRaw = [];

            if (count($team->players) == 0) {
                continue;
            }

            foreach ($team->players as $playerId => $_) {
                $playersRaw[] = ['id' => $playerId, 'name' => $userManager->getName($playerId)];
            }
            
            $teamsRaw[] = ['id' => $team->id, 'players' => $playersRaw];
        }

        $this->sendEvent('gameStarted', ['teams' => $teamsRaw]);
    }

    public function turnStarted($explainerId, $explainerName, $activeTeamId)
    {
        $this->sendEvent(
            'turnStarted',
            [
                'explainer' =>
                [
                    'id' => $explainerId,
                    'name' => $explainerName
                ],
                'activeTeamId' => $activeTeamId
            ]
        );
    }

    public function explanationFinished($explainerId, $explainerName, $tempResults, $words, $activeTeamId)
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
                    'name' => $explainerName
                ]
            ]
        );
    }

    public function resultUpdated($wordId, $value)
    {
        $this->sendEvent('resultUpdated', ['id' => $wordId, 'result' => $value]);
    }

    public function turnFinished($teamScores)
    {
        $scores = [];
        foreach ($teamScores as $teamId => $score) {
            $scores[] = [
                'id' => $teamId,
                'position' => $score
            ];
        }
        $this->sendEvent('turnFinished', $scores);
    }

    public function gameFinished($teamScores)
    {
        $scores = [];
        foreach ($teamScores as $teamId => $score) {
            $scores[] = [
                'id' => $teamId,
                'position' => $score
            ];
        }
        $this->sendEvent('gameFinished', $scores);
    }
}