<?php session_start();

// define the root path to the frontend folder
define('ROOT', '');
// define the root URL to the frontend section
define('ROOT_URL', 'http://cmmi.it/CaboozaTEST/landing/');
// General settings
define('DEFAULT_LANGUAGE', 'en_US');
define('DEFAULT_DISTANCE','mi');
define('INIT_ZOOM','11');
define('ZOOMHERE_ZOOM','15');
define('GEO_SETTINGS','1');
define('DEFAULT_LOCATION','New York, US');

// Styles settings

define('STYLE_MAP_COLOR','');
define('STYLE_TOP_BAR_BG','');
define('STYLE_TOP_BAR_FONT','');
define('STYLE_TOP_BAR_BORDER','');

define('STYLE_RESULTS_BG','');
define('STYLE_RESULTS_HL_BG','');
define('STYLE_RESULTS_HOVER_BG','');
define('STYLE_RESULTS_FONT','');
define('STYLE_RESULTS_DISTANCE_FONT','');

define('STYLE_DISTANCE_TOGGLE_BG','');
define('STYLE_CONTACT_BUTTON_BG','');
define('STYLE_CONTACT_BUTTON_FONT','');

define('STYLE_BUTTON_BG','');
define('STYLE_BUTTON_FONT','');

define('STYLE_LIST_NUMBER_BG','');
define('STYLE_LIST_NUMBER_FONT','');
// map style code patch 2.6
define('STYLE_MAP_CODE','');

if(isset($_REQUEST['langset'])){
   $_SESSION['language']=$_REQUEST['langset'];
}


// default language file
if(isset($_SESSION['language'])){
	include_once ROOT.'language/'.htmlspecialchars($_SESSION['language']).'.php';
} else {
	include_once ROOT.'language/'.DEFAULT_LANGUAGE.'.php';
	$_SESSION['language'] = DEFAULT_LANGUAGE;
}

// include library file
include_once ROOT.'includes/library.php';
// include database class
include_once ROOT.'includes/class.database.php';
// include image class
include_once ROOT.'includes/class.img.php';


// define database settings
define('HOSTNAME','localhost');
define('DB_USERNAME','danilo_cabooza_m');
define('DB_PASSWORD','CT(c^OM3N75%');
define('DB_NAME','danilo_superstorefinder');
// admin email address
define('ADMINISTRATOR_EMAIL','danilo.calandra@gmail.com');

//trigger install
if(DB_USERNAME=="your_database_username"){
echo "<script>document.location.href='install.php'</script>";
}

// new db instance
$db = new DB(array(
	'hostname'=>HOSTNAME,
	'username'=>DB_USERNAME,
	'password'=>DB_PASSWORD,
	'db_name'=>DB_NAME
));

// stop on db fail
if($db===FALSE) {
	$db_errors = $db->errors;
}

?>


