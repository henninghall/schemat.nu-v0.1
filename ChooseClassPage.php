<?php
include("connect.php");
include("CookieHandeler.php");
include("Layout.php");

new ChooseClassPage();

class ChooseClassPage{

private $ch;	// Cookie handeler

	// If a class has been choosen by previous POST request. 
	function __construct() {
		$this->ch = new CookieHandeler;

		if (isset($_POST["classList"])){
			$this->ch->setcookie($_POST["classList"]);
			header("Location:".$this->getHomePagePath());
		}
		else if  (isset($_POST["studentList"])){
			$this->ch->setcookie($_POST["studentList"]);
			header("Location:".$this->getHomePagePath());
		}
		else {
		$this->printChooseClassPage();
		}
	}

	// Returns the path to the home page. Works on both local and web host.  
	function getHomePagePath(){
		$path = dirname($_SERVER["REQUEST_URI"]);
		return "$path";
	}

	function printDropDownOptions($type){
		$array = $this->getAllClasses($type);
		foreach ($array as $currentClass) {
			echo "<option>$currentClass</option>\n";
		}
	}

	function getAllClasses($type){
			$sql = "SELECT class FROM timeeditID WHERE type = '$type' ORDER BY class";
			$sql = mysql_query($sql) or die ("Hittar inga klasser...");
			$resultSet = array();
			while($result = mysql_fetch_array($sql)){
				$resultSet[] = $result['class'];
			}
			return $resultSet;
		}


	function printChooseClassPage(){
		$layout = new Layout();
		$layout->printBeforeContent(); 
		echo "
		<div id=\"chooseClassPage\" class='subpage'>
		<h1>Vilket schema ska visas?</h1>
		<p>Du behöver bara ange detta en gång.</p><br />
		<p>Välj din klass: </p>
			<form action=\"\" method=\"post\">
				<select name=\"classList\">";
					 $this->printDropDownOptions("class");
				echo "</select>
				<input type=\"submit\" value=\"Välj\">
		<br /><br />
		<p>...eller ditt personliga schema:</p>
		</form>
			<form action=\"\" method=\"post\">
				<select name=\"studentList\">";
					 $this->printDropDownOptions("student");
				echo "</select>
				<input type=\"submit\" value=\"Välj\">
			</form>
			<p style='font-size:0.8em;'><a href='StudentSchedule.php'>Skapa ett personligt schema...</a><p>
		</div>
		";
		$layout->printAfterContent(); 
	}

}

?>
