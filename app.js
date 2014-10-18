var map;
function initialize() {
	var mapOptions = {
	  center: { lat: 47.6097, lng: -122.3331},
	  zoom: 8
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

var test1 = function() {
	var populationOptions = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.0,
      fillColor: '#FF0000',
      fillOpacity: 0.35,
      map: map,
      center: {lat: 47.6097, lng: -122.3331},
      radius: 100 * 100
    };

    var populationOptions2 = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.0,
      fillColor: '#FF0000',
      fillOpacity: 0.35,
      map: map,
      center: {lat: 47.601, lng: -122.3331},
      radius: 100 * 100
    };

    cityCircle = new google.maps.Circle(populationOptions);
    cityCircle = new google.maps.Circle(populationOptions2);
}

google.maps.event.addDomListener(window, 'load', initialize);