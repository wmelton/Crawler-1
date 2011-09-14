<?php

require_once('crawler.php');

session_start();

if (isset($_SESSION)) {

	//$_SESSION['crawler']
	echo "YES";

} else echo "NO";

echo "hej";

// PROBLEM: Kommer inte åt sessions-variablerna medan crawl kör,
// däremot kommer vi åt filen och kan skicka responss

?>
