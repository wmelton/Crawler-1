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
$crawler->crawl('http://www.dn.se');
$crawler->crawl('http://www.aftonbladet.se');
$crawler->crawl('http://www.sydsvenskan.se');
$crawler->print_db();
$crawler->close();

?>

</body>

</html>
