<!DOCTYPE html>
<html>
<head>
	<title>Crawler Demo</title>
	<!--<link rel="stylesheet" type="text/css" href="css/style.css" />-->
</head>

<body>

<?php

require_once('crawler.php');

$seeds = array(	'http://www.dn.se', 'http://www.aftonbladet.se',
				'http://www.sydsvenskan.se', 'http://www.svd.se',
				'http://www.pitchfork.com', 'http://www.expressen.se',
				'http://www.na.se');

$crawler = new Crawler();
$crawler->crawlSeeds($seeds);
$crawler->print_db();
$crawler->close();

?>

</body>

</html>
