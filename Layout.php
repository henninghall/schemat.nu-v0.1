<?php

class Layout{

	public function printBeforeContent(){
		echo"
		<!DOCTYPE HTML><html><head>
		<META http-equiv=\"Content-type\" content=\"text/html; charset=utf-8\">
		<title>Schemat.nu - Visar schemat snabbt & smidigt.</title>
		<link rel=\"stylesheet\" href=\"css/timeedit.css\" type=\"text/css\">
		<link rel=\"stylesheet\" href=\"css/design.css\" type=\"text/css\">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
		<body>
		";
	}

	public function printAfterContent(){
		echo"
		</body>
		</html>";
	}
}


?>