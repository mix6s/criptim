(function (factory) {
	/* global define */
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	} else if (typeof module === 'object' && module.exports) {
		// Node/CommonJS
		module.exports = factory(require('jquery'));
	} else {
		// Browser globals
		factory(window.jQuery);
	}
}(function ($) {

	var FileDialog = function (context) {
		var self = this;
		var ui = $.summernote.ui;

		var $editor = context.layoutInfo.editor;
		var options = context.options;
		var lang = options.langInfo;

		this.initialize = function () {
			var $container = options.dialogsInBody ? $(document.body) : $editor;


			var body = '<div class="form-group note-group-select-from-files">' +
				'<label>' + lang.image.selectFromFiles + '</label>' +
				'<input class="note-file-input form-control" type="file" name="files" multiple="multiple" />' +
				'</div>';

			var footer = '<button href="#" class="btn btn-primary note-file-btn disabled" disabled>' + lang.file.insert + '</button>';

			this.$dialog = ui.dialog({
				title: lang.file.insert,
				fade: options.dialogsFade,
				body: body,
				footer: footer
			}).render().appendTo($container);
		};

		this.destroy = function () {
			ui.hideDialog(this.$dialog);
			this.$dialog.remove();
		};

		this.bindEnterKey = function ($input, $btn) {
			$input.on('keypress', function (event) {
				if (event.keyCode === key.code.ENTER) {
					$btn.trigger('click');
				}
			});
		};

		this.show = function () {
			context.invoke('editor.saveRange');
			this.showFileDialog().then(function (data) {
				// [workaround] hide dialog before restore range for IE range focus
				ui.hideDialog(self.$dialog);
				context.invoke('editor.restoreRange');
				context.triggerEvent('file.upload', data);
			}).fail(function () {
				context.invoke('editor.restoreRange');
			});
		};

		/**
		 * show image dialog
		 *
		 * @param {jQuery} $dialog
		 * @return {Promise}
		 */
		this.showFileDialog = function () {
			return $.Deferred(function (deferred) {
				var $fileInput = self.$dialog.find('.note-file-input'),
					$fileBtn = self.$dialog.find('.note-file-btn');

				ui.onDialogShown(self.$dialog, function () {
					context.triggerEvent('dialog.shown');

					// Cloning imageInput to clear element.
					$fileInput.replaceWith($fileInput.clone()
						.on('change', function () {
							deferred.resolve(this.files || this.value);
						})
						.val('')
					);

					$fileBtn.click(function (event) {
						event.preventDefault();

						deferred.resolve($imageUrl.val());
					});
				});

				ui.onDialogHidden(self.$dialog, function () {
					$fileInput.off('change');
					$fileBtn.off('click');

					if (deferred.state() === 'pending') {
						deferred.reject();
					}
				});

				ui.showDialog(self.$dialog);
			});
		};
	};

	$.extend(true, $.summernote.lang, {
		'en-US': {
			file: {
				file: 'File',
				insert: 'Insert file',
				selectFromFiles: 'Browse',
			}
		},
		'ru-RU': {
			file: {
				file: 'Файл',
				insert: 'Вставить файл',
				selectFromFiles: 'Выбрать',
			}
		},
	});

	$.extend($.summernote.options.modules, {
		'fileDialog': FileDialog,
	});

	$.extend($.summernote.plugins, {
		/**
		 * @param {Object} context - context object has status of editor.
		 */
		'file': function (context) {
			var self = this;

			// ui has renders to build ui elements.
			//  - you can create a button with `ui.button`
			var ui = $.summernote.ui;
			var $editor = context.layoutInfo.editor;
			var options = context.options;
			var lang = options.langInfo;

			context.memo('button.file', function () {
				var button = ui.button({
					contents: '<i class="fa fa-file-text-o"/> ' + lang.file.file,
					tooltip: lang.file.file,
					click: context.createInvokeHandler('fileDialog.show')
				});
				return button.render();
			});
		}
	});
}));