<?php

class Schedule{	

	public $schedule;
	public $path;

	public function printSchedule() {
		$ch = new CookieHandeler(); 
		$this->schedule = $ch->getCookie();
		echo file_get_contents("schedules/$this->schedule.txt");
	}

	public function updateSchedule($class) {
		$this->updatePath();
		$this->schedule = $class;
		// curl class url
		$rawSchedule = $this->getRawScheduleFromUrl();

		// remove unneccesary stuff
		$cleanedSchedule = $this->cleanRawSchedule($rawSchedule);
				
		// Adds course links to schedule		
		$cleanedSchedule = $this->addCourseLinks($cleanedSchedule);

		// save backup 
		$this->makeBackup($cleanedSchedule);

		// overwrite old current schedule
		$this->pulishSchedule($cleanedSchedule);
	}

	public function updateAllSchedules() {
		$sql = "SELECT class FROM timeeditID";
		$sql = mysql_query($sql) or die ("Hittar inga klasser...");
		while ($resultat = mysql_fetch_array($sql))
		{
			print $resultat['class']." uppdaterad.<br />";
			$this->updateSchedule($resultat['class']);
		}
	}

	public function getRawScheduleUrl(){
		$sql = "SELECT scheduleURL FROM timeeditID WHERE class='$this->schedule'";
		$sql = mysql_query($sql) or die ("Hittar inga klasser...");
		$result = mysql_fetch_array($sql);
		return $result['scheduleURL'];
	}

	public function getRawScheduleFromUrl(){
		$sql = "SELECT scheduleURL FROM timeeditID WHERE class='$this->schedule'";
		$sql = mysql_query($sql) or die ("Hittar inga klasser...");
		$result = mysql_fetch_array($sql);
		$scheduleID = $result['scheduleURL']; 
		$rawContent = file_get_contents($scheduleID);
		return $rawContent;
	}

	public function cleanRawSchedule($schedule){						// current datatype of $schedule:
		$schedule = $this->removeTopAndBottom($schedule);				// string
		$schedule = $this->seperateWeeks($schedule);					// string -> array
		$schedule = $this->removeEmptyAndPastWeeks($schedule);			// array (of weeks)
		$schedule = $this->removeSundays($schedule);					// array (of weeks)
		$schedule = $this->addPastDaysMarkup($schedule);			// array (of weeks)
		$schedule = $this->mergeArrayToString($schedule); 				// array -> string
		$schedule = $this->addMissingHtmlAfterSeperation($schedule); 	// string
		return $schedule;
	}

	public function removeTopAndBottom($rawSchedule){
		$noTopBottomSchedule[] = "";
		preg_match("'<div id=\"contents\" data-hourwidth=\"24\">(.*?)<table cellspacing=\"0\" class=\"clearBoth customTitleObjects\">'si", $rawSchedule, $noTopBottomSchedule);
		return $noTopBottomSchedule[0];
	}

	public function removeEmptyAndPastWeeks($allWeeks){
		$nonEmptyWeeks ="";
		$isCurrentWeekFound = false; 
		$currentWeek = Date("W");

		// Shows schedule for next week if today is sunday
		// BUG: currentweek++ doesnt work around year break -> fix this bug later
		if (Date("N") == 7) $currentWeek++;

		foreach ($allWeeks as $week) {
		   if ($isCurrentWeekFound){
		   		// Removes empty weeks (all weeks with content includes class="c ) 
		   		if (strpos($week,"class=\"c ") !== false) {
		   			$activeWeeks[] = $week;
		  	 	}
		   }
		   // When current week is found all weeks afterwards are being returned
		   else if (strpos($week,"v $currentWeek") !== false){
		   		$isCurrentWeekFound = true;
		   		$activeWeeks[] = $week;
		   }

		}
		return $activeWeeks;
	}

	public function removeSundays($allWeeks){
		foreach ($allWeeks as $week) {
		  	
			// Sunday starts at position == Last weekday start at position.		strrpos = pos last occurance
		  	$sundayStartPos = strrpos($week, "<div class=\"weekDay\"");
 
		  	// removes sundays and re-adds the break which is also removed. 
		  	$sundayFreeWeek = substr($week, 0, $sundayStartPos)."<br class=\"clearBoth\">";
		  	
		  	// Putting back the week to the array.
		  	$sundayFreeWeeks[] = $sundayFreeWeek;
		}
		return $sundayFreeWeeks;
	}


	public function seperateWeeks($rawSchedule){
		preg_match_all("'<div class=\"weekDay\" style=\"z-index: (.*?)\">(.*?)<br class=\"clearBoth\">'si", $rawSchedule, $weekArray);
		return $weekArray[0];
	}

	public function addMissingHtmlAfterSeperation($schedule){
		$schedule = "<div id=\"contents\" data-hourwidth=\"24\">
		<div class=\"weekContainer\">".$schedule."</div></div>";
		return $schedule; 
	}

	// Adds a markup for past PAIRS of days. Mon-Tue, Wed-Thu, Fri-Sat.
	// This makes it posible to hide these days and always show current day 
	// at the top in mobile browsers
	public function addPastDaysMarkup($schedule){
		$weekDay = Date("N");
		if ($weekDay > 4) $markUpAfterDivNumber = 5;
		else if ($weekDay > 2) $markUpAfterDivNumber = 3;
		else return $schedule;
		$currentWeek = $schedule[0]; 
			$markup = "<div id='pastDays'>";
			$pos = $this->strposOffset("<div class=\"weekDay\"",$currentWeek,$markUpAfterDivNumber);
			$schedule[0] = $markup . substr($currentWeek, 0, $pos) . "</div>" . substr($currentWeek, $pos);
		return $schedule;
	}

	public function addCourseLinks($schedule){
		
		//  Finds all unique course codes on schedule (all words with 6 letters)
		$pattern = "'<div class=\"c \">([\w\d]{6})\b</div>'";
		preg_match_all($pattern, $schedule, $matches);
		$uniqueMatches = array_unique($matches[1]);

		// Replaces all course codes with a link to the course webpage. 
		foreach ($uniqueMatches as $match) {
		$course = $this->getCoursePageURL($match);	
			if ($course != ""){
				$replacement = "<div class=\"c \"><a href=\"$course\" target=\"_blank\">$match</a></div>";
				$pattern = "'<div class=\"c \">$match</div>'";
				$schedule = preg_replace($pattern, $replacement, $schedule);
			}
		}
		return $schedule;
	}

	public function getCoursePageURL($courseCode){
		$sql = "SELECT URL FROM links WHERE type = 'course' AND LinkText = '$courseCode'";
		$sql = mysql_query($sql) or die ("Hittar inga klasser...");
		$resultat = mysql_fetch_array($sql);
		$url = $resultat['URL'];
		return $url; 
	}

	public function mergeArrayToString($array){
		$string = ""; 
		foreach ($array as $cell) {
			$string = $string.$cell;
		   }
		return $string; 
	}

	public function updatePath(){
		if (is_dir("schedules")) $this->path = "schedules";
		else if (is_dir("../schedules")) $this->path = "../schedules";
		// Probably during updateScheules.php for the first time -> Create folder.
		else {
			mkdir("../schedules");
			$this->path = "../schedules";
		}
	 }	


	public function makeBackup($schedule){
		$backupFolder = $this->createBackupFolder();
		$this->writeTxtFile($schedule,"$this->path/backup/$backupFolder/$this->schedule.txt");
	}

	// Creates a backup folder and returns the name of it.
	public function createBackupFolder() {
		$date = Date('ymd');
		$i = 1; 
		//Creates backup-folder if it doesnt exist
		if (!file_exists("$this->path/backup")) mkdir("$this->path/backup");

		// First backup folder of the day
		if (!file_exists("$this->path/backup/$date/$this->schedule".".txt")){
			mkdir("$this->path/backup/$date");
			return "$date";
		}
		// Creates backup folder $i of the day
		else {
			while (file_exists("$this->path/backup/$date-$i/$this->schedule".".txt")) {
				$i++; 
			}
			if(!file_exists("$this->path/backup/$date-$i")) mkdir("$this->path/backup/$date-$i");
			return "$date-$i";
		}
	}	

	public function pulishSchedule($schedule){
		$this->writeTxtFile($schedule, "$this->path/$this->schedule.txt");
	}


	public function writeTxtFile($content, $filePath) {
		$fh = fopen($filePath, 'w') or die("can't open file");
		fwrite($fh, $content);
		fclose($fh);	
	}

	// Finds the position of the nth occurance of a string
	public function strposOffset($search, $string, $offset)
	{
	    /*** explode the string ***/
	    $arr = explode($search, $string);
	    /*** check the search is not out of bounds ***/
	    switch( $offset )
	    {
	        case $offset == 0:
	        return false;
	        break;
	    
	        case $offset > max(array_keys($arr)):
	        return false;
	        break;

	        default:
	        return strlen(implode($search, array_slice($arr, 0, $offset)));
	    }
	}

}

?>