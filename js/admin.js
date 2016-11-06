$(function() {
	$('#fetch-btn').click(function() {
		document.location.href = '?fetch';
	});

	$('#add-btn').click(function() {
		var date = new Date($('#add-btn-input').val());
		dateYMD = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).substr(-2) + '-' + ('0' + date.getDate()).substr(-2)

		var $existingField = $('[data-date="' + dateYMD + '"]');
		var $row;
		if($existingField.length) {
			$row = $existingField.parent();
		} else {
			$row = addRow({
				date: dateYMD,
				colors: [],
				orga: '',
				name: ''
			}, true);
		}

		$row[0].scrollIntoView();
		$row.find('.colors').focus();
	});

	$('#save-btn').click(function() {
		var $rows = $('.data-row');

		var data = {};

		$rows.each(function(_, row) {
			var $row = $(row);
			var date = $row.find('[data-timestamp]').attr("data-date");
			var rowObj = getRowObj($row);

			data[date] = rowObj;
		});

		$.post('php/ajax.php', {data: data, saveData: 1}, function() {
			$('#infobox-title').text('Info');
			$('#infobox-text').text('Data saved successfully!');
			$('#infobox-yes').hide();
			$('#infobox-no').hide();
			$('#infobox-close').show();
			$("#infobox").modal();
		});
	});

	$('#clear-btn').click(function() {
		$('#infobox-title').text('Clearing Data');
		$('#infobox-text').text('This will clear all event data. Are you sure?');
		$('#infobox-yes').click(function() {
			$('.data-row').remove();
			$('#infobox-yes').off('click');
		}).show();
		$('#infobox-no').show();
		$('#infobox-close').hide();
		$("#infobox").modal();
	});

	$('#settings-btn').click(function() {
		$.post('php/ajax.php', {settings: {
			backlog: $("#backlog-input").val(),
			email: $("#email-input").val()
		}}, function() {
			$('#infobox-title').text('Info');
			$('#infobox-text').text('Settings saved successfully!');
			$('#infobox-yes').hide();
			$('#infobox-no').hide();
			$('#infobox-close').show();
			$("#infobox").modal();
		});
	});

	Object.keys(serverData.data).sort().forEach(function(row) {
		var rowData = JSON.parse(JSON.stringify(serverData.data[row]));
		rowData.date = row;
		addRow(rowData, false);
	});
});

function getRowObj($row) {
	var rowObj = {};
	rowObj.colors = $row.find('.colors').val().split(',').map(function(el) { return el.trim(); });
	rowObj.orga = $row.find('.orga').val();
	rowObj.name = $row.find('.name').val();
	rowObj.desc = $row.find('.desc').val();
	return rowObj;
}

function addRow(rowData, sorted) {
	var daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
	var rowDateObj = new Date(rowData.date);
	rowDateObj.setHours(0, 0, 0, 0);

	var today = new Date();
	today.setHours(0, 0, 0, 0);

	var $row = $('<tr/>', {
		'class': 'data-row' + (rowDateObj.valueOf() == today.valueOf() ? ' today' : '')
	});

	var $date = $('<td/>', {
		'data-timestamp': rowDateObj.valueOf(),
		'data-date': rowData.date,
		text: daysOfWeek[rowDateObj.getDay()] + ', ' + rowData.date
	});
	$row.append($date);

	var $colors = $('<td/>');
	$colors.append($('<input/>', {
		type: 'text',
		value: rowData.colors.join(', '),
		'class': 'colors form-control'
	}));
	$row.append($colors);

	var $orga = $('<td/>');
	$orga.append($('<input/>', {
		type: 'text',
		value: rowData.orga,
		'class': 'orga form-control'
	}));
	$row.append($orga);

	var $name = $('<td/>');
	$name.append($('<input/>', {
		type: 'text',
		value: rowData.name,
		'class': 'name form-control'
	}));
	$row.append($name);

	var $desc = $('<td/>', {
		'class': 'hidden'
	});
	$desc.append($('<input/>', {
		type: 'hidden',
		value: rowData.desc,
		'class': 'desc form-control'
	}));
	$row.append($desc);

	var $btns = $('<td/>');
	$btns.append($('<button/>', {
		type: 'button',
		title: 'Edit description',
		'class': 'btn btn-default glyphicon glyphicon-new-window',
		click: function() {
			$('#infobox-title').text('Event Description');
			var $ta = $('<textarea/>', {
				id: "infobox-textarea",
				'class': "form-control",
				rows: 10
			});
			$ta.val($row.find('.desc').val());
			$('#infobox-text').html($ta);
			$('#infobox-text').append('You can use HTML in the description.')
			$('#infobox-yes').click(function() {
				$row.find('.desc').val($('#infobox-textarea').val());
				$('#infobox-yes').off('click');
			}).show();
			$('#infobox-no').show();
			$('#infobox-close').hide();
			$("#infobox").modal();
			setTimeout(function() {
				$('#infobox-text textarea').focus();
			}, 400);
		}
	}));
	$btns.append("&nbsp;");
	$btns.append($('<button/>', {
		type: 'button',
		title: 'Preview',
		'class': 'btn btn-default glyphicon glyphicon-eye-open',
		click: function() {
			window.open('index.php?date=' + encodeURIComponent(rowData.date) + '&data=' + encodeURIComponent(JSON.stringify(getRowObj($row))));
		}
	}));
	$btns.append("&nbsp;");
	$btns.append($('<button/>', {
		type: 'button',
		title: 'Remove',
		'class': 'btn btn-danger glyphicon glyphicon-remove',
		click: function() {
			$row.remove();
		}
	}));
	$row.append($btns);
	
	insertRow($row, sorted);

	return $row;
}

function insertRow($row, sorted) {
	var $rows = $('.data-row');

	if($rows.length && sorted) {
		var $before = null;

		$rows.each(function(_, r) {
			if(Number($(r).find('[data-timestamp]').attr('data-timestamp')) < Number($row.find('[data-timestamp]').attr('data-timestamp'))) {
				$before = $(r);
			}
		});

		if($before) {
			$before.after($row);
		} else {
			$('.data-table tbody').prepend($row);
		}
	} else {
		$('.data-table tbody').append($row);
	}
}