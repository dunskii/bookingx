(function() {
      
    tinymce.PluginManager.add('pushortcodes', function( editor )
    {
        var shortcodeValues = [];
        var shortcodes_obj = shortcodes_button;

        for (key in shortcodes_button) {
            shortcodeValues.push({text: shortcodes_button[key], value:key});
        }
         
        editor.addButton( 'pushortcodes', {
        title: 'Bookingx',
        image: '../wp-content/plugins/bookingx/images/dashicons-calendar.png',
        onclick: function() {
            editor.windowManager.open({
                title: 'Bookingx Shortcodes',
                body: [
                    {
                        type: 'listbox',
                        name: 'Bookingx',                    
                        values: shortcodeValues,
                        onselect: function(e) {
                            $selected_id = e.control._id;
                            var v = e.control.settings.value;
                            tinyMCE.activeEditor.selection.setContent( '[' + v + '][/' + v + ']' );
                        },
                    }
                ],
                onsubmit: function(e){

                }
            })
        }
    });
         
    });
})();


