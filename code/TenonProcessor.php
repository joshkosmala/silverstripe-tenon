<?php

class TenonProcessor extends Controller {

	private static $allowed_actions = array(
		'analyse',
		'analysePage'
	);

	// Analyse a page. The URL should have the page ID on it. The page content will be rendered
	// in draft, and sent to Tenon
	public function analysePage() {
		$params = $this->getURLParams();
		if (!isset($params['ID']) || !is_numeric($params['ID'])) {
			return "ID of the page to analyse is missing or invalid";
		}

		$pageID = $params['ID'];

		Versioned::reading_stage('Stage');

		// Get the page
		$page = Page::get()->byID($pageID);
		if (!$page) {
			return "Page does not exist";
		}

		$markup = Director::test($page->Link())->getBody();

		self::analyse($markup, $page);
	}

	// Invoke the tenon API. $content can either be an absolute URL, or markup to evaluate. $pageID is the page
	// that we associate the results with.
	public static function analyse($content, $page) {

		$tenon = SiteConfig::current_site_config();

		$tenon_options['key'] = $tenon->TenonAPIKey;

		// We need to tell tenon if it's a link or embedded content.
		if (self::is_url($content)) {
			$tenon_options['url'] = $content;
		} else {
			$tenon_options['src'] = $content;
		}

		// Initialise cURL
		$curlObj = curl_init();
		curl_setopt($curlObj, CURLOPT_URL, $tenon->TenonURL);
		//curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curlObj, CURLOPT_POST, true);
		curl_setopt($curlObj, CURLOPT_FAILONERROR, true);
		curl_setopt($curlObj, CURLOPT_POSTFIELDS, $tenon_options);
		//curl_setopt($curlObj,CURLOPT_HTTPHEADER, array('Expect: '));
		// Execute post, get results, close connection
		$data = curl_exec($curlObj);
		$code = curl_getinfo($curlObj, CURLINFO_HTTP_CODE);
		curl_close($curlObj);

		// Evaluate response
		if ($code === 200) {
			// Turn JSON response in to php array

			// First, delete existing results against this page, so we only have the latest.
			foreach ($page->TenonResults() as $tr) {
				$tr->Delete();
			}

			$results = json_decode($data, true);

			// add foreach here, iterate through the array creating new tenon results for each row in the array

			// this should be inside the foreach

			foreach($results['resultSet'] as $rsItem) {
				$tenonResult = new TenonResult();

                $timestamp = new SS_DateTime();
                $timestamp->setValue(date('Y-m-d H:i:s'));

				$tenonResult->PageID = $page->ID;
                $tenonResult->PageURL = $page->Link();
                $tenonResult->Timestamp = $timestamp;
                $tenonResult->PageDensity = $results['resultSummary']['density']['errorDensity'] / 100;
                $tenonResult->ErrorTitle = $rsItem['errorTitle'];
                $tenonResult->Title = $rsItem['errorTitle'];
                $tenonResult->Description = $rsItem['errorDescription'];
                $tenonResult->Snippet = html_entity_decode($rsItem['errorSnippet']);
                $tenonResult->Location = 'line: ' . $rsItem['position']['line'] . ', column: ' . $rsItem['position']['column'];
                $tenonResult->ResultType = 'Error';
				$tenonResult->write();
			}
		} else {
			TenonResult::createError("Tenon analyse didn't work. Are you behind a firewall? You need to be on a server connected to the internet. Perhaps your API key has expired or you forgot to fill it out?", $page);
			die();
		}

		// either success or fail cases return this. If there was an error, it would have been logged. Other cases that
		// don't return "ok" are if there was a problem connecting to TenonProcessor in the first place, so this needs
		// to remain here.
		echo "ok";
	}

	// Determine if a string is a URL. Return true if it is, false if it's not.
	public static function is_url($s) {
		$s = trim(strtolower($s));
		if (substr($s, 0, 7) == 'http://' || substr($s, 0, 8) == 'https://') {
			// absolute URLs
			return true;
		}

		if (substr($s, 0, 1) == '<') {
			// markup
			return false;
		}

		// @todo can we detect relative URLs, and do we even need to
		return false;
	}
}
