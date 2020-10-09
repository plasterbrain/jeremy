/* global jeremy, jQuery */
/**
 * Responsive Navigation.
 *
 * @file Makes the main navigation more accessible and responsive.
 * @version 2.0.0
 * @since 1.0.0
 *
 * Changelog:
 * 2.0.0
 *  - Skip link focus fix removed as per https://axesslab.com/skip-links/
 *  - Hamburger image no longer hardcoded and has a localized alt attribute.
 *  - New dropdown button for directions/add to calendar feature
 *  - Main menu now has proper aria-expanded attribute.
 */
/* eslint-disable no-alert, vars-on-top */

var hasopened = 'hasopened'; // Name of the "my child is expanded" class

function toggleParent( parent ) {
	parent.toggleClass( 'hasopened' );
	parent.attr( 'aria-expanded', function(index, attr) {
		return attr === 'true' ? 'false' : 'true';
	} );
}

function toggleSub( submenu ) {
	submenu.toggleClass( 'isopen' );
	submenu.find('ul').attr( 'aria-hidden', function(index, attr) {
		return attr === 'true' ? 'false' : 'true';
	} );
}

( function( $ ) {
	
	// ==== Main Menu ==== //
	var mainNav = $( '#site-navigation' );
	var openedSub, openedParent;
  
  $( '#nav-main__toggle' ).click( function( i ) {
    toggleParent( $( this ) );
    toggleSub( $( '#nav-main' ) );
  } );
  
	$( '.nav__item button' ).each( function( i ) {
		var id = i + 1;
		
		$( '#parent-menu-' + id ).click( function() {
			if ( $( this ).is( openedParent ) ) {
				// Clicking on parent to close its submenu
				
				toggleParent( $( this ) );
				//$( this ).removeClass( hasopened );
				mainNav.removeClass( hasopened );
				toggleSub( openedSub );
				
				// Currently opened parent/submenu is now none
				openedParent = null;
				openedSub = null;
			} else {
				// This parent is not yet opened.
				toggleParent( $( this ) );
				if ( mainNav.hasClass( hasopened ) ) {
					console.log("Closing another menu.");
					// Another submenu is open! Let's close it!
					toggleParent( openedParent );
					toggleSub( openedSub );
				} else {
					// This is the first time a submenu is being opened.
					mainNav.addClass( hasopened );
				}
				
				// Set our currently opened parent/submenu
				openedSub = $( '#sub-menu-' + id );
				openedParent = $( this );
				toggleSub( openedSub );
			}
		} );
	} );
	
	// ==== Dropdown Buttons (e.g. "Add to Calendar") ==== //
	
	$( 'button.button-dropdown' ).each( function() {
		var parent = $( this ).closest( '.dropdown__container' );
		var childNav = parent.find( '.nav-dropdown' ).first();
		$( this ).click( function() {
			childNav.toggle();
			$( this ).attr( 'aria-expanded', function(index, attr) {
				return attr === 'true' ? 'false' : 'true';
			} );
		} );
	} );
	// Dismiss when clicking outside dropdown(s)
	// @TODO test with multiple dropdowns on page
	$( document ).click( function( e ) {
		e.stopPropagation();
		if ( $( '.dropdown__container' ).has( e.target ).length === 0 ) {
			$( '.nav-dropdown' ).hide();
			$( 'button.button-dropdown' ).attr( 'aria-expanded', 'false' );
		}
	} );
}( jQuery ) );