<?php
include("connect.php");
include("CookieHandeler.php");
include("Layout.php");
include("Schedule.php");


new StudentSchedule();

class StudentSchedule{

	public $errorMessege = "hidden"; 
	public $errorMessegeText =  "Hörru fyll i rutan va!";
	public $allStandardLinksID = array('80');

	// If a class has been choosen by previous POST request. 
	function __construct() {
		if (isset($_POST["schedule-link"])){

			$scheduleLink =  mysql_real_escape_string($_POST["schedule-link"]);
			
			$ch = new CookieHandeler();
			$liuId = $ch->getCookie();

			if (empty($scheduleLink)) {
				$this->errorMessege = "visible";
			}
			else {
				// SUCCESS:
				$errorMessege = "hidden"; 
				$this->updateTimeeditLink($liuId, $scheduleLink);
				header("Location:".$this->getStartSiteAdress());
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

	public function updateTimeeditLink($liuId, $timeeditLink){
		$sql = "UPDATE timeeditID SET scheduleURL='$timeeditLink' WHERE class='$liuId'";
		mysql_query($sql) or die("Gick inte att uppdatera schemat, försök senare.");
		$ch = new Schedule();
		$ch->updateSchedule($liuId);
	}

	public function printPage(){
		$layout = new Layout();
		$layout->printBeforeContent(); 
		$ch = new CookieHandeler();
		echo "
		<div id=\"studentSchedulePage\" class='subpage'>
			<h1>Uppdatera schema: ".$ch->getCookie()." </h1>
			<p>Du behöver updatera länken till ditt timeedit-schema när det gamla löper ut. 

				<ol>
					<li>Surfa in på <a target=\"_blank\" href=\"https://se.timeedit.net/web/liu/db1/schema/ri1Q7.html\">timmeedit</a>.</li>
					<li>Sök fram alla dina kurser och lägg dem i listan <em>Mina val</em>.</li>
					<li>Klicka på <em>Ändra tid</em> och välj längsta möjliga datumgränser. Kicka på <em>OK</em>.</li>
					<li>Klicka på <em>Visa schema</em>.</li>
					<li>Klicka på <em>Grafiskt schema</em> längst ner till höger.</li>
					<li>Kopiera adressen i <a href=\"img/adressfaltet.png\" target=\"_blank\">adressfältet</a> och klistra in adressen på denna sida i rutan kallad <em>Timeedit-länk</em>.</li>
					<li>Fyll i ditt LiU-ID och klicka på <em>Skapa schema</em>.</li>
				</ol>
			</p>
			<form action=\"\" method=\"post\">
				<label>Timeedit-länk: <br /><input type=\"text\" name=\"schedule-link\" /></label><br />
				<input type=\"submit\" class='submit' value=\"Uppdatera schema\">
				<div style=\"color:red; visibility:".$this->errorMessege."\">".$this->errorMessegeText."</div>
				<p><a href=\"".$this->getStartSiteAdress()."\">Tillbaka...</a></p>
				<form>
				</div>
				";
				$layout->printAfterContent(); 
			}

		}

		?>
