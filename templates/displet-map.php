<?php $results = &$model['results']; foreach ($results as $result) : ?>
	<?php
		$latitude = $result->{'latitude'};
		$longitude = $result->{'longitude'};
		$address = $result->{'street-number'} . ' ' . $result->{'street-name'} . ' ' .$result->{'street-post-dir'} . '<br>' . $result->{'city'} . ' ' . $result->{'state'};
		$address = stripslashes(str_replace("'", "", $address));
		$url = $result->{'details'};
		$price = $result->{'list-price'};
		if (!is_int($price)) {
			$price = intval($price);
		}
		$price = number_format($price);
		$latlongs[] = array(
			'lat' => $latitude,
			'long' => $longitude,
			'address' => $address,
			'url' => $url,
			'price' => $price
		);
		$lats[] = floatval($latitude);
		$longs[] = floatval($longitude);
	?>
<?php endforeach; ?>
	
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
function loadDispletMap(min_price_option, max_price_option, new_map) {
	if (new_map) {
		renderDispletMap();
	}
	else {
		google.maps.event.addDomListener(window, 'load', renderDispletMap);
	}
  function renderDispletMap() {
	<?php
	if (count($latlongs) > 0) {
		$lat_count = count(array_filter($lats));
		$long_count = count(array_filter($longs));
		$lat_avg = array_sum($lats) / $lat_count;
		$long_avg = array_sum($longs) / $long_count;
		if (is_numeric($model['map_latlong_distance'])) {
			$variance = floatval($model['map_latlong_distance']);
		}
		else $variance = 2;
		global $lat_min, $lat_max, $long_min, $long_max;
		$lat_min = $lat_avg - $variance;
		$lat_max = $lat_avg + $variance;
		$long_min = $long_avg - $variance;
		$long_max = $long_avg + $variance;
		if (!function_exists('isNearbyLat')) {
			function isNearbyLat($cur) {
				global $lat_min, $lat_max;
				$lat = floatval($cur);
				if ($lat >= $lat_min && $lat <= $lat_max) {
					return true;
				}
				else return false;
			}
		}
		if (!function_exists('isNearbyLong')) {
			function isNearbyLong($cur) {
				global $long_min, $long_max;
				$long = floatval($cur);
				if ($long >= $long_min && $long <= $long_max) {
					return true;
				}
				else return false;
			}
		}
		$i = 0;
		foreach ($latlongs as $latlong) {
			if ($i < 1 && $latlong['lat'] != '' && $latlong['long'] != '') { ?>
				var centerLatLng = new google.maps.LatLng(<?php echo $latlong['lat']; ?>,<?php echo $latlong['long']; ?>);
			<?php $i++; }
		}
	}
	?>
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
      zoom: 13,
      center: centerLatLng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infoWindow = new google.maps.InfoWindow;

    function onMarkerClick(marker,address,url,price) {
      var latLng = marker.getPosition();
      infoWindow.setContent('<div class="marker-inner"><a href="' + url + '"><h3>$' + price + '</h3><h4>' + address + '</h4></a></div>');

      infoWindow.open(map, marker);
    };
    google.maps.event.addListener(map, 'click', function() {
      infoWindow.close();
    });

	var LatLngList = new Array();
	<?php if(count($latlongs)>0) {$i = 1; foreach ($latlongs as $latlong) : ?>
		<?php if ($latlong['lat'] != '' && $latlong['long'] != '' && isNearbyLat($latlong['lat']) && isNearbyLong($latlong['long'])) : ?>
			var address<?php echo $i; ?> = '<?php echo $latlong['address']; ?>';
			var url<?php echo $i; ?> = '<?php echo $latlong['url']; ?>';
			var price<?php echo $i; ?> = '<?php echo $latlong['price']; ?>';
			var price_value<?php echo $i; ?> = <?php echo str_replace(',','',$latlong['price']); ?>;
			if (price_value<?php echo $i; ?> >= min_price_option && price_value<?php echo $i; ?> <= max_price_option) {
				var myLatlng<?php echo $i; ?> = new google.maps.LatLng(<?php echo $latlong['lat']; ?>,<?php echo $latlong['long']; ?>);		
				var marker<?php echo $i; ?> = new google.maps.Marker({
		    	  map: map,
		    	  position: myLatlng<?php echo $i; ?>
		    	});
			    google.maps.event.addListener(marker<?php echo $i; ?>, 'click', function() {
					onMarkerClick(this,address<?php echo $i; ?>,url<?php echo $i; ?>,price<?php echo $i; ?>);
				});
				LatLngList.push(myLatlng<?php echo $i; ?>);
			}
		<?php endif; ?>
	<?php $i++; endforeach; } ?>

    //  Make an array of the LatLng's of the markers you want to show
	//var LatLngList = array (new google.maps.LatLng (52.537,-2.061), new google.maps.LatLng (52.564,-2.017));
	//  Create a new viewpoint bound
	var bounds = new google.maps.LatLngBounds ();
	//  Go through each...
	for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
	  //  And increase the bounds to take this point
	  bounds.extend (LatLngList[i]);
	}
	//  Fit these bounds to the map
	map.fitBounds (bounds);

  }
}
loadDispletMap(0,999999999,false);
</script>
<?php if ($lat_count > 0 && $long_count > 0) : ?>
	<div id="displet-map" class="<?php if ($model['map_hide'] == 'yes'){ echo 'hiding';} else{ echo 'showing';} ?>">

		<div class="showhide">
			<a href="javascript:void(0);" class="show" title="Show Map">
				<span class="plusminus">[+]</span><span class="under">Show Map</span>
			</a>
			<a href="javascript:void(0);" class="hide" title="Hide Map">
				<span class="plusminus">[-]</span><span class="under">Hide Map</span>
			</a>
		</div>
		<div id="map_canvas" style="<?php echo 'height:' . $model['map_height'] . 'px; margin-bottom:-' . $model['map_height'] . 'px;'; if ($model['map_width']) echo ' width:' . $model['map_width'] . 'px;'; ?>"></div>
	</div>
<?php endif; ?>
