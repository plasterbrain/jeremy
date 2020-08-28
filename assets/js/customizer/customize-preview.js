/**
 * File customizer.js.
 * Theme Customizer enhancements for a better user experience.
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function($) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-tagline' ).text( to );
		} );
	} );
	wp.customize('copyright', function(value) {
		value.bind( function( to ) {
			$( '.copyright' ).text( to );
		} );
	} );
	wp.customize('show_theme_credits', function(value) {
		value.bind( function(to) {
			$('.theme-credits').toggle();
		});
	} );
	wp.customize('show_powered_by', function(value) {
		value.bind( function(to) {
			$('.powered-by').toggle();
		});
	} );
	wp.customize('show_hero', function(value) {
		value.bind( function(to) {
			$( '.hero-home' ).slideToggle();
		});
	})
	wp.customize('hero_h1', function(value) {
		value.bind(function( to ) {
			console.log('test');
			$('.hero-home h1').text(to);
		});
	});
	wp.customize('hero_h2', function(value) {
		value.bind(function(to) {
			$('.hero-home h2').text(to);
		});
	});
	wp.customize('hero_text_color', function(value) {
		value.bind(function(to) {
			$('.hero-home h1').css('color', to);
			$('.hero-home h2').css('color', to);
		});
	})
	wp.customize('hero_button_text', function(value) {
		value.bind(function(to) {
			$('.hero-home .hero-button').text(to);
		});
	})
	wp.customize('hero_button_color', function(value) {
		value.bind(function(to) {
			$('.hero-home .hero-button').css('background', to);
		});
	})
	wp.customize('hero_button_text_color', function(value) {
		value.bind(function(to) {
			$('.hero-home .hero-button').css('color', to);
		});
	})
	wp.customize( 'bread-index', function( value ) {
		value.bind( function( to ) {
			$( '.bread-index' ).text( to );
			if ( '' !== to ) {
				$( '.bread-sep' ).text( ' > ' );
			} else {
				$( '.bread-sep' ).text( '' ); 
			}
		} );
	} );
	wp.customize( 'post-nav-toggle', function( value ) {
		value.bind( function( to ) {
			if ( to ) {
				$( '.post-navigation' ).show();
			} else {
				$( '.post-navigation' ).hide();
			}
		} );
	} );
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-tagline' ).css( {
					'display': 'none',
				} );
			} else {
				$( '.site-title, .site-tagline' ).css( {
					'display': 'block',
					'color': to,
				} );
			}
		} );
	} );
})(jQuery);