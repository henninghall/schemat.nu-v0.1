<?php 

class Area{	

	public $type;
	public $title;
	public $currentLinks;
	public $ch;

	public function __construct($type, $title){
	    	$this->ch = new CookieHandeler();
	    	$this->title = $title;
	    	$this->type = $type;
	    	$this->currentLinks = $this->getCurrentLinks();
	 }	

	public function getCurrentLinks(){
		
		if ($this->ch->isClass()) $allLinks = $this->getAllClassLinks(); 
		else $allLinks = $this->getAllStudentLinks();

		foreach ($allLinks as $link) {
		   	$from = $link->visibleFrom;
		   	$until = $link->visibleUntil;
		   	
		   	if($link->isLinkCurrent()){
		   		$currentLinks[] = $link; 
		   	}
		}
		if(!empty($currentLinks)) return $currentLinks;
	}

	public function getAllStudentLinks(){
		$liuID = $this->ch->getCookie();
		$sql = "SELECT linkID FROM studentLinks WHERE liuID = '$liuID'";
		$sql = mysql_query($sql);
		while ($resultat = mysql_fetch_array($sql))
		{
			$linkIDs[] = $resultat['linkID'];			
		}
		foreach ($linkIDs as $id) {
			$sql = "SELECT * FROM links WHERE id = $id AND Type = '$this->type' AND Published = '1'";
			$sql = mysql_query($sql);
			$links = array();
			while ($resultat = mysql_fetch_array($sql))
			{
				$linkText = $resultat['LinkText'];
				$url = $resultat['URL'];
				$description = $resultat['Description'];
				$visibleFrom = $resultat['VisibleFrom'];
				$visibleUntil = $resultat['VisibleUntil'];
				$target = $resultat['Target'];
				$type = $resultat['Type'];

			$links[] = new Link($linkText, $url, $description, $type, $visibleFrom, $visibleUntil, $target);
			}
		}
		return $links;
	}

	// Return all links associated with choosen class and area type
	public function getAllClassLinks(){
		$class = $this->ch->getCookie();

		$sql = "SELECT * FROM links WHERE $class = '1' AND Type = '$this->type' AND Published = '1'";
		$sql = mysql_query($sql) or die ("Länkinsamlingen gick snett.");
		$links = array();
		while ($resultat = mysql_fetch_array($sql))
		{
			$linkText = $resultat['LinkText'];
			$url = $resultat['URL'];
			$description = $resultat['Description'];
			$visibleFrom = $resultat['VisibleFrom'];
			$visibleUntil = $resultat['VisibleUntil'];
			$target = $resultat['Target'];
			$type = $resultat['Type'];

			$links[] = new Link($linkText, $url, $description, $type, $visibleFrom, $visibleUntil, $target);
		}
	return $links;
	}

	public function printLinks(){
				if(!empty($this->currentLinks)) {
			foreach ($this->currentLinks as $link) {
				$link->printLink();
			}
		}
	}
	
	public function printHeader(){
		if(!empty($this->currentLinks)) {
		echo "<strong>".$this->title."</strong><br />";		
		}
	}
}

?>