<?php
namespace BoardBerry\Games\Alias\Game\Room\Teams;

class Team
{
    public $players = [];
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function addPlayer($playerId)
    {
        $players[$playerId] = 1;
    }

    public function removePlayer($playerId)
    {
        unset($this->players[$playerId]);
    }
}