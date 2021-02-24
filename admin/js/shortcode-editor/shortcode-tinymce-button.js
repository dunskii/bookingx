(function () {

	tinymce.PluginManager.add(
		'pushortcodes',
		function (editor) {
			var shortcodeValues = [];
			var shortcodes_obj  = shortcodes_button;

			for (key in shortcodes_button) {
				shortcodeValues.push( {text: shortcodes_button[key], value: key} );
			}

			editor.addButton(
				'pushortcodes',
				{
					title: 'Bookingx',
					image: bkx_public_url + '/images/dashicons-calendar.png',
					onclick: function () {
						editor.windowManager.open(
							{
								title: 'Bookingx Shortcodes',
								body: [
								{
									type: 'listbox',
									name: 'Bookingx',
									values: shortcodeValues,
									onselect: function (e) {
										$selected_id = e.control._id;
										var v        = e.control.settings.value;
										// bkx_booking_page_bkx-setting
										if (bkx_shortcode_obj.location == 'post') {
											tinyMCE.activeEditor.selection.setContent( '[' + v + ']' );
										} else if (bkx_shortcode_obj.location == 'bkx_booking_page_bkx-setting') {
											tinyMCE.activeEditor.selection.setContent( '{' + v + '}' );
										}

									},
								}
								],
								onsubmit: function (e) {

								}
							}
						)
					}
				}
			);

		}
	);
})();
