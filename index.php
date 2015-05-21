<?php

date_default_timezone_set("Europe/Rome");
ini_set('include_path',get_include_path().PATH_SEPARATOR.'/home/hightecs/php');
ini_set('error_reporting',E_ALL & ~E_NOTICE );

require_once('include/functions.php');
if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
{
	if(strpos($_SERVER['HTTP_USER_AGENT'], '6.0') !== false)
		$browser="ie6";
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], '7.0') !== false)
		$browser="ie7";
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], '8.0') !== false)
		$browser="ie8";
}


/* temporaneo */

ini_set ('session.name', 'magazzino');
session_cache_limiter('private_no_expire');
session_start();

include('include/login.php');
if((!$isLogged)&&(!isset($_GET["rnd"]))&&(!$expired))
	header("location: ".$_SERVER["PHP_SELF"]."?rnd=".rand());
echo '<?xml version="1.0" encoding="utf-8"?>';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Magazzino</title>

<!-- JAVASCRIPT LIBRARIES -->
<script	type="text/javascript"
		src="js/jquery-1.2.6_min.js"></script>
<script	type="text/javascript"
		src="js/jquery.json-2.4.min.js"></script>
<script	type="text/javascript"
		src="js/jquery.ui-1.5.2/ui/effects.core.js"></script>
<script	type="text/javascript"
		src="js/jquery.ui-1.5.2/ui/ui.datepicker.js"></script>
<script	type="text/javascript"
		src="js/config.js"></script>
<script	type="text/javascript"
		src="js/utilities.js"></script>
<!-- <script	type="text/javascript"
		src="js/formChecks.js"></script> -->
<script	type="text/javascript"
		src="js/jquery.form.js"></script>
<script	type="text/javascript"
		src="js/main.js"></script>
<script	type="text/javascript"
		src="js/users.js"></script>
<script	type="text/javascript"
		src="js/parts.js"></script>
<script	type="text/javascript"
		src="js/items.js"></script>
<script	type="text/javascript"
		src="js/places.js"></script>
<script	type="text/javascript"
		src="js/movements.js"></script>
<script	type="text/javascript"
		src="js/reports.js"></script>
<script	type="text/javascript"
		src="js/fotografia.js"></script>
<script	type="text/javascript"
		src="js/admin.js"></script>
<script	type="text/javascript"
		src="js/compatibles.js"></script>
<script	type="text/javascript"
		src="js/flexigrid/flexigrid.js"></script>
<script	src="js/md5.js"
		type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/jquery.dimensions.js"></script>
<script type="text/javascript" src="js/jquery.positionBy.js"></script>
<script type="text/javascript" src="js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="js/jquery.jdMenu.js"></script>
<script	src="js/jquery.autocomplete.js"
		type="text/javascript"></script>
<script	src="js/toolbar.js"
		type="text/javascript"></script>
<script type="text/javascript">
	var session_level=<?=(int)$_SESSION["user_type"]?>;
$(function()
{
	initAjax();
	initMenu();
	$("#menu_inventory_list").click();
	var globalKeep=setInterval(function(){$.ajax({url:'include/keepalive.php'})},600000);
});
	</script>
	
<!-- CSS -->
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/reset.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/text.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/jquery.jdMenu.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/my.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/toolbar.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="js/flexigrid/css/flexigrid.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/ui.datepicker.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/autocomplete.css" />
</head>
<body>
	<div id="loading-div-background">
		<div id="loading-div" class="ui-corner-all" >
			<img style="height:80px;margin:30px;" src="img/loader.gif" alt="Loading.."/>
			<h2 style="color:gray;font-weight:normal;">Please wait....</h2>
		</div>
	</div>
<div class="mainMenuContainer">
<?php 
	if($isLogged)
		include('include/mainMenu.php'); 

?>
</div>
<div id="toolbar_container" style="display:none;">
<?php include('include/toolbar.php'); ?>
</div>
<div id="flexi_container">
	<table	class="flexi">
	</table>
</div>
<div id="loading_container" class="myLoading">
<?php include('include/loading.php'); ?>
</div>
<div id="form_container">
</div>
<?php
	if(!$isLogged)
		include('include/loginForm.php');
?>

<div class="messagebox" id="messageBox"></div>
</body>
</html>
