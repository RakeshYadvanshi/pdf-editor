<?php

class Phplogger
{

    var $fileName;

    function __construct()
    {
        $this->fileName = __dir__."/logs/log_".date('dmY').".txt";
    }

    function WriteLog($error_message)
    {
        echo $error_message;
        ///error_log("[".date('d m Y, h:i:s A')."] ".$error_message."\n\r", 3, $this->fileName);
    }

}
