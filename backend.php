<?php

require_once('crawler.php');

if (!isset($_SESSION)) {

	session_start();
	$_SESSION['crawler'] = new Crawler();

}

if (isset($_POST['request']) && isset($_SESSION)) {

	$request = json_decode($_POST['request']);

	// Start crawling if not doing so already
	if ($request->type == 'crawl') {

		echo 'Started crawling ' . $request->value;
		$_SESSION['crawler']->setDepth(0);
		$_SESSION['crawler']->crawl($request->value);
		//$crawler->close();
		//echo $crawler->print_db();

	}

	else if ($request->type == 'status') {

		if ($_SESSION['crawler']) {

			echo $_SESSION['crawler']->getStatus();

		} else echo "Not crawling";

	}

	else echo "Unknown input";

}

?>
