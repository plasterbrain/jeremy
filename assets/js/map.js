/**
 * Google Maps: Member Directory
 *
 * @file Adds a Google Map to the page with markers for all the member addresses registered on the site.
 * @version 1.0.0
 * @since 1.0.0
 */

var marker = [];
var infowindow, content;
var latlng, business, link;

function convertLatLng( string ) {
	var latlngStr = string.split(",", 2);
	var lat = parseFloat(latlngStr[0]);
	var lng = parseFloat(latlngStr[1]);
	return new google.maps.LatLng(lat, lng);
}

function initMap() {
	var map = new google.maps.Map( document.getElementById( 'members__map' ), {
		disableDefaultUI: true,
		styles: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#173768"}]},{"featureType":"administrative.neighborhood","elementType":"labels.text.fill","stylers":[{"color":"#6c7086"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#faf1d1"}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#fcf7e4"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"color":"#f58c6d"}]},{"featureType":"poi","elementType":"labels.text.fill","stylers":[{"color":"#757575"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#f7ac8c"},{"weight":1}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#fba2a2"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"geometry.fill","stylers":[{"color":"#f8a09c"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#b4e0dd"}]},{"featureType":"water","elementType":"labels.text","stylers":[{"color":"#306b70"}]}]
	} );
	var infowindow = new google.maps.InfoWindow({});
	var bounds = new google.maps.LatLngBounds();

	for ( var i = 0; i < markers.length; i++ ) {
		latlng = markers[i].geocode[0];
		latlng = convertLatLng(latlng);
		business = markers[i].name;

		marker[i] = new google.maps.Marker( {
			map: map,
			label: '', //For whatever reason the markers don't show up without this.
			animation: google.maps.Animation.DROP,
			position: latlng,
			id: i,
		} );
		
		if ( markers.length === 1 ) {
		  map.setCenter( latlng );
		  map.setZoom( 15 );
		} else {
			bounds.extend( latlng );
			map.fitBounds( bounds );
		}

		link = markers[i].page;
		content = link ? '<a href="' + link + '">' + business + '</a>' : business;
		content = '<div class="members__map__marker">' + business + '</div>';
		google.maps.event.addListener( marker[i], 'click', ( function( marker, i, content ) {
			return function() {
				infowindow.setContent( content );
				infowindow.open( map, marker[i] );
			};
		} ) ( marker, i, content ) );
	}
}