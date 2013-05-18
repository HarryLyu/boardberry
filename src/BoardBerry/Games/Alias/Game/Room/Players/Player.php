<?php
namespace BoardBerry\Games\Alias\Game\Room\Players;


class Player {
    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }
}