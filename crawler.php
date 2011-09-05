<?php

class Crawler {

	private $db;
	private $db_url = 'db/db.sqlite';

	public function __construct() {
	
		$this->db = new Sqlite3($this->db_url);
		
		$create_str = 'CREATE TABLE IF NOT EXISTS crawl_data (
							domain varchar(80), 
							email varchar(80)
						)';
		
		$this->db->exec($create_str);
		
	}
	
	public function crawl($url) {
	
		if ($handle	= fopen($url, 'r')) {
		
			$content	= stream_get_contents($handle);
			$parsed		= parse_url($url);
			$links		= $this->getLinks($content);
			$emails		= $this->getEmailAddresses($content);
			
			$insert_str = 'INSERT INTO crawl_data (domain, email)
							VALUES (:domain, :email)';
			
			$stmt = $this->db->prepare($insert_str);
			
			foreach ($emails as $email) {
			
				$stmt->bindValue(':domain', $parsed['host'], SQLITE3_TEXT);
				$stmt->bindValue(':email', $email, SQLITE3_TEXT); 
				$result = $stmt->execute();
			
			}
			
			$stmt->close();

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
	
	public function print_db() {
		
		
		
	}
	
	public function close() {

		$this->db->close();
	
	}

}

?>
