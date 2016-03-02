// Google Map API Standard Code
var map;
var geocoder;

var region = 'us';

var mc;
var mcOptions;

var markers = new Array();

var distancenode = '';
var distancecode = 1;

var themiles = '';
var thekm = '';
var arr = new Array();
var totalrec = 0;

////////////////////////// default to km/mi /////////////////////

var current_unit = 'km';
if(default_distance=='mi'){
current_unit = 'miles';
} else {
current_unit = 'km';
}

var km2mile = 0.621371;
var mile2km = 1.60934;


cachesearch = '';

/////////////////////////////////////////////////////////////////

$(document).ready(function(){

	$('p.distance-units label input:radio').click(function(){
      var units = $(this).parents('label').attr('units');
	  
	  ///////////////////////////////// store globally //////////////////
	    current_unit = units;
		//////////////////////////////////////////////////////////////////

		if($(this).parents('label').hasClass('unchecked')){
	
		 	 changeDistanceUnits(units);
		}
	  
	  
    });
	
	if(default_distance=='mi'){
		$('.radius-distance').html('mi');
	} else {
		$('.radius-distance').html('km');
	}
	

	if($('#map_canvas').length) {
//Inserite coordinate Fiumicino
		var lat = 41.76967;
		var lng = 12.2270;

		// instantiate geocoder
		geocoder = new google.maps.Geocoder();
		// create new latitude / longitude object
		var latlng = new google.maps.LatLng(lat,lng);

		// set map options
		var myOptions = {
			zoom: 10,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		// display map
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		// show the map div
		$('#map_canvas').show();
	}
	
	//autocomplete text field
	
	 var autocomplete = new google.maps.places.Autocomplete($("#address")[0], {});
	 var origin_autocomplete = new google.maps.places.Autocomplete($("#origin-direction")[0], {});


	if($('#clinic-finder-form').length) {

		$('#clinic-finder-form').submit(function(){

			var address = $('#address').val();
			var distance = $('input[name=distance]:radio:checked').val();

			// search for available locations
			if(address != '' && address!=cachesearch) {
				gmap_location_lookup(address, distance, region);
				cachesearch = address;
			} else {
				$('#address').focus();
			}
			
		return false;
		});
	}
	
	/////
	
	$('#edit-products').change(function() {
		cachesearch = '';
	});
	

	////

	if($('#add #map_canvas').length) {

	//Inserite coordinate Fiumicino
		var lat = 41.76967;
		var lng = 12.2270;

		
		// get pre-populated value and focus map
		var gmap_marker = false;
		if($('#latitude').length) {
		
			val = $('#latitude').val()*1;
			
			if(val != '' && !isNaN(val)) {
				lat = val;
				gmap_marker = true;
			}
			
		}
		if($('#longitude').length) {
		
			val = $('#longitude').val()*1;
			
			if(val != '' && !isNaN(val)) {
				lng = val;
			}
			
		}

	
		geocoder = new google.maps.Geocoder();

		var latlng = new google.maps.LatLng(lat,lng);
		
		// set map options
		var myOptions = {
			zoom: 10,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		// display map
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

		if(gmap_marker) {
			// create a map marker
			var marker = new google.maps.Marker({
				map: map,
				position: latlng
			});
		}
	}


	if($('#add #address').length) {

		$('#add #address').blur(function(){

			var address = $(this).val();

			if(address != '') {

				get_coordinate(address,region);
			}
		});
	}
	
	

	if(geo_settings==1){
		gmap_location_lookup(geoip_city()+', '+geoip_country_name(),'100',''); 	
	} else {
		gmap_location_lookup(default_location,'100',''); 	
	}  
	
	
});


/**
 * Retreiving location / address
 */
function get_coordinate(address, region) {

	if(region==null || region == '' || region == 'undefined') {
		region = 'us';
	}

	if(address != '') {
		$('#ajax_msg').html('<p>Loading location</p>');

		geocoder.geocode( {'address':address,'region':region}, function(results, status) {
		
			if(status == google.maps.GeocoderStatus.OK) {
				$('#ajax_msg').html('<p></p>');
			
				$('#latitude').val( results[0].geometry.location.lat() );
				$('#longitude').val( results[0].geometry.location.lng() );


				map.setZoom(10);

				map.setCenter(results[0].geometry.location);

				var marker = new google.maps.Marker({
					map: map,
					position: results[0].geometry.location
				});
			} else {
				
				$('#ajax_msg').html('<p>Google map geocoder failed: '+status+'</p>');
			}
		});
	}
}


/**
 * Google map location lookup
 */
function gmap_location_lookup(address,distance,region) {

	if(region==null || region == '') {
		region = 'us';
	}
	
	distancecode = 1;
	
	if(address != '') {
		
		$('#map_canvas').html("<img src='./img/ajax-loader.gif' alt='Ajax Loading Image' />").show();
		$('#ajax_msg').hide();
	
		geocoder = new google.maps.Geocoder();
	
		geocoder.geocode( {'address':address,'region':region}, function(results, status) {
		
			if(status == google.maps.GeocoderStatus.OK) {
			
				var lat = results[0].geometry.location.lat();
				var lng = results[0].geometry.location.lng();
				var location = results[0].geometry.location;

				
				var myOptions = {
					zoom: parseInt(init_zoom),
					center: location,
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				
				map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

				
				var marker = new google.maps.Marker({
					map: map,
					draggable:false,
					animation: google.maps.Animation.DROP,
					position: results[0].geometry.location,
					title:'Your entered address'
				});
				
				 var image = new google.maps.MarkerImage(
					'img/image.png',
					new google.maps.Size(34,46),
					new google.maps.Point(0,0),
					new google.maps.Point(17,46)
				 );
				 
				//patch 2.6 custom map code
				if(ssf_map_code!="" && ssf_map_code!=undefined)
				{
				  map.setOptions({styles: ssf_map_code});	

				} else {

				if(style_map_color!=""){
					  var styles = [
					  {
						stylers: [
						  { hue: style_map_color },
						  { saturation: 0 },
						  { lightness: 50 },
						  { gamma: 1 },
						]
					  }
					];

					map.setOptions({styles: styles});
					}
				}
				
				  var shadow = new google.maps.MarkerImage(
					'img/shadow.png',
					new google.maps.Size(60,46),
					new google.maps.Point(0,0),
					new google.maps.Point(17,46)
				  );

				  var shape = {
					coord: [31,0,32,1,33,2,33,3,33,4,33,5,33,6,33,7,33,8,33,9,33,10,33,11,33,12,33,13,33,14,33,15,33,16,33,17,33,18,33,19,33,20,33,21,33,22,33,23,33,24,33,25,33,26,33,27,33,28,33,29,33,30,33,31,33,32,32,33,31,34,29,35,26,36,25,37,25,38,24,39,23,40,23,41,22,42,22,43,21,44,20,45,16,45,15,44,14,43,14,42,13,41,13,40,12,39,12,38,11,37,10,36,6,35,4,34,3,33,2,32,1,31,1,30,0,29,0,28,0,27,0,26,0,25,0,24,0,23,0,22,0,21,0,20,0,19,0,18,0,17,0,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,0,6,0,5,1,4,1,3,2,2,3,1,4,0,31,0],
					type: 'poly'
				  };
				
				  
				// clear all markers
				jQuery.each(markers,function(k,v){
					v.setMap(null);
				});
				
				markers = [];
				
				// clear list
				$('ol#list').empty();
				
				var number = 0;
				
				///////////////
				if(current_unit=='km'){
				  distanceradio = distance;
				  distance = (distance/mile2km)-(distanceradio/4.5); 
				}
				//////////////

				$.ajax({
					type:"POST",
					url:$('#clinic-finder-form').attr('action'),
					data:"ajax=1&action=get_nearby_stores&distance="+distance+"&lat="+lat+"&lng="+lng+"&products="+$('#edit-products').val(),
					success:function(msg) {
						

						var results = JSON.parse(msg);
						if (results.hasOwnProperty('stores')) {
							totalrec = results.stores.length;
						}
					
						if( results.success ) {
						
							
							var infowindow = new google.maps.InfoWindow({
								maxWidth: "400",
								content: ''
							});
								
							$i = 0;
							jQuery.each( results.stores,function(k,v){
								var marker = new google.maps.Marker({
									__gm_id: $i++,
									map: map,
									position: new google.maps.LatLng(v.lat,v.lng),
									icon: image,
									shadow: shadow,
									shape: shape,
									title: v.name+' : '+v.address
								});
								
								// calc distance
								origin = new google.maps.LatLng(lat, lng);
								dest = new google.maps.LatLng(v.lat,v.lng);
							// set km / miles language	
							themiles = v.titlemiles;
							thekm = v.titlekm;
							
							/////////////////////
							default_unit_system = google.maps.UnitSystem.METRIC;
							if(current_unit=="km"){
							default_unit_system = google.maps.UnitSystem.METRIC;
							} else if(current_unit=="miles"){
							default_unit_system = google.maps.UnitSystem.IMPERIAL;
							}
							///////////////////
							
								var service = new google.maps.DistanceMatrixService();
								service.getDistanceMatrix(
								  {
									origins: [origin],
									destinations: [dest],
									travelMode: google.maps.TravelMode.DRIVING,
									unitSystem: google.maps.UnitSystem.METRIC,
									avoidHighways: false,
									avoidTolls: false
								  }, callback);
					  
								// add to markers
								markers.push(marker);
								
								

								// build content string
								var info_window_string = info_window_content(v);
								ctype='';
								if(v.cat_img != '') {
									var ctype = '<img src="'+v.cat_img+'" style="max-width:15px; max-height:15px;" />';
								}
								
								number++
								//distancenode = google.maps.geometry.spherical.computeDistanceBetween(origin, dest).toFixed(2);
								if(number>9){
								$("<li id='l_"+marker['__gm_id']+"' class='clinic_list double-digit' />")
									.html("<span class='number'>"+number+"</span><div><strong>"+v.name+"</strong><br /><span>"+v.address+"</span><div id=d_"+number+" class='distance'><span id='disval' attr-dist='"+distancenode+"' class='value'>"+distancenode+"</span> <span class='units'></span> <span class='time2'></span></div></div><div class='products'>"+ctype+"</div>")
									.click(function(){
										//displayPoint(marker, i);
										infowindow.setContent(info_window_string);
										infowindow.open(map,marker);
										toggleBounce(marker);
									})
									.appendTo("#list");
								} else {
								$("<li id='l_"+marker['__gm_id']+"' class='clinic_list' />")
									.html("<span class='number'>"+number+"</span><div><strong>"+v.name+"</strong><br /><span>"+v.address+"</span><div id=d_"+number+" class='distance'><span id='disval' attr-dist='"+distancenode+"' class='value'>"+distancenode+"</span> <span class='units'></span> <span class='time2'></span></div></div><div class='products'>"+ctype+"</div><a id='la_"+marker['__gm_id']+"' name='la_"+marker['__gm_id']+"'></a>")
									.click(function(){
										//displayPoint(marker, i);
										infowindow.setContent(info_window_string);
										infowindow.open(map,marker);
										toggleBounce(marker);
										
										
									})
									.appendTo("#list");
								}

								// sort distance
								
								
								// attach popup to click event
								google.maps.event.addListener(marker, 'click', function() {
								    $('#list .clinic_list').removeClass('active');
									
									$('#l_'+marker['__gm_id']).addClass('active');
									
									$('#list').animate({ scrollTop: $('#list').scrollTop() + $('#list li.active').offset().top - $('#list').offset().top }, { duration: 'slow', easing: 'swing'});
									$('html,body').animate({ scrollTop: $('#list').offset().top - $(window).height() + $('#list li.active').height() }, { duration: 1000, easing: 'swing'});
									
									
									infowindow.setContent(info_window_string);
									infowindow.open(map,marker);
									toggleBounce(marker);
								});
							
							} ); // end loop
							
							// Marker Clusterer
							var mcOptions = {minimumClusterSize : 2};
                            mc = new MarkerClusterer(map, markers, mcOptions);
							
							$('.clinic_list').click(function(){
							    $('html, body').animate({scrollTop:100}, 'normal');
								$('#list .clinic_list').removeClass('active');
								$(this).addClass('active');
								$('div[title="Exit Street View"]').trigger('click');
								map.setZoom(parseInt(init_zoom));
								
							 }); 
				
							$('#ajax_msg').html("<p class='flash_good'>"+results.stores.length+" stores have been found</p>").fadeIn();
						} else {
						
							
							$("<li  />")
									.html("<span class='number'>!</span><p>"+results.msg+"</p>")
									.click(function(){
										//displayPoint(marker, i);
										infowindow.setContent(info_window_string);
										infowindow.open(map,marker);
										toggleBounce(marker);
									})
									.appendTo("#list");
							$('#ajax_msg').html("<p class='alert alert-block alert-error fade in'>"+results.msg+"</p>").fadeIn();
						}
						
						//sort_distance();
					}
				});
			}
		});
	}
}

// sort distance

function sort_distance(){

// r & d
    var numbersort = 1;
	var items = $('#list li').get();
	
	items.sort(function(a,b){ 
	  
	  
	 /*  Sort Alphabetical order 
	  
	  var keyA = $(a).find('strong').text();
	  var keyB = $(b).find('strong').text();
	  console.log($(a).find('strong'));
	  
	   if (keyA < keyB) { return -1; }
	   if (keyA > keyB) { return 1; }
	   
	 */

	 /* Sort by distance  */
	 
	  var keyA = $(a).find('#disval').text();
	  var keyB = $(b).find('#disval').text();
	  //console.log($(a).find('#disval').text());
	  
	   
	  if ((Math.round(parseFloat(keyA)*100)/100) < (Math.round(parseFloat(keyB)*100)/100)) { return -1; }
	  if ((Math.round(parseFloat(keyA)*100)/100) > (Math.round(parseFloat(keyB)*100)/100)) { return 1; }
	  
	  return 0;
	});
	
	
	var ol = $('#list');
	$.each(items, function(i, li){
	  ol.append(li);
	  $('#'+$(li).attr('id')+' .number').text(numbersort);
	  numbersort++;
	});


}

// bouncing marker

function toggleBounce(marker) {

	$(markers).each(function(i,marker2){
	
	 if(marker['__gm_id']!=marker2['__gm_id']){
	  marker2.setAnimation(null);
	  }

	});
	
	

	if (marker.getAnimation() != null) {
	  marker.setAnimation(null);
	} else {
	  marker.setAnimation(google.maps.Animation.BOUNCE);
	}
	
	var $allVideos = $("iframe[src^='http']");

					$allVideos.each(function() {

					$(this)
						.data('aspectRatio', this.height / this.width)
						
						// and remove the hard coded width/height
						.removeAttr('height')
						.removeAttr('width');

					});
				
					var newWidth = "220px";
				
					$allVideos.each(function() {

						var $el = $(this);
						$el
							.width(newWidth)
							.height(newWidth);

					});
}

//end function
	
// distance
							
/*			
// Old callback inconsistency in Firefox			
function callback(response, status) {
  if (status == google.maps.DistanceMatrixStatus.OK) {
    var origins = response.originAddresses;
    var destinations = response.destinationAddresses;

    for (var i = 0; i < origins.length; i++) {
      var results = response.rows[i].elements;
      for (var j = 0; j < results.length; j++) {
        var element = results[j];
        var distance = element.distance.text;
        var duration = element.duration.text;
        var from = origins[i];
        var to = destinations[j];
		var n = distance.split(" ");
		$('#d_'+distancecode+' .value').html(n[0]);

	  $('#d_'+distancecode+' .units').html(n[1]);
	  $('#d_'+distancecode+' .time').html(' in '+ results[j].duration.text);
      }
    }
	distancecode++;
  }
}
*/

function callback(response, status) {
  if (status == google.maps.DistanceMatrixStatus.OK) {
    var origins = response.originAddresses;
    var destinations = response.destinationAddresses;

    for (var i = 0; i < origins.length; i++) {
      var results = response.rows[i].elements;
	  
      for (var j = 0; j < results.length; j++) {
	  
        var element = results[j];
        ///////////////  distance is in meter ////////////////////////////////
          var distance = element.distance.value;

	  distance /= 1000;  // conv to km

	  var un = '';
	  if(current_unit=='miles') {
	      distance= parseFloat(Math.round(distance * km2mile *100)/100);
	      un = themiles;
	  }	      
	  else {
 	      distance= parseFloat(Math.round(distance*100)/100);   
	      un = thekm;
	  }
/////////////////////////////////////////////////////////////////////////////
        var duration = element.duration.text;
        var from = origins[i];
        var to = destinations[j];

		arr.push(distance);

      }
    }
	distancecode++;

	if(distancecode==(totalrec+1)){
		distancecode=0;
		arr.sort(function(a,b){return a-b});
		
		for(k=0;k<=arr.length;k++){
			$('#d_'+(k+1)+' .value').html(arr[k]);
			$('#d_'+(k+1)+' .units').html(un);
		}
		arr = [];
	}
  } 
}

// end function

/**
 * Change distance miles and km
 */
 
 function changeDistanceUnits(units){
    cachesearch = '';
    var results = $('#results');
    var dUnits = results.find('p.distance-units label');
    var distance = results.find('.distance');
    var unitsSpan = distance.find('.units');
    var valueSpan = distance.find('.value');
    
    dUnits.removeClass('unchecked');
    dUnits.filter(':not([units="'+units+'"])').addClass('unchecked');
    
    switch (units){
      case 'km':
        
		unitsSpan.html(' '+thekm+' ');
        
        $.each(distance, function(i, val){
          // values are already in kms so just round to 2 decimal places.
		  i++;
		  val = $('#d_'+i+' .value').html();
          val = parseFloat(val);
		
           ///////////		
//          $('#d_'+i+' .value').text((Math.round(val / milesToKm * 100) / 100));
          $('#d_'+i+' .value').text((Math.round(val * mile2km * 100) / 100));
		  $('.radius-distance').html('km');
//////////
		
        });
		
      break;  
      
      case 'miles':
	
        unitsSpan.html(' '+themiles+' ');

        $.each(distance, function(i, val){
          // Values are in kms so convert to miles then round down to two decimal places.
          i++;
		  val = $('#d_'+i+' .value').html();
		  val = parseFloat(val);
		  
          ////////////////////////////////
//          $('#d_'+i+' .value').text(Math.round((val * milesToKm) * 100) / 100);
            $('#d_'+i+' .value').text(Math.round(val * km2mile * 100)/100);
			 $('.radius-distance').html('mi');
///////////////////////////////
        });
	   
      break;

      default:
     }
  }
  
function info_window_content(v) {

	var info_window_string = "<div class='maps_popup'>";

	if(v.default_media == '' || v.default_media =='image') {
	
	
		if(v.img != '') {
			info_window_string += "<img class='img' src='"+v.img+"' alt='"+v.name+"' />";
		}
	
	} else {
		if(v.embed_video != '' && v.embed_video != 'null' && v.embed_video !=null) {
			info_window_string += v.embed_video;
			
			
		}
	}
	
	 var splitaddress = "";
	 saddress = v.address.split(" ");
	 for(i=0;i<saddress.length;i++){
		splitaddress += saddress[i]+" ";
		 if(i==4){
		  splitaddress += "<br>";
		 }
	 }

	info_window_string += "<h1>"+v.name+"</h1><p>"+splitaddress+"</p>";

	if(v.telephone != '') {
		info_window_string += "<p class='tel'>"+v.titletel+": "+v.telephone+"</p>";
	}
	if(v.email != '') {
		info_window_string += "<p class='email'>"+v.titleemail+": <a href='mailto:"+v.email+"'>"+v.email+"</a></p>";
	}
		
	if(v.website != '') {
	
		if(v.website.substring(0, 4)!="http"){
		info_window_string += "<p class='web'>"+v.titlewebsite+": <a href='http://"+v.website+"' target='new'>http://"+v.website+"</a></p>";
		} else {
			info_window_string += "<p class='web'>"+v.titlewebsite+": <a href='"+v.website+"' target='new'>"+v.website+"</a></p>";
		}
	}
	
	if(v.description != '') {
		info_window_string += "<p class='description'>"+v.description+"</p>";
	}
	
	
	if(v.cat_img != '') {
	    info_window_string += "<div class='products'><img src='"+v.cat_img+"' style='max-width:15px; max-height:15px;' /> "+v.cat_name+"</div>";
	}
	
	if(v.email != ''){
	  info_window_string += "<span class='email'><center><a href='mailto:"+v.email+"' class='contact-clinic button blue-button' style='display:block;"+
                      "padding:5px 10px;"+
                      "margin-top:10px;"+ 
                      "margin-bottom:10px;"+
                      "margin-left:3px;"+
                      "border:1px solid #8b8b8b;"+
                      "text-align: center;"+
                      "font-weight:bold;"+
                      "width:190px;'>"+v.titlecontactstore+"</a></center></span>";
					  
	}

	info_window_string += "<a href='javascript:streetView("+v.lat+","+v.lng+");'>"+ssf_street_view+"</a> | <a href='javascript:zoomHere("+v.lat+","+v.lng+");'>"+ssf_zoom_here+"</a> | <a href='javascript:direction(\""+v.address+"\","+v.lat+","+v.lng+");'>"+ssf_get_direction+"</a>";
	info_window_string += "</div>";

return info_window_string;
}

function streetView(lat,lng){

		   // street view
		   street = new google.maps.StreetViewPanorama(document.getElementById("map_canvas"), { 

			position: new google.maps.LatLng(lat, lng),
			zoomControl: false,
			enableCloseButton: true,
			addressControl: false,
			panControl: true,
			linksControl: true
		  });

}

function zoomHere(lat,lng){

	map.setZoom(parseInt(zoomhere_zoom));
	var currentLatLng = new google.maps.LatLng(lat, lng);
	map.setCenter(currentLatLng);

}


function direction(dest,lat,lng){

	 $('#direction').show();
	 $('#results').hide();
     $('#dest-direction').val(dest);


	$('#direction-form').submit(function() {
	
	 var ori = $('#origin-direction').val();

		map.setZoom(7);
		var currentLatLng = new google.maps.LatLng(lat, lng);
		map.setCenter(currentLatLng);
		
			var directionsRenderer = new google.maps.DirectionsRenderer();
			directionsRenderer.setMap(map);    
			directionsRenderer.setPanel(document.getElementById('direction'));
			 
			var directionsService = new google.maps.DirectionsService();
			
			/////////////////////
			default_unit_system = google.maps.UnitSystem.METRIC;
			if(current_unit=="km"){
			default_unit_system = google.maps.UnitSystem.METRIC;
			} else if(current_unit=="miles"){
			default_unit_system = google.maps.UnitSystem.IMPERIAL;
			}
			/////////////////////
			
			
			var request = {
			  origin: ori, 
			  destination: dest,
			  travelMode: google.maps.DirectionsTravelMode.DRIVING,
			  unitSystem: default_unit_system
			};
			directionsService.route(request, function(response, status) {
			  if (status == google.maps.DirectionsStatus.OK) {
				directionsRenderer.setDirections(response);
			  } else {
				//alert('Error: ' + status);
				$('#direction').append('<table width="100%"><tr><td>Direction not found. Please try again.</td></tr></table>');
			  }	
			});
			
      $('#direction-form').nextAll().remove();
	  return false;
	  
    });

}

function directionBack(){

	 $('#direction').hide();
	 $('#results').show();
	 resetDirection();
}

function resetDirection(){
    gmap_location_lookup($('#address').val(),$('input[name=distance]:radio:checked').val(),'');
     $('#direction').html('');
	 $('#direction').html('<h2 class="title-bg" style="padding-bottom:10px !important; ">Directions</h2><form method="post" id="direction-form"><p><table><tr><td>Origin:</td><td><input id="origin-direction" name="origin-direction" class="orides-txt" type=text /></td></tr><tr><td>Destination:</td><td><input id="dest-direction" name="dest-direction" class="orides-txt" type=text readonly /></td></tr></table><div id="get-dir-button" class="get-dir-button"><input type=submit id="get-direction" class="btn" value="Get Direction"> <a href="javascript:directionBack()">Back</a></div></p></form>');
	var origin_autocomplete = new google.maps.places.Autocomplete($("#origin-direction")[0], {});
}
