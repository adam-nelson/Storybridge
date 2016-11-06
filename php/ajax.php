<?php

require('scraper.php');

if(isset($_POST['saveData'])) {
	$data = json_decode(file_get_contents(DATA_FILE));
	$data->data = $_POST['data'] ?: new StdClass(); // Because the data object won't be sent by AJAX if it's empty
	file_put_contents(DATA_FILE, json_encode($data));
}
if(isset($_POST['settings'])) {
	$data = json_decode(file_get_contents(DATA_FILE));
	$data->backlog = (integer)$_POST['settings']['backlog'] ?: 30;
	$data->email = $_POST['settings']['email'];
	file_put_contents(DATA_FILE, json_encode($data));
}
