<?php
include("connect.php");
include("CookieHandeler.php");
include("Layout.php");
include("Schedule.php");


new StudentSchedule();

class StudentSchedule{

	public $errorMessege = "hidden"; 
	public $errorMessegeText =  "Hörru fyll i allt!";
	public $allStandardLinksID = array('80');

	// If a class has been choosen by previous POST request. 
	function __construct() {
		if (isset($_POST["liu-id"])){
			$liuId = mysql_real_escape_string($_POST["liu-id"]);
			$scheduleLink =  mysql_real_escape_string($_POST["schedule-link"]);
			if (empty($liuId) || empty($scheduleLink)) {
					$this->errorMessege = "visible";
			}
			else {
				if (strlen($liuId) != 8) {
					$this->errorMessege = "visible";
					$this->errorMessegeText = "Kom igen, det där ser inte ut som ett LiU-ID!";
				}
				// SUCCESS:
				else {	
					if ($this->isLiuID($liuId)) {
						$this->errorMessege = "visible";
						$this->errorMessegeText = "LiU-ID:et finns redan kopplat till ett schema. Kontakta henha972@student.liu.se om du vill ändra din timeedit-länk.";
					}
					else {
						$errorMessege = "hidden"; 
						$this->createSchedule($liuId, $scheduleLink);
						$ch = new CookieHandeler();
						$ch->setCookie($liuId);
						header("Location:".$this->getStartSiteAdress());
					}
				}
			}
			$this->printPage();
		}
		else {
			$this->errorMessege = "hidden"; 
			$this->printPage();
		}
	}

	public function getStartSiteAdress(){
		return dirname($_SERVER["REQUEST_URI"]);
	}

	public function createSchedule($liuId, $timeeditLink){
		$this->insertToTimeeditTable($liuId, $timeeditLink);
		$this->addStandardLinks($liuId);
		$ch = new Schedule();
		$ch->updateSchedule($liuId);
	}

	public function isLiuID($liuID){
		$sql = "SELECT * FROM studentLinks WHERE liuID = '$liuID'";
		$result = mysql_query($sql);
		if(mysql_fetch_array($result) !== false) return true;
		else return false;		
	}


	public function insertToTimeeditTable($liuId, $timeeditLink){
		$sql = "INSERT INTO timeeditID (ID, class, scheduleURL, type) 
		VALUES (id, '$liuId', '$timeeditLink', 'student')";
		mysql_query($sql) or die("Gick inte att lägga till schemat, försök senare.");
	}

	public function addStandardLinks($liuId){
		$allStandardLinksID = $this->allStandardLinksID;
		foreach ($allStandardLinksID as $linkID) {
			$sql = "INSERT INTO studentLinks (id, linkID, liuID) 
			VALUES (id, '$linkID', '$liuId')"; 	
			mysql_query($sql) or die("Gick inte att lägga till schemat, försök senare.");
		}
	}

	public function printPage(){
		$layout = new Layout();
		$layout->printBeforeContent(); 
		echo "
		<div id=\"studentSchedulePage\" class='subpage'>
		<h1>Skapa personligt schema</h1>
		<p>Så här skapar du enkelt ditt personliga schema.

		<ol>
		<li>Surfa in på <a target=\"_blank\" href=\"https://se.timeedit.net/web/liu/db1/schema/ri1Q7.html\">timmeedit</a> (för sista gången).</li>
		<li>Sök fram alla dina kurser och lägg dem i listan <em>Mina val</em>.</li>
		<li>Klicka på <em>Ändra tid</em> och välj längsta möjliga datumgränser. Kicka på <em>OK</em>.</li>
		<li>Klicka på <em>Visa schema</em>.</li>
		<li>Klicka på <em>Grafiskt schema</em> längst ner till höger.</li>
		<li>Kopiera adressen i <a href=\"img/adressfaltet.png\" target=\"_blank\">adressfältet</a> och klistra in adressen på denna sida i rutan kallad <em>Timeedit-länk</em>.</li>
		<li>Fyll i ditt LiU-ID och klicka på <em>Skapa schema</em>. Grattis!</li>
		</ol>
		</p>
				<form action=\"\" method=\"post\">
				<label>Timeedit-länk: <br /><input type=\"text\" name=\"schedule-link\" /></label><br />
				<label >LiU-ID: <br /><input type=\"text\" name=\"liu-id\" /></label><br />
				<input type=\"submit\" class='submit' value=\"Skapa schema\">
				<div style=\"color:red; visibility:".$this->errorMessege."\">".$this->errorMessegeText."</div>
			<p><a href=\"".$this->getStartSiteAdress()."\">Tillbaka...</a></p>
			<form>
		</div>
		";
		$layout->printAfterContent(); 
	}

}

?>
