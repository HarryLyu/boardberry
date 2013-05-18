<?php

    require_once "../../../lib/Realplexor.php";
    $rpl = new Dklab_Realplexor(
        "127.0.0.1",
        "10010",
        "bb"
    );

    for($i=0;$i<100;$i++) {
        $rpl->send("BroadCast",$i);
        //break;
    }
?>