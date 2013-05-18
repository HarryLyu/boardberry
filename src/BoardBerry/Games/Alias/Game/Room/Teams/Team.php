<?php
namespace BoardBerry\Games\Alias\Game\Room\Teams;


class Team {
    public $users = [];
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}