<?php

class Crawler {

	private $db;
	private $db_url = 'db/db.sqlite';

	public function __construct() {
	
		$this->db = new Sqlite3($this->db_url);
		
		$create_str = 'CREATE TABLE IF NOT EXISTS crawl_data (
							domain	VARCHAR(80), 
							email	VARCHAR(80) UNIQUE
						)';
		
		$this->db->exec($create_str);
		
	}
	
	public function crawl($url) {
	
		if ($handle	= fopen($url, 'r')) {
		
			$content	= stream_get_contents($handle);
			$parsed		= parse_url($url);
			$links		= $this->getLinks($content);
			$emails		= $this->getEmailAddresses($content);
			
			$insert_str = 'INSERT OR IGNORE INTO crawl_data (domain, email)
						   VALUES (:domain, :email)';
			
			// At the moment, only crawl for e-mail addresses
			// To be moved into separate functions later
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
	
	// Allows crawling of multiple urls ("seeds") passed as a string array
	public function crawlSeeds($seeds) {
	
		if (is_array($seeds)) {

			foreach ($seeds as $seed) {
			
				if (is_string($seed)) $this->crawl($seed);
			
			}

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
	
	
	// Temporary dummy function to output the database
	
	public function print_db() {
		
		$result	= $this->db->query('SELECT * FROM crawl_data ORDER BY domain');
		
		echo '<table>';
		
		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			
			echo '<tr>';
			echo '<td>'.$row['domain'].'</td><td>'.$row['email'].'</td>';
			echo '</tr>';
			
		}
		
		echo '</table>';
		
	}
	
	public function close() {

		$this->db->close();
	
	}

}

?>
