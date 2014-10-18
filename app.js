var map;

window.onload = function(){
	var but = document.getElementById('clickButton');
	but.onclick = function(){
		console.log('working!!!');
	}	
}
function initialize() {
	var mapOptions = {
	  center: { lat: 47.6097, lng: -122.3331},
	  zoom: 13
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

function drawCircle(lat, long, rating){
	var populationOptions = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.0,
      fillColor: '#FF0000',
      fillOpacity: 0.1 * rating,
      map: map,
      center: {lat: lat, lng: long},
      radius:  38
    };

    cityCircle = new google.maps.Circle(populationOptions);
};

google.maps.event.addDomListener(window, 'load', initialize);