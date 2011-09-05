<!DOCTYPE html>
<html>
<head>
	<title>Crawler Demo</title>
	<!--<link rel="stylesheet" type="text/css" href="css/style.css" />-->
</head>

<body>

<?php

require_once('crawler.php');

/*

For later tests with "huge" sites

$seeds = array(	'http://www.dn.se', 'http://www.aftonbladet.se',
				'http://www.sydsvenskan.se', 'http://www.svd.se',
				'http://www.pitchfork.com', 'http://www.expressen.se',
				'http://www.na.se');
*/

$seeds = array('http://www.lonewolfmedia.se');

$crawler = new Crawler();
$crawler->setDepth(1);
$crawler->crawlSeeds($seeds);
$crawler->print_db();
$crawler->close();

?>

</body>

</html>
