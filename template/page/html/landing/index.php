<?php
// include config file
include_once './includes/config.inc.php';

// list of available distances
$distances = array(
	100=>'100 Miles',
    50=>'50 Miles',
	10=>'10 Miles',
);


if(isset($_POST['ajax'])) {
	
	if(isset($_POST['action']) && $_POST['action']=='get_nearby_stores') {
		
		if(!isset($_POST['lat']) || !isset($_POST['lng'])) {
			
			echo json_encode(array('success'=>0,'msg'=>'Coordinate not found'));
		exit;
		}
		
		// support unicode
		mysql_query("SET NAMES utf8");

		// category filter
		if(!isset($_POST['products']) || $_POST['products']==""){
			$category_filter = "";
		} else {
			$category_filter = " AND cat_id='".$_POST['products']."'";
		}
		
		$sql = "SELECT *, ( 3959 * ACOS( COS( RADIANS(".$_POST['lat'].") ) * COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS(".$_POST['lng'].") ) + SIN( RADIANS(".$_POST['lat'].") ) * SIN( RADIANS( latitude ) ) ) ) AS distance FROM stores WHERE status=1 AND approved=1 ".$category_filter." HAVING distance <= ".$_POST['distance']." ORDER BY distance ASC LIMIT 0,60";
	
		
		
		echo json_stores_list($sql);
	}
exit;
}


$errors = array();

if($_POST) {
	if(isset($_POST['address']) && empty($_POST['address'])) {
		$errors[] = 'Please enter your address';
	} else {

			
		$google_api_key = '';

		$region = 'us';
		$tmp = '';

		$xml = convertXMLtoArray($tmp);
		
		if($xml['Response']['Status']['code']=='200') {
			
			$coords = explode(',', $xml['Response']['Placemark']['Point']['coordinates']);
			
			if(isset($coords[0]) && isset($coords[1])) {
				
				$data = array(
					'name'=>$v['name'],
					'address'=>$v['address'],
					'latitude'=>$coords[1],
					'longitude'=>$coords[0]
				);

				
				$sql = "SELECT *, ( 3959 * ACOS( COS( RADIANS(".$coords[1].") ) * COS( RADIANS( latitude ) ) * COS( RADIANS( longitude ) - RADIANS(".$coords[0].") ) + SIN( RADIANS(".$coords[1].") ) * SIN( RADIANS( latitude ) ) ) ) AS distance FROM stores WHERE status=1 HAVING distance <= ".$db->escape($_POST['distance'])." ORDER BY distance ASC  LIMIT 0,60";
				
				$stores = $db->get_rows($sql);

				
				if(empty($stores)) {
					$errors[] = 'Stores with address '.$_POST['address'].' not found.';
				}
			} else {
				$errors[] = 'Address not valid';
			}
		} else {
			$errors[] = 'Entered address'.$_POST['address'].' not found.';
		}
	}
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $lang['STORE_FINDER']; ?> - Google Maps Store Locator with Google Street View, Google Direction, Admin Area, Category Icons, Store Thumbnail, Custom Markers, Google Maps API v3</title>
	 <meta name="keywords" content="street view, google direction, ajax, bootstrap, embed, geo ip, geolocation, gmap, google maps, jquery, json, map, responsive, store admin, store finder, store locator" />
     <meta name="description" content="Super Store Finder &amp;#8211; Easy to use Google Maps API Store Finder Super Store Finder is a multi-language fully featured PHP Application integrated with Google Maps API v3 that allows customers to..." />
	 <link rel="shortcut icon" href="img/favicon.ico" />

	<script>

	function changeLang(v){
	document.location.href="?langset="+v;
	}
	</script>
	  <?php include ROOT."settings.php"; ?>
	  <?php include ROOT."themes/meta_mobile.php"; ?>


</head>
<body id="super-store-finder">


<!-- Start Head Container -->
		<div class="container_12 margin">
		
			<!-- Logo -->
			<h1 class="grid_4 logo"><a href="http://superstorefinder.net" class='ie6fix'>Super Store Finder</a></h1>
			
			

		</div>
<!-- Head Container END -->
		
		<div class="clear"></div><!-- CLEAR -->
		
		<!-- Start Header Break Line -->
		<div class="container_12">
			<hr class="grid_12"></hr>
		</div>
		<!-- Header Break Line END -->
		
		<div class="clear"></div><!-- CLEAR -->
	
		<!-- Start Teaser -->
		<div class="container_12 ">
			
			<!-- Start Centered Text -->
			<div class="grid_12 middle">
				
				<!-- Heading -->	
				<center>
				<div style="padding:10px;">Language: 
				<select onChange="changeLang(this.value)">
				<option value="en_US" <?php if(!isset($_SESSION['language']) || $_SESSION['language']=="en_US") { ?>selected<?php } ?>>English</option>
				<option value="sv_SE" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="sv_SE") { ?>selected<?php } ?>>Swedish</option>
				<option value="es_ES" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="es_ES") { ?>selected<?php } ?>>Spanish</option>
				<option value="fr_FR" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="fr_FR") { ?>selected<?php } ?>>French</option>
				<option value="de_DE" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="de_DE") { ?>selected<?php } ?>>German</option>
				<option value="cn_CN" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="cn_CN") { ?>selected<?php } ?>>Chinese</option>
				<option value="kr_KR" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="kr_KR") { ?>selected<?php } ?>>Korean</option>
				<option value="jp_JP" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="jp_JP") { ?>selected<?php } ?>>Japanese</option>
				<option value="ar_AR" <?php if(isset($_SESSION['language']) && $_SESSION['language']=="ar_AR") { ?>selected<?php } ?>>Arabic</option>
				</select>
				</div>		
				</center>
				
			</div><!-- Centered Text END -->
	
		</div>
		<!-- Teaser END -->
	
	<div class="clear"></div>
		
	<!-- Start Container 12 -->
	<div id="main_content" class="container_12">
	
		<div id="main">
		<div class="width-container">

			<div id="container-sidebar">
				
				
				
				
				
				<div class="content-boxed">
				
					
					
					
					
	
					
					<div id="map-container">

						<div id="clinic-finder" class="clear-block">
						<div class="links"></div>
			
						<form method="post" action="./index.php" accept-charset="UTF-8" method="post" id="clinic-finder-form" class="clear-block">
							<table style="width:100%">
							<tr><td width="95%" style="padding-right:20px;">
							<div class="form-item" id="edit-gmap-address-wrapper">
							 <label for="edit-gmap-address"><?php echo $lang['PLEASE_ENTER_YOUR_LOCATION']; ?>: </label>
							 <input type="text" maxlength="128" name="address" id="address" size="60" value="" class="form-text" autocomplete="off" />
							</div>
							</td>
							</tr>
							<tr>
							
							<td  width="95%">
							<?php 
							// support unicode
							mysql_query("SET NAMES utf8");
							$cats = $db->get_rows("SELECT categories.* FROM categories WHERE categories.id!='' ORDER BY categories.cat_name ASC");

							?>
							<div class="form-item" id="edit-products-wrapper">
							 
							 <select name="products" class="form-select" id="edit-products" ><option value=""><?php echo $lang['SSF_ALL_CATEGORY']; ?></option>
							 <?php if(!empty($cats)): ?>
								<?php foreach($cats as $k=>$v): ?>
								<option value="<?php echo $v['id']; ?>"><?php echo $v['cat_name']; ?></option>
								<?php endforeach; ?>
								<?php endif; ?>
							 </select>
							 
							
							
							</div>
							
							</tr>
							<tr><td align="center" nowrap><input type="submit" name="op" id="edit-submit" value="<?php echo $lang['FIND_STORE']; ?>" class="btn btn-primary" style="color:white !important;"/>
							<input type="hidden" name="form_build_id" id="form-0168068fce35cf80f346d6c1dbd7344e" value="form-0168068fce35cf80f346d6c1dbd7344e"  />
							<input type="hidden" name="form_id" id="edit-clinic-finder-form" value="clinic_finder_form"  />
							
							<input type="button" name="op" onclick="document.location.href='newstore.php'" id="edit-submit" value="<?php echo $lang['REQUEST_ADD_STORE']; ?>" class="btn btn-primary" style="color:white !important;"/>
							
							</td></tr>
							</table>
							


					  <div id="map_canvas"><?php echo $lang['JAVASCRIPT_ENABLED']; ?></div>
					  <div id="results">        
						<h2 class="title-bg" style="padding-bottom:10px !important; "><input type="radio" id="distance" name="distance" value="50" onChange="cachesearch = ''; $('#clinic-finder-form').submit();"> 50 <div class="radius-distance" style="display:inline;"><?php echo DEFAULT_DISTANCE; ?></div> <input type="radio" id="distance" name="distance" value="100" checked  onChange="cachesearch = ''; $('#clinic-finder-form').submit();"> 100 <div class="radius-distance"  style="display:inline;"><?php echo DEFAULT_DISTANCE; ?></div></h2>
						<p class="distance-units">
						  <label class="km <?php if(DEFAULT_DISTANCE=="mi") {?>unchecked<?php } ?>" units="km" <?php if(DEFAULT_DISTANCE=="mi") {?> checked="unchecked"<?php } ?> >
							<input type="radio" name="distance-units" value="kms" /><?php echo $lang['KM']; ?>
						  </label>
						  <label class="miles <?php if(DEFAULT_DISTANCE=="km") {?>unchecked<?php } ?>" <?php if(DEFAULT_DISTANCE=="km") {?> checked="unchecked"<?php } ?>units="miles">
							<input type="radio" name="distance-units" value="miles" /><?php echo $lang['MILES']; ?>
						  </label>
						</p>
						<ol style="display: block; " id="list"></ol>
					  </div>
					  
					  
					    </form>
						
					  
					  <div id="direction">
					  <h2 class="title-bg" style="padding-bottom:10px !important; ">Directions</h2>
					  <form method="post" id="direction-form">
					  
					  <p>
					  <table><tr>
					  <td>Origin:</td><td><input id="origin-direction" name="origin-direction" class="orides-txt" type=text /></td>
					  </tr>
					  <tr>
					  <td>Destination:</td><td><input id="dest-direction" name="dest-direction" class="orides-txt" type=text readonly /></td>
					  </tr>
					  </table>
					  <div id="get-dir-button" class="get-dir-button"><input type=submit id="get-direction" class="btn" value="Get Direction"> <a href="javascript:directionBack()">Back</a></div></p>
					  </form>
					  </div>
					  
	</div>


					</div>
					
						
						<div class="clearfix"></div>

					</div>
					

			</div><!-- close #container-sidebar -->
			

			
		<div class="clearfix"></div>
		</div><!-- close .width-container -->
	</div><!-- close #main -->
  
   <center>


		
		

		<div class="clear"></div><!-- CLEAR -->
				
  <br><br>

		 <h4><?php echo $lang['EMBED']; ?>:</h4>
  <textarea id="embed" style="width:650px;"><iframe src="<?php echo ROOT_URL; ?>embed.php" width="100%" height="1180px" scrolling=no frameborder=no allowtransparency="true"></iframe></textarea>
		<br><br>
		
		<div class="grid_12">
			
			
  </center>
  

	
	<script>
		if(geo_settings==1){
				$('#address').val(geoip_city()+", "+geoip_country_name());
		} else {
				$('#address').val(default_location);
		}
	</script>
    <div class="clear"></div>
	<center>
      <br>
   <br>
<a href="index.php" rel="nofollow" ><img src="http://superstorefinder.net/img/store.jpg" alt="Super Store Finder Demo"></a> <a href="admin" rel="nofollow" ><img src="http://superstorefinder.net/img/admin.jpg" alt="Super Store Finder Admin Demo"></a>
  <br>
<a href="http://superstorefinder.net/products/superstorefinder/index_geoip.php" rel="nofollow" ><img src="http://superstorefinder.net/img/geoip.jpg" alt="Super Store Finder with Geo IP"></a> <a href="http://superstorefinder.net/clients/responsive" rel="nofollow" ><img src="http://superstorefinder.net/img/responsive.jpg" alt="Super Store Finder Responsive Demo"></a>
			
</center>
		</div>
	
	</div><!-- Container 12 END-->






<?php include ROOT."themes/footer.inc.php"; ?>
</body>
</html>