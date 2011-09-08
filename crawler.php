<?php

class Crawler {

	private $db;
	private $db_url = 'db/db.sqlite';
	// The depth variable defines how far the crawler should traverse
	// -1 = infinite, 0 = single page, 1 = single domain, >1 = number of domains
	private $depth = 1;
	// Keep track of pages crawled to avoid crawling them again
	private static $crawled = array();
	private $count = 0;
	// Broadcast each site crawled to client
	private $broadcast = true;

	public function __construct() {
	
		$this->db = new Sqlite3($this->db_url);
		
		// Store only unique e-mail addresses
		$create_str = 'CREATE TABLE IF NOT EXISTS crawl_data (
							domain	VARCHAR(80), 
							email	VARCHAR(80) UNIQUE
						)';
		
		$this->db->exec($create_str);
		
	}
	
	public function crawl($url) {
	
		if ($handle	= fopen($url, 'r')) {

			// Add page to list of previously crawled pages
			self::$crawled[] = $url;
			error_log(count(self::$crawled) + "\n", 3, 'tmp.log');
		
			$content	= stream_get_contents($handle);
			$mimetype	= $this->getMimeType($content);
			
			// For now, only crawl text/html files
			if ($mimetype == 'text/html') {

				$host		= $this->getHost($url);
				$links		= $this->getLinks($content);
				$emails		= $this->getEmailAddresses($content);
			
			} else {
				
				fclose($handle);
				return;
			
			}

			if ($this->broadcast) {
			
				echo 	'Crawling.. ' . $url . 
						' Links.. ' . count($links) .  
						' Crawled ' . count(self::$crawled) . '<br />';
						
			}
						
			fclose($handle);

			// $depth == 0, only given url is to be searched
			if ($this->depth == 0) {
			
				$this->storeEmailAddresses($emails, $host);
			
			}
			
			// $depth == 1, entire domain is to be searched
			if ($this->depth == 1) {
				
				// Save any e-mail address on current page
				// Then proceed to traverse further
				$this->storeEmailAddresses($emails, $host);
				
				foreach($links as $link) {
					// Check to see if page belongs to same domain
					$p_link = parse_url($link);
					// isset to avoid 'undefined index' notice
					if (isset($p_link['host'])) {
						
						if ($p_link['host'] == $host) {
							//Check to see if page has been crawled before
							if (!in_array($link, self::$crawled)) {
								// Make sure link doesn't point to self
								if (($link != $url) && 
									($link != ($url . '/'))) {
								
									$this->crawl($link);
							
								}
							}
						}
					}
				}
			}
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
	
	public function getStatus() {
	
		return $this->count;
	
	}
	
	private function getLinks($dom) {
	
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
	
	private function getEmailAddresses($dom) {
		
		// Pretty basic function to scrape e-mail addresses from text
		// and return them in an array.
		$emails = array();
		
		// Search for a regular e-mail pattern, i.e. 'example@example.org'
		preg_match_all('/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i', 
						$dom, $emails);
		
		// Return only full pattern matches, not subpatterns etc.
		// Strip out duplicate addresses.
		return array_unique($emails[0]);
	
	}
	
	private function storeEmailAddresses($emails, $domain) {
	
		$insert_str = 'INSERT OR IGNORE INTO crawl_data (domain, email)
						   VALUES (:domain, :email)';

		$stmt = $this->db->prepare($insert_str);
			
		foreach ($emails as $email) {
			
			$stmt->bindValue(':domain', $domain, SQLITE3_TEXT);
			$stmt->bindValue(':email', $email, SQLITE3_TEXT); 
			$result = $stmt->execute();
			
		}
			
		$stmt->close();
	
	}
	
	private function getMimeType($str) {
	
		$fileinfo = new finfo(FILEINFO_MIME_TYPE);
		
		return $fileinfo->buffer($str);
	
	}
	
	private function getHost($url) {
	
		$parsed		= parse_url($url);
		
		return $parsed['host'];
	
	}
	
	public function setDepth($depth) {
	
		$this->depth = $depth;
	
	}
	
	public function close() {

		$this->db->close();
	
	}
	
	// Temporary dummy function to output the database in an html table
	public function print_db() {
		
		$result	= $this->db->query('SELECT * FROM crawl_data ORDER BY domain');
		$output = '';
		
		$output .= '<table>';
		$output .= '<tr><td>Domain</td><td>E-mail</td></tr>';
		
		while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
			
			$output .= '<tr>';
			$output .= '<td>'.$row['domain'].'</td><td>'.$row['email'].'</td>';
			$output .= '</tr>';
			
		}

		$output .= '</table>';
		
		return $output;
	}

}

?>
