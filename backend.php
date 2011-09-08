<?php

require_once('crawler.php');

if (isset($_POST['request'])) {

	$crawler = new Crawler();
	
	$request = json_decode($_POST['request']);
	
	// Start crawling if not doing so already
	if ($request->type == 'crawl') {
		
		//echo 'Started crawling ' . $request->value;
		
		$crawler->setDepth(1);
		$crawler->crawl($request->value);
		$crawler->close();
		//echo $crawler->print_db();
	
	}
	
	else if ($request->type == 'status') {
	
		if ($crawler) {
			
			echo $crawler->getStatus();
			
		} else echo "Not crawling";
	
	}
	
	else echo "Unknown input";

}

?>
