

$(document).ready(function() {
	
	// Initialize the protocol labels so that when they are clicked
	// the corresponding datasets appear
	$('div.dataset-list h2')
		.addClass('clickable')
		.click(function() {
		$(this).next().children('li').toggle('fast');
	});

});