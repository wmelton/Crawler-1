<?php

class Crawler {

	private $db;
	private $db_url = 'db/db.sqlite';

	public function __construct() {
		
		$this->db = new Sqlite3($this->db_url);
		
	}
	
	public function crawl($url) {
	
		if ($handle	= fopen($url, 'r')) {
		
			$content	= stream_get_contents($handle);
			$parsed		= parse_url($url);
			$links		= $this->getLinks($content);
			$emails		= $this->getEmailAddresses($content);	
			
			echo $parsed['host'];
			
			//print_r($links);
			print_r($emails);

			fclose($handle);
		
		}
	
	}
	
	public function getLinks($dom) {
	
		$domdoc = new DOMDocument();
		// Ignore warnings of malformed html
		@$domdoc->loadHTML($dom);
		
		// Save all <a> tags to array
		$links		= $domdoc->getElementsByTagName('a'); 
		$num_links	= $links->length;
		$hrefs		= array();
		
		// For each link, extract the href attribute 
		for ($i = 0; $i < $num_links; $i++) {
		
			$attributes = $links->item($i)->attributes;
			
			if ($attributes->getNamedItem('href')) {
			
				$hrefs[] = $attributes->getNamedItem('href')->nodeValue;
			
			}
			
		}
		
		return $hrefs;
	
	}
	
	public function getEmailAddresses($dom) {
		
		// Pretty basic function to scrape e-mail addresses from text
		// and return them in an array.
		$emails = array();
		
		// Search for a regular e-mail pattern, i.e. 'example@example.org'
		preg_match_all('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', $dom, $emails);
		
		// Return only full pattern matches, not subpatterns etc.
		// Strip out duplicate addresses.
		return array_unique($emails[0]);
	
	}

}

?>
