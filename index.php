<?php
include("CookieHandeler.php");
include("connect.php");
include("Area.php");
include("Schedule.php");
include("Link.php");
include("Stats.php");
$layout = new Layout();
?>


<?php 
$layout->printBeforeContent(); 
$ch = new CookieHandeler;
$ch->goToChooseCookiePageIfNoCookie();
?>

<div id="topAreas">

	<div id="classArea"><?php
	echo $ch->getCookie();
	?></div>

	<div id="toolArea">
	<?php
	$toolArea = new Area("tool", "Verktyg");
	$toolArea->printHeader();
	$toolArea->printLinks();
	?></div>

	<div id="courseArea">
	<?php
	$courseArea = new Area("course", "Aktuella kurser");
	$courseArea->printHeader();
	$courseArea->printLinks();
	?></div>

	<div id="linkArea"><?php
	$linkArea = new Area("link", "Länkar");
	$linkArea->printHeader();
	$linkArea->printLinks();
	?></div>

	<div id="rememberArea"><?php
	$rememberArea = new Area("remember", "Kom ihåg");
	$rememberArea->printHeader();
	$rememberArea->printLinks();
	?></div>

<p style="line-height:10%;font-size:0.1em"><br style="clear:both"/></p>

</div>


<div id="schedule"><?php
$sh = new Schedule();
$sh->printSchedule();
?></div>


<?php // STATS
$st = new Stats();
$st->captureStats();  
?>

<?php $layout->printAfterContent(); ?>

