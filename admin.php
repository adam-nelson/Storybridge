<?php

$ADMIN_AUTH = 'admin:blinkenlichten';
@include('data/login.php');

if(!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW'] != $ADMIN_AUTH) {
	header('WWW-Authenticate: Basic realm="Story Bridge App Admin"');
	header('HTTP/1.0 401 Unauthorized');
	echo 'Valid authentication required';
	exit;
}

require('php/scraper.php');

if(isset($_REQUEST['fetch'])) {
	scrapeData();
	header('Location: ?');
	exit;
}

try {
	$serverData = json_decode(@file_get_contents('data/data.json'));
	if(!$serverData) throw new Exception();
} catch (Exception $e) {
	$serverData = scrapeData();
}
if($serverData->timestamp + 24 * 60 * 60 < time() || !$serverData->data) $serverData = scrapeData();

?><html>
	<head>
		<title>Story Bridge App Admin</title>
		<meta name=viewport content="width=device-width, initial-scale=1"/>
		<link rel="stylesheet" href="css/admin.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="js/admin.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script type="text/javascript">
			var serverData = <?php echo json_encode($serverData ?: array('data' => array(), 'timestamp' => 0, 'backlog' => 30, 'email' => '')); ?>;
		</script>
	</head>
	<body>
		<h1>Story Bridge App Admin</h1>
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-lg-10-offset-2">
					<table class="data-table table table-bordered">
						<thead>
							<th style="width: 6%">Date</th>
							<th class="width: 15%">Colors</th>
							<th>Organization</th>
							<th class="width: 20%">Event Name</th>
							<th style="width: 25px;"></th>
						</thead>
						<tbody>
						</tbody>
					</table>
					<div class="form-inline">
						<div class="input-group">
							<input id="add-btn-input" class="form-control" type="text" placeholder="YYYY-MM-DD">
							<span class="input-group-btn">
								<button id="add-btn" type="button" class="btn btn-default">Add new date</button>
							</span>
						</div>

						<button id="save-btn" type="button" class="btn btn-primary">Save changes</button>
						<button id="clear-btn" type="button" class="btn btn-danger">Clear all data</button>
					</div>
					<br/>
					<div class="form-inline">
						<button id="fetch-btn" type="button" class="btn btn-default">Fetch updates from council website</button>
					</div>
					<hr/>
					<h4>Settings:</h4>
					<div class="form-inline">
						<div class="input-group">
							<span class="input-group-addon">Days to keep in the past</span>
							<input id="backlog-input" class="form-control" type="number" min="0" value="<?php echo $serverData->backlog ?: 30 ?>"/>
						</div>
						<div class="input-group">
							<span class="input-group-addon">Notification email address</span>
							<input id="email-input" class="form-control" type="email"  value="<?php echo htmlentities($serverData->email) ?>"/>
						</div>
						<button id="settings-btn" type="button" class="btn btn-primary">Save</button>
					</div>
					<br/>
				</div>
			</div>
		</div>
		<div id="infobox" class="modal fade" role="dialog">
			<div class="modal-dialog modal-md">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close glyphicon glyphicon-remove" data-dismiss="modal"></button>
						<h4 class="modal-title" id="infobox-title">Info</h4>
					</div>
					<div class="modal-body">
						<p id="infobox-text"></p>
					</div>
					<div class="modal-footer">
						<button id="infobox-yes" type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
						<button id="infobox-no" type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button id="infobox-close" type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>