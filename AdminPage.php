<?php
include("connect.php");	

new AdminPage;
/**
* Use AdminPage to update the period dates of the term. 
*/
class AdminPage {

	public $ht = "";
	public $ht1 = "";
	public $ht2 = "";
	public $vt = "";
	public $vt1 = "";
	public $vt2 = "";
	public $visibleForm = "";
	public $visibleUntil = "";

	function __construct() {
		// If a period to change has been chosed
		if (!empty($_GET)) {
			$this->fillBoxesWithOldValues($_GET['class']);
			$this->SelectSelectedOption($_GET['class']);
		}

		if (isset($_POST["periodList"])) {
			$periodToBeChanged = $_POST["periodList"];
			$newStartDate = $_POST["VisibleFrom"];
			$newEndDate = $_POST["VisibleUntil"];
			$this->changePeriodDates($periodToBeChanged, $newStartDate, $newEndDate);
			header("Location:AdminPage.php");
		}
		else {
		$this->printAdminPage();
		}
	}

	// When changeing or updating period with new dates,
	// both period and links table must be updated.
	function changePeriodDates($periodToBeChanged, $newStartDate, $newEndDate){
		if ($periodToBeChanged == null|| $newStartDate == null || $newEndDate == null) break;
		if ($periodToBeChanged == ""|| $newStartDate == "" || $newEndDate == "") break;
		$this->updatePeriodTable($periodToBeChanged, $newStartDate, $newEndDate);
		$this->updateLinksTable($periodToBeChanged, $newStartDate, $newEndDate);
	}

	function updatePeriodTable($periodToBeChanged, $newStartDate, $newEndDate) {
		$sql = "UPDATE periods SET startDate='$newStartDate', endDate='$newEndDate' 
		WHERE Period='$periodToBeChanged'";
		mysql_query($sql) or die ("Cant uppdate period table with new periods.");
	}

	function updateLinksTable($periodToBeChanged, $newStartDate, $newEndDate) {
	$sql = "UPDATE links SET VisibleFrom='$newStartDate', VisibleUntil='$newEndDate' 
	WHERE Period='$periodToBeChanged'";
	mysql_query($sql) or die ("Cant uppdate links table with new periods.");
	}

	function fillBoxesWithOldValues($period){
		$sql = "SELECT * FROM periods WHERE period = '$period'";
		$sql = mysql_query($sql) or die ("Hittar inga finfina länkar...");
		$resultat = mysql_fetch_array($sql);
		$this->visibleForm = $resultat['startDate'];
		$this->visibleUntil = $resultat['endDate'];
	}

	function SelectSelectedOption($period){
		switch ($period) {
		    case "ht": $this->ht = "selected"; break;
		    case "ht1": $this->ht1 = "selected"; break;
		    case "ht2": $this->ht2 = "selected"; break;
		    case "vt": $this->vt = "selected"; break;
		    case "vt1": $this->vt1 = "selected"; break;
		    case "vt2": $this->vt2 = "selected"; break;
		}
	}

	public function printAdminPage(){
	echo "
	<!DOCTYPE HTML>
	<html>
	<head>
		<title>Schemat.nu - Se schemat snabbt & smidigt.</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
	</head>
	<body>
<script>
function showDates() {
 var options = document.form.periodList.options;
for(var i=0;i<options.length;i++){
  if(options[i].selected){
 window.location = 'AdminPage.php?class='+options[i].value;
  }
}
}
</script>


	<form action=\"\" method=\"post\" name=\"form\">
		Vilken period ska ändras? 
		<select name=\"periodList\" id=\"periodList\" onchange=\"showDates()\">;
		<option>Välj termin</option>
		<option name=\"ht\" ".$this->ht.">ht</option>
		<option name=\"ht1\" ".$this->ht1.">ht1</option>
		<option name=\"ht2\" ".$this->ht2.">ht2</option>
		<option name=\"vt\" ".$this->vt.">vt</option>
		<option name=\"vt1\" ".$this->vt1.">vt1</option>
		<option name=\"vt2\" ".$this->vt2.">vt2</option>
		</select><br />
		Perioden startar: 
		<input type=\"text\" name=\"VisibleFrom\" value=\"".$this->visibleForm."\"><br />
		Perioden slutar: 
		<input type=\"text\" name=\"VisibleUntil\" value=\"".$this->visibleUntil."\"><br />
		<input type=\"submit\" value=\"Update\">
	<form>
	</body>
	</html>";
	}
}

	


?>



