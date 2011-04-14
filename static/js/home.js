/**
 * JavaScript for the eCAS Homepage 
 */

// Configuration Settings
var filterListLength = 8;
var blurbLength      = 300;

// Document Initialization
$(document).ready(function() {
	
	// Appearance fixups
	initWindow();
	
	// Initialize the product type browser
	initProductTypeBrowser();
	
	// Init product type filter menu
	initFilterTabMenu();
	
	// Initialize the product type filter tabs
	initFilterTab('investigator');
	initFilterTab('protocol');
	initFilterTab('site');

	// Initialize Collab Group links
	$('div.collabGroupLink a').click(function(e) {
		e.preventDefault();
		$('#loadingDatasets').show();
		var dataUrl = $(this).attr('href');
		$.get(dataUrl,{},function(data) {
			updateProductTypeBrowser(data);
		},'json');
	});
	
});

function initWindow() {
	
	$('.box').corner();
	
}

function initProductTypeBrowser() {
	
	// Remove any selections
	$('div.facet ul.filterList li')
		.removeClass('selected')
		.children('.removeLink').hide();
	
	 // Enable the loading message
	$('#loadingDatasets').show();
	
	// Load all data
	$.get('/ecas/data/productTypeFilter.do?key=DataSetName&value=*',
		updateProductTypeBrowser);
}


function initFilterTabMenu() {
	$menu = $('#facetMenu');
	$list = $('#facetMenu ul');
	$elms = $list.children('li');
	
	// Start by showing the PI tab only
	filterMenuChangeTo('facet-organ');
	
	// Enable clicking on menu links
	$elms.children('a')
		.click(function(e) {
			filterMenuChangeTo($(this).attr('href').substr(1));
			$('#facetMenu span.label').text($(this).attr('title'));
			$(this).parent().siblings().removeClass('selected');
			$(this).parent().addClass('selected');
			$(this).blur();
			e.stopPropagation();
			return false;
		})
		.hover(function(e) {
			$('#facetMenu span.label').text('Filter by ' + $(this).attr('title'));
		},function(e) {
			$('#facetMenu span.label').text($('#facetMenu ul li.selected a').attr('title'));
		});
}

function filterMenuChangeTo(tab) {
	// Hide all to start
	$('div.facet.box').hide();
	
	// Show the desired tab
	$('div#' + tab).show();
}

function initFilterTab(which) {
	
	var $list     = $('ul#' + which + 'List');
	var $listElms = $('ul#' + which + 'List li');
	var $moreLink = $('a#'  + which + 'MoreLink'); 
	var $searchBox= $('#'   + which + 'Search');
	
	// First, limit the number of `which` shown to `filterListLength`
	$listElms.slice(filterListLength).hide();
	
	// Then provide a link to "show all" or "show fewer" investigators
	$list.after(
		$('<a>')
			.addClass('moreLink')
			.attr('id', which + 'MoreLink')
			.attr('href','#')
			.click(function(e) {
				if ($(this).text() == 'Show Fewer') {
					$listElms.slice(filterListLength).hide();
					$(this).text('Show All ' + $listElms.length);
					$(this).blur();
				} else {
					$listElms.show();
					$searchBox.val('').labelify();
					$(this).text('Show Fewer');
					$(this).blur();
				}
				e.stopPropagation();
				return false;
			})
			.text('Show All ' + $listElms.length));
	
	// Then set up the links to cancel filtering
	$listElms.children('.removeLink').click(function(e) {
		initProductTypeBrowser();		
	});
			
	// Then set up the search box
	$searchBox
		.labelify()
		.keyup(function(e) {
			var needle = $(this).val();
			
			if (needle.length == 0) {
				$listElms.show().slice(filterListLength).hide();
				$moreLink.text('Show All ' + $listElms.length);
			} else {
				$listElms.hide();
				$listElms.filter(function() {
					return $(this).attr("title")
						.toLowerCase().indexOf(needle.toLowerCase()) != -1;  
				}).show().slice(filterListLength).hide();
			}
		});
	
	// Finally, enable clicking on a `which` name
	$listElms.children('a:first-child').click(function() {
		$.get($(this).attr('href'),updateProductTypeBrowser);
		$('div.facet ul.filterList li')
			.removeClass('selected')
			.children('.removeLink').hide();
		$(this).parent().addClass('selected');
		$(this).parent().children('.removeLink').show();
		$(this).blur();
		return false;
	});	
}





function updateProductTypeBrowser( data ) {
	$('#loadingDatasets').hide();
	$('#ptBrowser').empty();
	$.each(data,function(i,v) {
		$('#ptBrowser').append(
			generateProductTypeTeaser( v ));
	});
}

function generateProductTypeTeaser( data ) {
	var $blurb = $('<em>').text('No description available yet'); 
	if (data.description) {
		$blurb = $('<p>').text(data.description[0]);
		if ($blurb.text().length > blurbLength) {
			$blurb.text($blurb.text().substr(0,blurbLength) + '...');
			$blurb.append($('<a>').attr('href','/ecas/data/dataset/'+data.DatasetId[0]).text('more'));			
		}
	}
	
	var $content = $('<div>').addClass('productTypeTeaser')
		.append($('<h5>').css('margin-bottom','0px').append($('<a>').attr('href','/ecas/data/dataset/'+data.DatasetId[0]).text(data.DataSetName[0])))
		.append($('<div>').addClass('quiet').text(data.LeadPI[0]))
		.append($('<div>').addClass('blurb').html($blurb));
	
	return $content;
}
