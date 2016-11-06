<?php

error_reporting(E_ALL & ~E_NOTICE);

date_default_timezone_set('Australia/Sydney');

define('ROOT_DIR', dirname(__DIR__));
define('DATA_DIR', ROOT_DIR . '/data');
define('DATA_FILE', DATA_DIR . '/data.json');

@mkdir(DATA_DIR);

$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
$validColors = ['aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure', 'beige', 'bisque', 'black', 'blanchedalmond', 'blue', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgreen', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'gray', 'green', 'greenyellow', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightsteelblue', 'lightyellow', 'lime', 'limegreen', 'linen', 'magenta', 'maroon', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive', 'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue', 'slategray', 'snow', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white', 'whitesmoke', 'yellow', 'yellowgreen'];

function getInnerTextWithNL($el) {
	$text = "";

	foreach($el->childNodes as $c) {
		if($c->tagName == "br") {
			$text .= "\n";
		} else {
			$text .= $c->textContent;
		}
	}

	return $text;
}

function getDateFromS($dateStr) {
	global $months;
	$dateParts2 = preg_split('/\\s+/', $dateStr);
	$d = (integer)$dateParts2[1];
	$m = array_search($dateParts2[2], $months) + 1;
	$y = (integer)(((integer)$m < (integer)date("n") - 6) ? (integer)date("Y") + 1 : date("Y"));
	$dt = new DateTime();
	$dt->setTime(0, 0, 0);
	$dt->setDate($y, $m, $d);
	return $dt;
}

function prepareText($t) {
	return preg_replace('/[\\r\\n]/', '; ', preg_replace('/[^\\w\\s,-]/', '$1', preg_replace('/[\\x{00A0}]/', ' ', utf8_decode($t))));
}

function getM($dateStr) {
	return preg_split('/\\s+/', $dateStr)[2];
}

function cropData($data, $days) {
	$backlogDate = new DateTime();
	$backlogDate->setTimestamp(time() - $days * 24 * 60 * 60);
	$backlogDate->setTime(0, 0, 0);

	foreach(get_object_vars($data->data) as $day => $dayData) {
		if(DateTime::createFromFormat('Y-m-d', $day) < $backlogDate) unset($data->data->$day);
	}
}

function scrapeData($notify = false) {
	$doc = new DOMDocument();
	@$doc->loadHTMLFile("https://www.brisbane.qld.gov.au/laws-permits/laws-permits-businesses/light-brisbane-hang-bridge-banner/light-council-asset");
	$xp = new DOMXpath($doc);

	$rows = $xp->query("//table[@summary='This table shows the schedule of future light ups for Story Bridge by date, colour and organisation.']/tbody/tr");

	$infoPerDate;

	$serverData = (object)["data" => new StdClass(), "timestamp" => 0];
	try {
		$serverData = json_decode(@file_get_contents(DATA_FILE));
	} catch(Exception $e) {}

	$infoPerDate = $serverData->data;

	foreach($rows as $row) {
		$cols = $xp->query("./td", $row);

		$dates = prepareText(getInnerTextWithNL($cols->item(0)));
		$colors = prepareText(getInnerTextWithNL($cols->item(1)));
		$reason = explode(' - ', prepareText(getInnerTextWithNL($cols->item(2))));
		$orga = $reason[0];
		$name = $reason[1];

		$colors_plain = array();

		preg_replace_callback('/\\w*/', function($c) use(&$colors_plain) {
			global $validColors;
			if(in_array(strtolower($c[0]), $validColors)) {
				$colors_plain[] = strtolower($c[0]);
			}
		}, $colors);

		$colors_plain = array_unique($colors_plain); 

		$orga = htmlentities($orga);
		$name = htmlentities($name);

		$dateParts = preg_split('/\\s*-\\s*/', $dates);

		if(count($dateParts) > 1) {
			if(getM($dateParts[0]) && !getM($dateParts[1])) $dateParts[1] .= ' ' . getM($dateParts[0]);
			if(getM($dateParts[1]) && !getM($dateParts[0])) $dateParts[0] .= ' ' . getM($dateParts[1]);
		}

		$firstDate = getDateFromS($dateParts[0]);
		$lastDate = getDateFromS($dateParts[1] ?: $dateParts[0]);

		for($d = $firstDate->getTimestamp(); $d <= $lastDate->getTimestamp(); $d += 86400) {
			$dt = new DateTime();
			$dt->setTimestamp($d);
			$ymd = $dt->format('Y-m-d');
			
			if(!isset($infoPerDate->$ymd)) {
				$infoPerDate->$ymd = (object)["colors" => $colors_plain, "orga" => $orga, "name" => $name, "description" => ""];
				$newEntriesAdded = true;
			}
		}
	}

	cropData($infoPerDate, $serverData->backlog ?: 30);

	$serverData->timestamp = time();
	$serverData->data = $infoPerDate;

	file_put_contents(DATA_FILE, json_encode($serverData));
	
	if($newEntriesAdded && $notify && $data->email) {
		$text = "New data has been scraped from the council website. Please log in to the Story Bridge App admin to verify!";
		mail($data->email, "New Story Bridge Entries", $text, "From: Story Bridge App <storybridge@dotlabs.co>\r\n");
	}

	return $serverData;
}

// *********************************************************************************************************
// In order not to be totally dependent on cron, the app checks here when the last scrape was done and does one if needed
$data = (object)["timestamp" => 0];
try {
	$data = json_decode(@file_get_contents(DATA_FILE));
} catch(Exception $e) {}
if($data->timestamp < time() - 24.5 * 60 * 60) scrapeData(true);
unset($data);
