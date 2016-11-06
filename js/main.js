$(function() {
	$('#upcoming-btn').click(function() {
		$('#today').slideUp();
		$('#upcoming').slideDown();
		return false;
	});
	$('#today-btn').click(function() {
		$('#upcoming').slideUp();
		$('#today').slideDown();
		return false;
	});

	var spd1 = 125, spd2 = 75, spd3 = 250;

	$('#today > div:first').animate({ opacity: 1 }, spd3);
	$('#today > h1').delay(1 * spd1).animate({ opacity: 1 }, spd3);
	$('#today > h2').delay(2 * spd1).animate({ opacity: 1 }, spd3);
	$colors = $('#today > aside span');
	$colors.each(function(i) {
		$(this).delay(3 * spd1 + i * spd2).animate({ opacity: 1 }, spd3);
	});
	$('#today > p').delay(3 * spd1 + $colors.length * spd2).animate({ opacity: 1 }, spd3);
	$('#today > hr').delay(3 * spd1 + $colors.length * spd2).animate({ opacity: 1 }, spd3);
	$('#today > .nextup').delay(3 * spd1 + $colors.length * spd2).animate({ opacity: 1 }, spd3);
	$('#today > h4').delay(3 * spd1 + $colors.length * spd2).animate({ opacity: 1 }, spd3);

});