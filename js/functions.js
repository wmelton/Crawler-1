var crawling = false;
var timer;

// Get data from input text, create a json object, pass it to our ajax request
function crawlStart() {

	if (!crawling) {

		crawling = true;

		var url	= document.getElementById('input_crawl').value;
		var obj	= { 
			"type": "crawl",
			"value": url
		};

		var json		= JSON.stringify(obj);
		var ajaxCrawl	= new ajaxObject('backend.php', handleResponse);

		ajaxCrawl.doPost('request=' + json);
		// Send a status request message each second
		timer = setInterval(getStatus, 1000);

	}

	else {

		updateStatus('Already crawling!');
		return;

	}

}

function crawlStop() {

	var obj	= { 
		"type": "status",
		"value": "stop"
	};

	var json		= JSON.stringify(obj);
	var ajaxCrawl	= new ajaxObject('backend.php', handleResponse);

	ajaxCrawl.doPost('request=' + json);
	clearInterval(timer);
	crawling = false;

}

// Queries the crawler for a status message
function getStatus() {

	var obj	= { 
		"type": "status",
		"value": "check"
	};

	var json		= JSON.stringify(obj);
	var statCrawl	= new ajaxObject('backend.php', updateStatus);

	statCrawl.doPost('request=' + json);

}

// Handle ajax response

function handleResponse(resptxt) {

	document.getElementById('status').innerHTML = resptxt;

}

function updateStatus(resptxt) {

	document.getElementById('tmp').innerHTML = resptxt;

}

// Create concurrent ajax objects

function ajaxObject(url, callback) {

	var req = createRequest();
	req.onreadystatechange = processRequest;

	function createRequest() {

		if (window.XMLHttpRequest) 	{

			return new XMLHttpRequest();


		} else {

			return new ActiveXObject("Microsoft.XMLHTTP");

		}

	}

	function processRequest() {

		if (req.readyState == 4 && req.status == 200) {

			if (callback) callback(req.responseText);

		}


	}

	this.doGet = function() {

		req.open('GET', url, true);
		req.send();

	}

	this.doPost = function(body) {

		req.open('POST', url, true);
		req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.send(body);

	}

}
