(function ($) {
	"use strict";

	var Constructor = function (options) {
		this.init('typeahead', options, Constructor.defaults);
	};

	$.fn.editableutils.inherit(Constructor, $.fn.editabletypes.text);

	$.extend(Constructor.prototype, {
		render: function() {
			this.renderClear();
			this.setClass();
			this.setAttr('placeholder');
			var that = this;
			this.$input.typeahead($.extend(this.options.typeahead, {
				afterSelect: function (item) {
					that.options.typeahead.onSelect(item);
					that.$input.closest('form').submit();
				}
			}));

			if($.fn.editableform.engine === 'bs3') {
				if(this.$input.hasClass('input-sm')) {
					this.$input.siblings('input.tt-hint').addClass('input-sm');
				}
				if(this.$input.hasClass('input-lg')) {
					this.$input.siblings('input.tt-hint').addClass('input-lg');
				}
			}
		}
	});

	Constructor.defaults = $.extend({}, $.fn.editabletypes.list.defaults, {
		/**
		 @property tpl
		 @default <input type="text">
		 **/
		tpl:'<input class="form-control" style="margin-left: 15px;" type="text">',
		/**
		 Configuration of typeahead itself.
		 [Full list of options](https://github.com/twitter/typeahead.js#dataset).

		 @property typeahead
		 @type object
		 @default null
		 **/
		typeahead: {},
		/**
		 Whether to show `clear` button

		 @property clear
		 @type boolean
		 @default true
		 **/
		clear: true
	});

	$.fn.editabletypes.typeahead = Constructor;

}(window.jQuery));