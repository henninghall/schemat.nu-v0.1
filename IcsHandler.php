<?php

include("Event.php");
require '/ics-parser/iCalReader.php';



class IcsHandler {

    private $file = "https://se.timeedit.net/web/liu/db1/schema/ri65q81Q056Z0ZQ5966557c0ym095W70b8Y68Q0Q7977QZr2.ics";
    private $events = array();

    function __construct() {
        $this->saveIcsFileFromUrl($this->file);
    }

    public function saveIcsFileFromUrl($url){
        $content = file_get_contents($this->file);
        file_put_contents('schedules/test.ics', $content);

        print $content;
    }

}