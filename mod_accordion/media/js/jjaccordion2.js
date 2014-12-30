// the semi-colon before function invocation is a safety net against concatenated
// scripts and/or other plugins which may not be closed properly.
;(function ( $, window, document, undefined ) {

    // undefined is used here as the undefined global variable in ECMAScript 3 is
    // mutable (ie. it can be changed by someone else). undefined isn't really being
    // passed in so we can ensure the value of it is truly undefined. In ES5, undefined
    // can no longer be modified.

    // window and document are passed through as local variable rather than global
    // as this (slightly) quickens the resolution process and can be more efficiently
    // minified (especially when both are regularly referenced in your plugin).

    // Create the defaults once
    var pluginName = "jjaccordion",
        defaults = {
            header: 'jjaccordion-header',
            activeheader: 'active-header',
            inactiveheader: 'inactive-header',
            content: 'jjaccordion-content',
            opencontent: 'open-content',
            open: true,
            speed: 'fast'
        };

    // The actual plugin constructor
    function Plugin ( element, options ) {
        this.element = element;
        // jQuery has an extend method which merges the contents of two or
        // more objects, storing the result in the first object. The first object
        // is generally empty as we don't want to alter the default options for
        // future instances of the plugin
        this.settings = $.extend( {}, defaults, options );
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    // Avoid Plugin.prototype conflicts
    $.extend(Plugin.prototype, {
        init: function () {
            // The headers for our accordion instance
            var jjaccordion = $(this.element).find('.' + this.settings.header);

            // Ensure all elements are closed on page load
            jjaccordion.toggleClass(this.settings.inactiveheader);

            // But open the first accordion pane if instructed to do so
            if (open) {
                jjaccordion.first().toggleClass(this.settings.activeheader).toggleClass(this.settings.inactiveheader);
                $(this.element).find('.' + this.settings.content).first().slideDown(this.settings.speed).toggleClass(this.settings.opencontent);
            }

            jjaccordion.on('click', {
                parent: $(this.element),
                options: this.settings
            }, dealWithClick);
        },
        dealWithClick: function (event) {
            var self = $(this),
                parentElement = event.data.parent,
                options = event.data.options;

            // If inactive open the pane and if currently active close the pane.
            if (self.hasClass(options.inactiveheader)) {
                parentElement.find('.' + options.activeheader).toggleClass(options.activeheader).toggleClass(options.inactiveheader).next().slideToggle(options.speed).toggleClass(options.opencontent);
                self.toggleClass(options.activeheader).toggleClass(options.inactiveheader);
                self.next().slideToggle(options.speed).toggleClass(options.opencontent);
            } else {
                self.toggleClass(options.activeheader).toggleClass(options.inactiveheader);
                self.next().slideToggle(options.speed).toggleClass(options.opencontent);
            }
        }
    });

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[ pluginName ] = function ( options ) {
        this.each(function() {
            if ( !$.data( this, "plugin_" + pluginName ) ) {
                $.data( this, "plugin_" + pluginName, new Plugin( this, options ) );
            }
        });

        // chain jQuery functions
        return this;
    };

})( jQuery, window, document );