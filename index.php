<!DOCTYPE html>
<html>
<head>
	<title>Crawler Demo</title>
	<!--<link rel="stylesheet" type="text/css" href="css/style.css" />-->
</head>

<body>

<?php

require_once('crawler.php');

$crawler = new Crawler();
$crawler->crawl('http://www.sydsvenskan.se');

//$crawler->getColumn('email');

?>

</body>

</html>
