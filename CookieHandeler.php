<?php
class CookieHandeler{

	private $cookieName = "classCookie";					// Name of the cookie.
	private $ChooseClassPage = "ChooseClassPage.php";		// Page where user gets the cookie.

	public function goToChooseCookiePageIfNoCookie(){
		if (!$this->isCookie()) $this->goToChooseClassPage();
	}

	public function setCookie($schedule){
		setcookie($this->cookieName,$schedule,time() + (86400 * 365)); // 86400 = 1 day
	}
	
	public function getCookie(){
		if(isset($_COOKIE["$this->cookieName"])) {
			return $_COOKIE["$this->cookieName"];
		}
		return "null";
	}

	public function removeCookie(){
		setcookie($this->cookieName, "", 1);
	}
	
	public function isCookie(){
		if(isset($_COOKIE[$this->cookieName])) return true;
		else return false;
	}	

	public function getChooseClassPage(){
		return $this->ChooseClassPage;
	}

	public function goToChooseClassPage(){
		header("Location:".$this->getChooseClassPage());
	}	

	public function isClass(){
		$class = $this->getCookie();
		$sql = "SELECT type FROM timeeditID WHERE class = '$class'";
		$sql = mysql_query($sql) or print "Ingenting?";
		$result = mysql_fetch_array($sql);
		$type = $result['type'];
		if ($type == "class") return true;
		else return false;
	}
}

?>