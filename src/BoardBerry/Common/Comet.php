<?php

namespace BoardBerry\Common;

class Comet
{

    const BROADCAST_CHANNEL = "BB_BCAST";
    /**
     * @var $comet \Dklab_Realplexor
     */
    private $comet;

    public function __construct($comet)
    {
        $this->comet = $comet;
    }

    public function send($channelName, $data) {
        $this->comet->send($channelName, [$data]);
    }

}