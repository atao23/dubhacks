var map;
//var searchLocation;
//var searchTerm;

window.onload = function(){
	var but = document.getElementById('clickButton');
	searchLocation = document.getElementById('location');
	searchTerm = document.getElementById('term');
	but.onclick = function(){
		getToServer();
	}	
}
function initialize() {
	var mapOptions = {
	  center: { lat: 47.6097, lng: -122.3331},
	  zoom: 13
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

function drawCircle(lat, long, rating) {
 	var color;
	if (rating < 2) {
		color = '#cc8b1f';
	} else if (rating < 3) {
		color = '#dcb228';
	} else if (rating < 4) {
		color = '#f0991e';
	} else if (rating < 5) {
		color = '#f26a2c';
	} else {
		color = '#dd050b';
	}
	var populationOptions = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.0,
      fillColor: color,
      fillOpacity: 0.5,
      map: map,
      center: {lat: lat, lng: long},
      radius:  38 //CS
    };

    cityCircle = new google.maps.Circle(populationOptions);
};

function getToServer() {
	$.get( "sample.php", function( data ) {
		//searchLocation.value = "";
		//searchTerm.value = "";
		data = JSON.parse(data);
		console.log(data);
		data.forEach(function(object){
			if(object.longitude){
				drawCircle(object.latitude, object.longitude, object.rating || 3);
			}
		});
	});
}

google.maps.event.addDomListener(window, 'load', initialize);
