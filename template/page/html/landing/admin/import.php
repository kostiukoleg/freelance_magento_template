<?php ini_set('max_execution_time', 0);
// include Config File
date_default_timezone_set('America/New_York');
include_once './includes/config.inc.php';
include_once './includes/validate.php';
// Authenticate user login
auth();
$db = db_connect();
$func = "";
$cat_id = "";

if(!isset($_SESSION['truncate'])){
	$_SESSION['truncate'] = 1;
}

if (isset($_FILES['file'])){

$func = $_POST['func'];
$truncate = $_POST['truncate'];
$_SESSION['truncate'] = $truncate;


	$import = array();
	$extension= explode(".", $_FILES['file']['name']);
	
	if($func=="import"){
	$cat_id = $_POST['cat_id'];
	}

	
// isset Determines if a variable is set and is not NULL. Set Size Limit less then 10 MB=10485760 bytes. Extension must be CSV.
	

	if (isset($_FILES['file']) && $_FILES['file']['size'] < 10485760 && $extension[1]== 'csv')
	{  
	// We will get csv file and save it in a $file
    $file = $_FILES['file']['tmp_name']; 
	//$handle is a valid file pointer to a file successfully opened by fopen(), popen(), or fsockopen(). fopen() used to open file.
    $handle = fopen($file, "r"); 
	// We will use try{} Catch() statements here.
			try { 
	// Database Connection using PDO
		$db = db_connect();

		mysql_query("SET NAMES utf8"); 
	//truncate stores table records verification
		if($_SESSION['truncate']==1){
			mysql_query("TRUNCATE table stores"); 
		}
	//Check handel is True or False

				if ($handle !== FALSE) 
				{
		
	// fgets() Gets a line from file pointer and read the first line from $handle and ignore it.   
					fgets($handle);
	// While loop used here and  fgetcsv() parses the line it reads for fields in CSV format and returns an array containing the fields read.
					ini_set('auto_detect_line_endings',TRUE);
					
					while (($data = fgetcsv($handle)) !== FALSE)
					{
					

	

					if($func=="import"){
					
					
					$import['name'] = str_replace('\'', '\\\'', $data[0]);
					$import['address'] = str_replace('\'', '\\\'',$data[1]);
					$import['address'] .= ", ".str_replace('\'', '\\\'',$data[2]);
					$import['address'] .= ", ".str_replace('\'', '\\\'',$data[3]);
					$import['address'] .= ", ".str_replace('\'', '\\\'',$data[4]);
					$import['address'] .= ", ".str_replace('\'', '\\\'',$data[5]);

					$import['telephone'] = str_replace('\'', '\\\'',$data[6]);
					$import['fax'] = str_replace('\'', '\\\'',$data[7]);
					$import['mobile'] = str_replace('\'', '\\\'',$data[8]);
					$import['email'] = str_replace('\'', '\\\'',$data[9]);
					$import['website'] = str_replace('\'', '\\\'',$data[10]);
					$import['description'] = str_replace('\'', '\\\'',$data[11]);
					$import['approved'] = str_replace('\'', '\\\'',$data[12]);
					$import['status'] = str_replace('\'', '\\\'',$data[13]);

					//$import['approved'] = 1;
					
					//hardcode category id here
					$import['cat_id'] = $cat_id;
					
					} else if($func=="restore"){
					
					$import['name'] = str_replace('\'', '\\\'', $data[1]);
					$import['address'] = str_replace('\'', '\\\'',$data[2]);


					$import['telephone'] = str_replace('\'', '\\\'',$data[3]);
					$import['fax'] = str_replace('\'', '\\\'',$data[4]);
					$import['mobile'] = str_replace('\'', '\\\'',$data[5]);
					$import['email'] = str_replace('\'', '\\\'',$data[6]);
					$import['website'] = str_replace('\'', '\\\'',$data[7]);
					$import['description'] = str_replace('\'', '\\\'',$data[8]);
					$import['approved'] = str_replace('\'', '\\\'',$data[9]);

					$import['latitude'] = str_replace('\'', '\\\'',$data[10]);
					$import['longitude'] = str_replace('\'', '\\\'',$data[11]);
					$import['created'] = str_replace('\'', '\\\'',$data[12]);
					$import['modified'] = str_replace('\'', '\\\'',$data[13]);
					$import['status'] = str_replace('\'', '\\\'',$data[14]);
					$import['cat_id'] = str_replace('\'', '\\\'',$data[15]);
					
					}




					
					if(!$db->insert('stores',$import)) {
						$errors[] = 'Error while importing CSV file, kindly check your CSV file data/format.';
					} else {
					
					}
					
					}       
	//The file pointed to by handle will be closed.
					fclose($handle);
					
	// Closing MySQL database connection
					$dbh = null; 
	// If data inserted successfully we will redirect this page to index.php and show success message there with code 77083368
					$_SESSION['notification'] = array('type'=>'good','msg'=>'Data successfully imported.'); 
					
							
				}

			}
	// Exceptions error will be thrown if Database not connected. 
			catch(PDOException $e)
			{
			die($e->getMessage());
			}


	}
	else 
	{
	// Error mesage id File type is not CSV or File Size is greater then 10MB.
	$errors[] = 'Invalid file or file size too large, please try again.';
	}


}

mysql_query("SET NAMES utf8"); 
$cats = $db->get_rows("SELECT categories.* FROM categories WHERE categories.id!='' ORDER BY categories.cat_name");


class GeoCode {
	
	// variables to set
	var $region = 'us';
	var $table = 'stores';
	

	
	// geocode an address
	// returns $coords (array)
	function geolocate($address)
	{
		$lat = 0;
		$lng = 0;
		
		$data_location = "http://maps.google.com/maps/api/geocode/json?address=".str_replace(" ", "+", $address)."&sensor=false";
		
		if ($this->region!="" && strlen($this->region)==2) { $data_location .= "&region=".$this->region; }
		$data = file_get_contents($data_location);
		usleep(200000);
		
		// turn this on to see if we are being blocked
		// echo $data;
		
		$data = json_decode($data);
		
		if ($data->status=="OK") {
			$lat = $data->results[0]->geometry->location->lat;
			$lng = $data->results[0]->geometry->location->lng;
		}
		
		// concatenate lat/long coordinates
		$coords['lat'] = $lat;
		$coords['lng'] = $lng;
		
		return $coords;
	}
	
	// gets all addresses from a table, uses address1 / city / state / zip (change these if your column names are different)
	// checks for addresses that are not yet geolocated (bg_lat, bg_long are empty)
	// returns $result (MySQL object)
	function getAddresses()
	{
		// connect to the database
		//$this->dbsetup();
		
		$query = "SELECT address FROM " . $this->table . " WHERE latitude = '0' AND longitude = '0'";
		
		$result = mysql_query($query);
		
		return $result;
	}
	
	// updates the database with geolocated coordinates where the address is equivalent
	// echoes out the UPDATE query for quality control and visualization
	// returns void
	function updatedb($lat, $lng, $address)
	{
		$query = "UPDATE " . $this->table . "  SET latitude = '". $lat ."', longitude = '". $lng ."' WHERE address = '". $address ."'";
		$result = mysql_query($query);
		$status = "";
		if($lat==0 && $lng==0){
		   $status = "<font color=red><b>FAILED</b></font>";
		} else {
		   $status = "<font color=green><b>SUCCESS</b></font>";
		}
		
		echo "<tr>
					<td>$address</td>
					<td>$lat".","."$lng</td>
					
					<td>".$status."</td>
				</tr>";
	}
	
	// simple function used with array_walk() to escape values in preparation for insertion to the database
	// returns void
	function mysql_escape(&$value)
	{
		$value = mysql_real_escape_string($value);
	}
	
	// main function
	// returns void
	function invoke()
	{
		// get the list of addresses
		$results = $this->getAddresses();
		
		$coords = array();
		echo "<b>Imported and Geocoded address list:</b><br><br>
		
		<table class='table table-bordered' style='width:100%;'>
				<thead>
				<tr>
					<th>Address</th>
					<th>Lat/Lng</th>
					
					<th>Status</th>
				</tr>
				</thead>
				<tbody>";
				
		while ($row = mysql_fetch_array($results))
		{
			// escape the data recursively
			array_walk($row, array($this, 'mysql_escape'));
			
			// if there is a # sign for a suite number, ignore it (google has problems with these), otherwise use the address
			if (strpos($row['address'], '#'))
			{
				$addresses = explode('#',$row['address']);
				$address = $addresses[0];
			}
			else
			{
				$address = $row['address'];
			}
			
			// concatenate an address line for geolocation using commas
			//$addressline = $address . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['zip'];
			$addressline = $address;
			
			// ship it off to google
			$coords = $this->geolocate($addressline);
			
			// update the database with the coordinates
			$this->updatedb($coords['lat'], $coords['lng'], $row['address']);
		}
		
		echo "</tbody></table>";
	}
}





?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title><?php echo $lang['STORE_FINDER']; ?> - <?php echo $lang['ADMIN_ADD_USER']; ?></title>
	<?php include 'header.php'; ?>
	<script>
	function init_import(f){
	
	if($('input[name=truncate]:checked').val()=="1"){
	
		var a = confirm("Please backup your database. If you proceed, all your existing data will be lost");
		
	} else {
	
	    var a = confirm("Please backup your database. If you proceed, you will append the CSV files into your existing data.");
	}
		if(a){
			document.f1.submit();
		}
	
	}
	
	function init_import2(f){
	
	
	var a = confirm("Please backup your database. If you proceed, all your existing data will be lost");
	
		if(a){
			document.f2.submit();
		}
	
	}
	</script>
</head>
<body id="add">
	<div id="wrapper">
		<div id="header">
			
			<?php include 'nav.php'; ?>
		</div>

		<div id="main">

			<h2>Import Stores</h2>
			<?php echo notification(); ?>
			<?php if(isset($errors)): ?>
			<div class="alert alert-block alert-error fade in">
			<ul>
				<?php foreach($errors as $k=>$v): ?>
				<li><?php echo $v; ?></li>
				<?php endforeach; ?>
			</ul>
			</div>
			<?php endif; ?>

			
			<form name="f1" id="f1" class="form-horizontal" enctype="multipart/form-data" method="post" onsubmit="init_import(); return false;">
			<input type=hidden name="func" value="import" />
Select Category: <br><br><select name="cat_id">
<option value="">No Category</option>
<?php if(!empty($cats)): ?>
					<?php foreach($cats as $k=>$v): ?>

						<option value="<?php echo $v['id']; ?>"><?php echo $v['cat_name']; ?></option>
						
						
						<?php endforeach; ?>
				<?php else: ?>

				<?php endif; ?>
				</select>
			<br><br>
			<div class="radio">
  <label>
			<input type="radio" id="truncate" name="truncate" value="1" <?php if(isset($_SESSION['truncate'])){ if($_SESSION['truncate']==1){?>checked<?php } } ?>> Clear all stores database records</input>
			 </label>
</div>
<div class="radio">
  <label>
			<input type="radio" id="truncate" name="truncate" value="0" <?php if(isset($_SESSION['truncate'])){ if($_SESSION['truncate']==0){?>checked<?php } } ?>> Append to existing stores database records</input>
			 </label>
</div>
			<br><br>
			<button href="#fileupload" data-toggle="modal" class"btn btn-primary btn-large">Upload CSV</button> <a href="csv/store-samples.csv">Download Sample CSV</a>
			
			<div id="fileupload" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
              <h3 id="myModalLabel">Import Stores via CSV File</h3>
            </div>
            <div class="modal-body">

            <div class="control-group">
              <label class="control-label" for="inputName">CSV File</label>
              <div class="controls">
              <input type="hidden" name="MAX_FILE_SIZE" value="9999999" />
              <input class="btn btn-info" name="file" type="file" id="file" onchange="showCode()"  required="required"  />
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <button type="submit" class="btn">Import and GeoCode</button>
              </div>
            </div>
        

    </div>
</div>

  </form>
  
  <?php 

  if (isset($_FILES['file'])){
	 // invoke the class
	 if($func=="import"){
		$geoCode = new GeoCode();
		$geoCode->invoke();
	 }

	}
	
	?>
	<br><br>
	
	<h2>Backup Stores</h2>
	
	<a href="export.php" target="new">Export All Existing Stores to CSV</a>
		
		
	<br><br><br><br>
	
	<h2>Restore Stores </h2>
	
	<form name="f2" id="f2" class="form-horizontal" enctype="multipart/form-data" method="post" onsubmit="init_import2(); return false;">
	<input type=hidden name="func" value="restore" />

			<button href="#fileupload2" data-toggle="modal" class"btn btn-primary btn-large">Upload CSV</button> (You can only upload the Backup Stores CSV)
			
			<div id="fileupload2" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
              <h3 id="myModalLabel">Restore Stores from Backup CSV</h3>
            </div>
            <div class="modal-body">

            <div class="control-group">
              <label class="control-label" for="inputName">CSV File</label>
              <div class="controls">
              <input type="hidden" name="MAX_FILE_SIZE" value="9999999" />
              <input class="btn btn-info" name="file" type="file" id="file" onclick="showCode" required="required"  />
              </div>
            </div>
            <div class="control-group">
              <div class="controls">
                <button type="submit" class="btn">Restore Stores</button>
              </div>
            </div>
        

    </div>
</div>

  </form>
  
  
		</div>
		
		</div>
	</div>
	
	
<?php include '../themes/footer.inc.php'; ?>
</body>
</html>