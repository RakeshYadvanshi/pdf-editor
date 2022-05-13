<?php
class PdfSettings
{

    var $fileName;

    function __construct()
    {
        $this->fileName = "pdfsetting.json";
    }

    function WriteSettings($ob)
    {
        $myfile = fopen($this->fileName, "w") or die("Unable to open file!");
        fwrite($myfile, json_encode($ob));
        fclose($myfile);
    }

    function ReadSettings()
    {
        $myfile = fopen($this->fileName, "r") or die("Unable to open file!");
        $settings = json_decode(fread($myfile, filesize($this->fileName)));
        fclose($myfile);

        return $settings;
    }
}
