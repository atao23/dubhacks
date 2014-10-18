var map;

window.onload = function(){
	var but = document.getElementById('clickButton');
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

//var object = [{"longitude":-122.350311,"latitude":47.658493},{"longitude":-122.3342613,"latitude":47.6088937},{"longitude":null,"latitude":null},{"longitude":-122.3424354,"latitude":47.6103582},{"longitude":null,"latitude":null},{"longitude":-122.268441,"latitude":47.661428},{"longitude":null,"latitude":null},{"longitude":-122.357127,"latitude":47.624851},{"longitude":null,"latitude":null},{"longitude":null,"latitude":null},{"longitude":-122.34187334197,"latitude":47.610441439483},{"longitude":null,"latitude":null},{"longitude":null,"latitude":null},{"longitude":null,"latitude":null},{"longitude":null,"latitude":null},{"longitude":-122.314078,"latitude":47.613063}];

function getToServer() {
	// object.forEach(function(data){
	// 	if(data.longitude){
	// 		drawCircle(data.latitude, data.longitude, Math.floor(Math.random() * 5) + 1);
	// 	}
	// });
	$.get( "sample.php", function( data ) {
		data = JSON.parse(data);
		console.log(data);
		data.forEach(function(object){
			if(object.longitude){
				drawCircle(object.longitude, object.latitude, object.rating || 3);
			}
		});
	});
}

google.maps.event.addDomListener(window, 'load', initialize);