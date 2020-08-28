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
  var map = new google.maps.Map(document.getElementById('directory-map'), {
    disableDefaultUI: true    
  });
  var infowindow = new google.maps.InfoWindow({});
  var bounds = new google.maps.LatLngBounds();

  for( var i = 0; i < markers.length; i++ ) {
    latlng = markers[i].geocode[0];
    latlng = convertLatLng(latlng);
    business = markers[i].name;

    marker[i] = new google.maps.Marker({
      map: map,
      label: '', //For whatever reason the markers don't show up without this. :smirk:
      animation: google.maps.Animation.DROP,
      position: latlng,
      id: i,
    });
    bounds.extend(latlng);
    map.fitBounds(bounds);

    link = markers[i].page;
    content = '<div class="map-infobox"><a href="' + link + '"><strong>' + business + '</strong></a></div>';
    google.maps.event.addListener(marker[i], 'click', (function(marker, i, content) {
      return function() {
          infowindow.setContent(content);
          infowindow.open(map, marker[i]);
      }
  })(marker, i, content))
  }
}