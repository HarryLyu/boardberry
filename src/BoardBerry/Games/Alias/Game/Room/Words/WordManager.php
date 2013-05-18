<?php
namespace BoardBerry\Games\Alias\Game\Room\Words;

class WordManager
{

    public function loadWords()
    {
        return range(0, 10);
    }

    public function generateWordSet()
    {

        $words = $this->loadWords();
        shuffle($words);
        return $words;

    }


}