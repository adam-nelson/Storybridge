<!DOCTYPE html>
<html>
	<head>
		<title>Story Bridge</title>
		<meta name=viewport content="width=device-width, initial-scale=1"/>
		<link rel="stylesheet" href="css/main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="js/main.js"></script>
	</head>
	<body>
		<main id="today">
			<?php if($today): ?>
				<div style="opacity: 0;"><em>Today, the Story Bridge reminds of:</em></div>
				<h1 style="opacity: 0;"><?php echo htmlentities($today->name); ?></h1>
				<h2 style="opacity: 0;"><?php echo htmlentities($today->orga); ?></h2>
				<aside>
					<?php foreach($today->colors as $color): ?>
						<span style="opacity: 0; box-shadow: 0 0 10px 10px <?php echo $color; ?>; background-color: <?php echo $color; ?>;"></span>
					<?php endforeach; ?>
				</aside>
				<p style="opacity: 0;"><?php echo nl2br($today->desc); ?></p>
			<?php else: ?>
				<h2 style="opacity: 0;">Today, we don't know anything about the Story Bridge...</h2>
			<?php endif; ?>
			<?php if($next): ?>
				<hr style="opacity: 0;"/>
				<div class="nextup" style="opacity: 0;"><em>Next up, <?php echo $next->dayDesc; ?>:</em></div>
				<h4 style="opacity: 0;">
					<aside class="next">
						<?php foreach($next->colors as $color): ?>
							<span style="box-shadow: 0 0 10px 10px <?php echo $color; ?>; background-color: <?php echo $color; ?>;"></span>
						<?php endforeach; ?>
						<?php echo htmlentities($next->name . ' (' . $next->orga . ')'); ?>
					</aside>
				</h4>
			<?php endif; ?>
			<i></i>
			<?php if($upcoming): ?>
				<nav><button id="upcoming-btn">View upcoming</button></nav>
			<?php endif; ?>
		</main>
		<?php if($upcoming): ?>
			<main id="upcoming" style="display: none;">
				<div><em>Upcoming events:</em></div>
				<?php foreach($upcoming as $event): ?>
					<p><?php echo $event->rangeDesc; ?>:</p>
					<h4>
						<aside class="next">
							<?php foreach($event->colors as $color): ?>
								<span style="box-shadow: 0 0 10px 10px <?php echo $color; ?>; background-color: <?php echo $color; ?>;"></span>
							<?php endforeach; ?>
							<?php echo htmlentities($event->name . ' (' . $event->orga . ')'); ?>
						</aside>
					</h4>
					<hr/>
				<?php endforeach; ?>
				<i></i>
				<nav><button id="today-btn">View today</button></nav>
			</main>
		<?php endif; ?>
		<footer>
			Background image: Riverfestival at Story Bridge -
			<a href="http://www.flickr.com/photos/pauls_parking/3913504271/in/photostream/">pauls parking @ Flickr</a> -
			<a href="http://creativecommons.org/licenses/by/2.0">CC BY 2.0</a>
		</footer>
	</body>
</html>