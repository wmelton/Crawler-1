<!DOCTYPE html>
<html>
<head>
	<title>Crawler Demo</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<script type="text/javascript" src="js/functions.js"></script>
</head>

<body>

<?php

/*
require_once('crawler.php');

For later tests with "huge" sites

$seeds = array(	'http://www.dn.se', 'http://www.aftonbladet.se',
				'http://www.sydsvenskan.se', 'http://www.svd.se',
				'http://www.pitchfork.com', 'http://www.expressen.se',
				'http://www.na.se');


$seeds = array('http://www.lonewolfmedia.se');

*/

?>

<div id="message_bar">

	<h3 id="status">Message</h3>

</div>

<h3 id="tmp">status</h3>

<div id="bottom_bar">
	<div id="bottom_content">

		<input id="input_crawl" type="text" value="http://www.lonewolfmedia.se" />
		<button id="button_crawl" onclick="crawlStart()">Crawl</button>
		<button id="button_stop" onclick="crawlStop()">Stop</button>

	</div>
</div>

</body>

</html>
