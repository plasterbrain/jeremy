/**
 * BuddyPress Ajax
 *
 * @file A slimmed-down version of the BuddyPress Legacy buddypress.js to only include member directory Ajax and to match our site structure.
 * @version 2.0.0
 * @since 1.0.0
 */
var jq = jQuery;

// Global variable to prevent multiple AJAX requests
var bp_ajax_request = null;

jq( function() {
	bp_init_objects( 'members' );
	jq( '#members' ).on( 'click', function(event) {
		var target = jq( event.target ),
		search_terms, 
		pagination_id = jq( target ).closest( '.nav-pagination' ).attr( 'id' ),
		template,
		page_number,
		caller;

		if ( target.hasClass( 'button' ) ) {
			return true;
		}
		
		if ( pagination_id ) {
			event.preventDefault();
			if ( target.hasClass( 'current' ) || target.hasClass( 'nav-pagination__disabled' ) || ! target.is( 'a,img,svg' ) ) {
				return false;
			}
			search_terms = false;
			template = null;
			
			// Page number
			if ( jq( target ).hasClass( 'nav-pagination__next' ) || jq( target ).hasClass( 'nav-pagination__prev' ) ) {
				page_number = jq( '.nav-pagination span.current' ).html();
			} else {
				page_number = jq( target ).html();
			}

			// Remove any non-numeric characters from page number text
			page_number = Number( page_number.replace(/\D/g,'') );
			
			if ( jq( target ).hasClass( 'nav-pagination__next' ) ) {
				page_number++;
			} else if ( jq( target ).hasClass( 'nav-pagination__prev' ) ) {
				page_number--;
			}

			if ( pagination_id.indexOf( '#members-pages-bot' ) !== -1 ) {
				caller = 'pag-bottom';
			} else {
				caller = null;
			}
			
			bp_filter_request( 'members', jq.cookie( 'bp-member-filter' ), jq.cookie('bp-member-scope'), 'div.members', search_terms, page_number, jq.cookie('bp-member-extras'), caller, template );

			return false;
		}
	});
});

/* Setup object scope and filter based on the current cookie settings for the object. */
function bp_init_objects(objects) {
	jq(objects).each( function(i) {
		if ( undefined !== jq.cookie('bp-' + objects[i] + '-filter') && jq('#' + objects[i] + '-order-select select').length ) {
			jq('#' + objects[i] + '-order-select select option[value="' + jq.cookie('bp-' + objects[i] + '-filter') + '"]').prop( 'selected', true );
		}

		if ( undefined !== jq.cookie('bp-' + objects[i] + '-scope') && jq('div.' + objects[i]).length ) {
			jq('.item-list-tabs li').each( function() {
				jq(this).removeClass('selected');
			});
			jq('#' + objects[i] + '-' + jq.cookie('bp-' + objects[i] + '-scope') + ', #object-nav li.current').addClass('selected');
		}
	});
}

/* Filter the current content list (groups/members/blogs/topics) */
function bp_filter_request( object, filter, scope, target, search_terms, page, extras, caller, template ) {
	if ( null === scope ) {
		scope = 'all';
	}

	/* Save the settings we want to remain persistent to a cookie */
	jq.cookie( 'bp-' + object + '-scope', scope, {
		path: '/',
		secure: ( 'https:' === window.location.protocol )
	} );
	jq.cookie( 'bp-' + object + '-filter', filter, {
		path: '/',
		secure: ( 'https:' === window.location.protocol )
	} );
	jq.cookie( 'bp-' + object + '-extras', extras, {
		path: '/',
		secure: ( 'https:' === window.location.protocol )
	} );

	if ( bp_ajax_request ) {
		bp_ajax_request.abort();
	}

	bp_ajax_request = jq.post( ajaxurl, {
		action: object + '_filter',
		'cookie': bp_get_cookies(),
		'object': object,
		'filter': filter,
		'search_terms': search_terms,
		'scope': scope,
		'page': page,
		'extras': extras,
		'template': template
	},
	function( response ) {
		/* animate to top if called from bottom pagination */
		if ( caller === 'pag-bottom' && jq('#members-pages-top').length ) {
			var top = jq('#members-pages-top');
			jq('html,body').animate({scrollTop: top.offset().top}, 'slow', function() {
				jq(target).fadeOut( 100, function() {
					jq(this).html(response);
					jq(this).fadeIn(100);
				});
			});

		} else {
			jq(target).fadeOut( 100, function() {
				jq(this).html(response);
				jq(this).fadeIn(100);
			});
		}
	});
}

/* Returns a querystring of BP cookies (cookies beginning with 'bp-') */
function bp_get_cookies() {
	var allCookies = document.cookie.split(';'),  // get all cookies and split into an array
		bpCookies      = {},
		cookiePrefix   = 'bp-',
		i, cookie, delimiter, name, value;

	// loop through cookies
	for (i = 0; i < allCookies.length; i++) {
		cookie    = allCookies[i];
		delimiter = cookie.indexOf('=');
		name      = jq.trim( unescape( cookie.slice(0, delimiter) ) );
		value     = unescape( cookie.slice(delimiter + 1) );

		// if BP cookie, store it
		if ( name.indexOf(cookiePrefix) === 0 ) {
			bpCookies[name] = value;
		}
	}

	// returns BP cookies as querystring
	return encodeURIComponent( jq.param(bpCookies) );
}