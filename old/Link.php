<?php

class Link{

	public $label;
	public $url;
	public $target;
	public $description;
	public $type;
	public $visibleFrom;
	public $visibleUntil; 

	public function __construct($label, $url, $desc, $type ,$visibleFrom, $visibleUntil, $target) 
	    {
	    	$this->label = $label;
	    	$this->url = $url;
	    	$this->target = $target;
	    	$this->description = $desc;
	    	$this->type = $type;
	    	$this->visibleFrom = $visibleFrom;
	    	$this->visibleUntil = $visibleUntil;
	    }	

		public function printLink(){
				print "<a href=\"$this->url\"";
				if ($this->type == "remember") print " class=\"remember\"";
				if (!empty($this->target)) print " target=\"$this->target\"";
				print ">$this->label</a>"; 
				if (!empty($this->description)) print " - $this->description";
				print "<br />";
		}

		public function isLinkCurrent(){
			$currentYear = Date("y");

			// Is the period ending after new year?
			if (strtotime("$currentYear-$this->visibleFrom") > strtotime("$currentYear-$this->visibleUntil")) {

				// Before new year
				if (time() > strtotime("$currentYear-07-01")){
					$endYear = $currentYear + 1; 
					$startYear = $currentYear;
				}

				// After new year
				else { 
					$endYear = $currentYear;
					$startYear = $currentYear - 1 ; 
				}

			}
			else {
				$endYear = $currentYear;
				$startYear = $currentYear; 
			}

			// If link visibility period is now
			if((strtotime("$startYear-$this->visibleFrom") < time()) &&  (strtotime("$endYear-$this->visibleUntil") > time())){
				return true; 
			}
			else return false;
		}

}

?>