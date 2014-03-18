$(document).ready(function(){

	$('.jjaccordion-header').toggleClass('inactive-header');
	
	//Open The First Accordion Section When Page Loads
	$('.jjaccordion-header').first().toggleClass('active-header').toggleClass('inactive-header');
	$('.jjaccordion-content').first().slideDown().toggleClass('open-content');
	
	// The Accordion Effect
	$('.jjaccordion-header').click(function () {
		if($(this).is('.inactive-header')) {
			$('.active-header').toggleClass('active-header').toggleClass('inactive-header').next().slideToggle('fast').toggleClass('open-content');
			$(this).toggleClass('active-header').toggleClass('inactive-header');
			$(this).next().slideToggle('fast').toggleClass('open-content');
		}
		
		else {
			$(this).toggleClass('active-header').toggleClass('inactive-header');
			$(this).next().slideToggle('fast').toggleClass('open-content');
		}
	});
	
	return false;
});