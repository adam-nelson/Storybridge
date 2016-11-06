<?php

require('php/scraper.php');

$reqDate = isset($_REQUEST['date']) ? $_REQUEST['date'] : date('Y-m-d');

$data = json_decode(file_get_contents(DATA_FILE));

$infoPerDate = get_object_vars($data->data);

ksort($infoPerDate);

$today = isset($_REQUEST['data']) ? json_decode($_REQUEST['data']) : $infoPerDate[$reqDate];

$rdt = DateTime::createFromFormat('Y-m-d', $reqDate)->getTimestamp();
$next = null;
for($i = 1; $i <= 30; $i++) {
	$dt = new DateTime();
	$dt->setTimestamp($rdt + $i * 86400);
	$ymd = $dt->format('Y-m-d');
	
	if(isset($infoPerDate[$ymd])) {
		$d = $infoPerDate[$ymd];
		if($d->name != $today->name || implode(',', $d->colors) != implode(',', $today->colors)) {
			$next = $d;
			if($i == 1) {
				$next->dayDesc = "tomorrow";
			} else {
				$next->dayDesc = "on " . $dt->format('l, j M');
			}
			$next->date = $ymd;
			break;
		}
	}
}

$upcoming = [$next];
$upcPrev = $next;
$upcPrevStartDT = $upcPrevEndDT = DateTime::createFromFormat('Y-m-d', $next->date);

if($next) {
	$keys = array_keys($infoPerDate);
	for($i = array_search($next->date, $keys); $i <= count($keys); $i++) {
		$d = $i < count($keys) ? $infoPerDate[$keys[$i]] : null;
		if(!$d || $d->name != $upcPrev->name || implode(',', $d->colors) != implode(',', $upcPrev->colors)) {
			if($upcPrevStartDT->getTimestamp() == $upcPrevEndDT->getTimestamp()) {
				$upcPrev->rangeDesc = $upcPrevStartDT->format('l, j M');
			} else {
				$upcPrev->rangeDesc = $upcPrevStartDT->format('l, j M') . ' - ' . $upcPrevEndDT->format('l, j M');
			}

			if($d) {
				$upcoming[] = $upcPrev = $d;
				$upcPrevStartDT = $upcPrevEndDT = DateTime::createFromFormat('Y-m-d', $keys[$i]);
			}
		} else {
			$upcPrevEndDT = DateTime::createFromFormat('Y-m-d', $keys[$i]);
		}
	}
}

require('template.php');
