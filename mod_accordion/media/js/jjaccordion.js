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
		var options = $.extend({}, defaults, options);

		if (!options.id) throw "Must provide a id.";

		$(document).ready(function($) {
			$('#accordion' + options.id + ' .' + options.header).toggleClass(options.inactiveheader);

			if (open)
			{
				$('#accordion' + options.id + ' .' + options.header).first().toggleClass(options.activeheader).toggleClass(options.inactiveheader);
				$('#accordion' + options.id + ' .' + options.content).first().slideDown(options.speed).toggleClass(options.opencontent);
			}

			$('#accordion' + options.id + ' .' + options.header).click(function () {
				var self = $(this);

				if(self.is('#accordion' + options.id + ' .' + options.inactiveheader)) {
					$('#accordion' + options.id + ' .' + options.activeheader).toggleClass(options.activeheader).toggleClass(options.inactiveheader).next().slideToggle(options.speed).toggleClass(options.opencontent);
					self.toggleClass(options.activeheader).toggleClass(options.inactiveheader);
					self.next().slideToggle(options.speed).toggleClass(options.opencontent);
				} else {
					self.toggleClass(options.activeheader).toggleClass(options.inactiveheader);
					self.next().slideToggle(options.speed).toggleClass(options.opencontent);
				}
			});

			return false;
		});
	};
})(jQuery);