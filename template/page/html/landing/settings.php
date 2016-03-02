<script>

var default_distance = '<?php echo DEFAULT_DISTANCE; ?>';
var init_zoom = '<?php echo INIT_ZOOM; ?>';
var zoomhere_zoom = '<?php echo ZOOMHERE_ZOOM; ?>';

<?php if(isset($_REQUEST['boxsearch']) && $_REQUEST['boxsearch']!='') { ?>
var geo_settings = '0';
var default_location = '<?php echo $_REQUEST['boxsearch']; ?>';
<?php } else { ?>
var geo_settings = '<?php echo GEO_SETTINGS; ?>';
var default_location = '<?php echo DEFAULT_LOCATION; ?>';
<?php } ?>

var style_map_color = '<?php echo STYLE_MAP_COLOR; ?>';

var ssf_street_view = '<?php echo $lang['SSF_STREET_VIEW']; ?>';
var ssf_get_direction = '<?php echo $lang['SSF_GET_DIRECTION']; ?>';
var ssf_zoom_here = '<?php echo $lang['SSF_ZOOM_HERE']; ?>';

var ssf_map_code;
<?php
if(STYLE_MAP_CODE!=""){
 echo "\n\n
 var ssf_map_code=".stripslashes(STYLE_MAP_CODE).";\n\n";
}
?>


</script>

<style>

#clinic-finder form#clinic-finder-form {
<?php if(STYLE_TOP_BAR_BG!=""){ ?>
background-color: <?php echo STYLE_TOP_BAR_BG; ?> !important;
background-image: none !important;
<?php } ?>

<?php if(STYLE_TOP_BAR_BORDER!=""){ ?>
border: 1px solid <?php echo STYLE_TOP_BAR_BORDER; ?> !important;
<?php } ?>
}

<?php if(STYLE_TOP_BAR_FONT!=""){ ?>
#clinic-finder form#clinic-finder-form label {
color: <?php echo STYLE_TOP_BAR_FONT; ?> !important;
}
<?php } ?>

<?php if(STYLE_RESULTS_BG!=""){ ?>
#clinic-finder {
background-color: <?php echo STYLE_RESULTS_BG; ?> !important;
}
<?php } ?>

<?php if(STYLE_RESULTS_HL_BG!=""){ ?>
#results ol li.active {
background-color:  <?php echo STYLE_RESULTS_HL_BG; ?> !important;
}
<?php } ?>

<?php if(STYLE_RESULTS_FONT!=""){ ?>
#clinic-finder #results ol li {
color: <?php echo STYLE_RESULTS_FONT; ?> !important; 
}

.help-block, .help-inline {
color: <?php echo STYLE_RESULTS_FONT; ?> !important;
}

#form_new_store{
color: <?php echo STYLE_RESULTS_FONT; ?> !important;
}
<?php } ?>

<?php if(STYLE_RESULTS_DISTANCE_FONT!=""){ ?>
#clinic-finder #results ol li div.distance {
color: <?php echo STYLE_RESULTS_DISTANCE_FONT; ?>  !important; 
}
<?php } ?>


<?php if(STYLE_DISTANCE_TOGGLE_BG!=""){ ?>
#clinic-finder .distance-units {
background-color: <?php echo STYLE_DISTANCE_TOGGLE_BG; ?>  !important;
background-image: none !important;
}
<?php } ?>


#clinic-finder .blue-button {
<?php if(STYLE_CONTACT_BUTTON_BG!=""){ ?>
background-image: -webkit-gradient(linear, 0% 0%, 0% 90%, from(<?php echo STYLE_CONTACT_BUTTON_BG; ?>  ), to(<?php echo STYLE_CONTACT_BUTTON_BG; ?>  )) !important;
background-image: -moz-linear-gradient(<?php echo STYLE_CONTACT_BUTTON_BG; ?>   0%, <?php echo STYLE_CONTACT_BUTTON_BG; ?>   90%) !important;
<?php } ?>

<?php if(STYLE_CONTACT_BUTTON_FONT!=""){ ?>
color: <?php echo STYLE_CONTACT_BUTTON_FONT; ?> !important; 
<?php } ?>
}



<?php if(STYLE_LIST_NUMBER_BG!=""){ ?>
#clinic-finder #results ol li {

background-image: url('') !important;

}


#clinic-finder #results ol li span.number {
background: <?php echo STYLE_LIST_NUMBER_BG; ?> !important;
font-size: 14px;
display: block;
position: absolute;
top: 10px;
left: 6px!important;

width: 28px;
padding: 4px 0px;
text-align: center;
background: #333;
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
border-radius: 4px;
}
<?php } ?>

<?php if(STYLE_LIST_NUMBER_FONT!=""){ ?>
#clinic-finder #results ol li span.number {

color: <?php echo STYLE_LIST_NUMBER_FONT; ?> !important;

}
<?php } ?>


<?php if(STYLE_BUTTON_BG!=""){ ?>

.btn-primary {
padding: 10px 10px;

text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25) !important;
background-color: <?php echo STYLE_BUTTON_BG; ?> !important;
background-image: -moz-linear-gradient(top, <?php echo STYLE_BUTTON_BG; ?>, <?php echo STYLE_BUTTON_BG; ?>) !important;
background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo STYLE_BUTTON_BG; ?>), to(<?php echo STYLE_BUTTON_BG; ?>))  !important;
background-image: -webkit-linear-gradient(top, <?php echo STYLE_BUTTON_BG; ?>, <?php echo STYLE_BUTTON_BG; ?>)  !important;
background-image: -o-linear-gradient(top, <?php echo STYLE_BUTTON_BG; ?>, <?php echo STYLE_BUTTON_BG; ?>)  !important;
background-image: linear-gradient(to bottom, <?php echo STYLE_BUTTON_BG; ?>, <?php echo STYLE_BUTTON_BG; ?>)  !important;
background-repeat: repeat-x  !important;
border-color: #E76804 #E76804 #E76804  !important;
border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25)  !important;
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='<?php echo STYLE_BUTTON_BG; ?>', endColorstr='<?php echo STYLE_BUTTON_BG; ?>', GradientType=0)  !important;
filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
}

.btn-primary:hover,
.btn-primary:focus,
.btn-primary:active,
.btn-primary.active,
.btn-primary.disabled,
.btn-primary[disabled] {
  color: #ffffff !important;
  background-color: <?php echo STYLE_BUTTON_BG; ?> !important;
  *background-color: <?php echo STYLE_BUTTON_BG; ?> !important;
}
<?php } ?>

<?php if(STYLE_BUTTON_FONT!=""){ ?>
.btn-primary {
color: <?php echo STYLE_BUTTON_FONT; ?> !important;
}
<?php } ?>

<?php if(STYLE_RESULTS_HOVER_BG!=""){ ?>
#clinic-finder #results ol li:hover {
  background-color: <?php echo STYLE_RESULTS_HOVER_BG; ?> !important;

}
<?php } ?>

.btn-group{
width:100% !important;
}

</style>