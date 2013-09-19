<?php
include("../Schedule.php");
include("../CookieHandeler.php");
include("../connect.php");


$sh = new Schedule();
$sh->updateAllSchedules();
?>