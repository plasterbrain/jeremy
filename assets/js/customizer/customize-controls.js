( function($) {
	wp.customize.bind( 'ready', function() {
		var toggleControls = [
			'hero_bg_img',
			'hero_bg_parallax',
			'hero_h1',
			'hero_h2',
			'hero_button_text',
			'hero_button_link',
			'hero_bg_color',
			'hero_text_color',
			'hero_button_color',
			'hero_button_text_color',
		];
		wp.customize.instance( 'show_hero', function( value ) {
			if ( value.get() == false ) {
				$.each( toggleControls, function(index, control_name) {
					$('#customize-control-' + control_name).hide();
				});
			}
			value.bind(function(to) {
				$.each(toggleControls, function( index, control_name) {
					$('#customize-control-' + control_name).slideToggle();
				});
			});
		});
	} );
})(jQuery);