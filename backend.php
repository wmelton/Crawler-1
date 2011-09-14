<?php

require_once('crawler.php');

if (!isset($_SESSION)) {

	session_start();
	$_SESSION['crawler'] = new Crawler();
	error_log('first');

}

if (isset($_POST['request']) && isset($_SESSION)) {

	$request = json_decode($_POST['request']);

	//error_log($request->type);

	// Start crawling if not doing so already
	if ($request->type == 'crawl') {

		echo 'Started crawling ' . $request->value . '<br />';
		$_SESSION['crawler']->setDepth(1);
		$_SESSION['crawler']->crawl($request->value);

		//$crawler->close();
		//echo $crawler->print_db();

	}

	else if (($request->type == 'status')) {

		if ($request->value == 'check') {

			echo 'request check';
			//echo $_SESSION['crawler']->getStatus();

		} else if ($request->value == 'stop') {

			//$_SESSION['crawler']->stop();
			echo 'Crawler stopped!';

		} else {

			echo "Not crawling";

		}

	}

	else echo "Unknown input";

}

?>
