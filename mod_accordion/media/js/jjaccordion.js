(function($){

	$.fn.jjaccordion = function(options) {
		// Set some defaults
		var defaults = {
			header: 'jjaccordion-header',
			activeheader: 'active-header',
			inactiveheader: 'inactive-header',
			content: 'jjaccordion-content',
			opencontent: 'open-content',
			open: true,
			speed: 'fast'
		};

		// Merge defaults with injected params
		var options 	= $.extend({}, defaults, options);

		// The headers for our accordion instance
		var jjaccordion = $('#accordion' + options.id + ' .' + options.header);

		// Check we have an id to ensure we are triggering on the right element
		if (!options.id) 
		{
			throw "Must provide an id.";
		}

		// Ensure all elements are closed on page load
		jjaccordion.toggleClass(options.inactiveheader);

		// But open the first accordion pane if instructed to do so
		if (open)
		{
			jjaccordion.first().toggleClass(options.activeheader).toggleClass(options.inactiveheader);
			$('#accordion' + options.id + ' .' + options.content).first().slideDown(options.speed).toggleClass(options.opencontent);
		}

		// On clicking on an element open that pane
		jjaccordion.on('click', function () {
			var self = $(this);

			// If inactive open the pane and if currently active close the pane.
			if(self.hasClass(options.inactiveheader)) 
			{
				$('#accordion' + options.id + ' .' + options.activeheader).toggleClass(options.activeheader).toggleClass(options.inactiveheader).next().slideToggle(options.speed).toggleClass(options.opencontent);
				self.toggleClass(options.activeheader).toggleClass(options.inactiveheader);
				self.next().slideToggle(options.speed).toggleClass(options.opencontent);
			} else {
				self.toggleClass(options.activeheader).toggleClass(options.inactiveheader);
				self.next().slideToggle(options.speed).toggleClass(options.opencontent);
			}
		});

		return false;
	};
	
})(jQuery);
