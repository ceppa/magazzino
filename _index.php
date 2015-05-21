<?php
include_once('include/functions.php');

if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
{
	if(strpos($_SERVER['HTTP_USER_AGENT'], '6.0') !== false)
	{
		$browser = "ie6";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], '7.0') !== false)
	{
		$browser="ie7";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], '8.0') !== false)
	{
	$browser="ie8";
	}
}


if(isset($_SESSION["pass"]))
	$login_password = $_SESSION["pass"];
$isLogged = false;
$random_string = '';
$address_is_good = false;
$agent_is_good = false;

/* temporaneo */
$_SESSION['userType'] = 'admin';

ini_set ('session.name', 'magazzino');
session_cache_limiter('private_no_expire');
session_start();

if (isset($_SESSION['key']))
{
	$random_string = $_SESSION['key'];
}
else
{
	$random_string = $_SESSION['key'] = make_key();
}

if ((isset($_SESSION['remote_addr']))
		&&($_SERVER['REMOTE_ADDR'] == $_SESSION['remote_addr'])) 
{
    $address_is_good = true;
}
else 
{
	$_SESSION['remote_addr'] = $_SERVER['REMOTE_ADDR'];
}

if ((isset($_SESSION['agent']))
		&&($_SERVER['HTTP_USER_AGENT'] == $_SESSION['agent']))
{
    $agent_is_good = true;
}
else
{
	$_SESSION['agent'] = $_SERVER['HTTP_USER_AGENT'];
}

include('include/login.php');
?>



<?php echo '<?xml version="1.0" encoding="utf-8"?>'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>Magazzino</title>

<!-- JAVASCRIPT LIBRARIES -->
<script	type="text/javascript"
		src="js/jquery-1.2.6.js"></script>
<script	type="text/javascript"
		src="js/config.js"></script>
<script	type="text/javascript"
		src="js/utilities.js"></script>
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
		src="js/warehouses.js"></script>
<script	type="text/javascript"
		src="js/flexigrid/flexigrid.js"></script>
<script type="text/javascript" src="js/md5.js"></script>
<script type="text/javascript" 
        src="js/jquery.autocomplete.js"></script>
<script type="text/javascript" src="js/jquery.dataTables.js"></script>
<script type="text/javascript" src="js/jquery.dimensions.js"></script>
<script type="text/javascript" src="js/jquery.positionBy.js"></script>
<script type="text/javascript" src="js/jquery.bgiframe.js"></script>
<script type="text/javascript" src="js/jquery.jdMenu.js"></script>
<script	type="text/javascript">
$(document).ready(function(){
	initAjax();
    $('ul.jd_menu').jdMenu({showDelay:0,hideDelay:0});
    bind_menu_part_list_click();
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
		href="css/dataTables.css" />
<link	rel="stylesheet"
		type="text/css"
		media="all"
		href="css/my.css" />
</head>
<body>
<div class="mainMenuContainer">
<?php 
if($isLogged) {
    include('include/mainMenu.php'); 
}
?>
</div>
<br>
<?php include("include/loading.php"); ?>
<div id="tableContainer"></div>
<div	class="container_12">
        <?php
		if(!$isLogged)
			include('include/loginForm.php');
        ?>
		<div	class="clear">&nbsp;</div>


		<div	id="right_frame_1"
				class="grid_12"
				style="display:none">
			<div	class="frame_title">
				&nbsp;
			</div>
			<div	class="frame_body">
				&nbsp;
			</div>
		</div>
	</div>
	<div	class="clear">&nbsp;</div>
	<table>
        <tr>
            <td style="border:1px solid #000">
		<?php
			echo "<br/><b>POST<br/></b>";
			echo "<pre>".htmlspecialchars(print_r($_POST,true))."</pre>";
		?>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000">
		<?php
			echo "<br/><b>GET<br/></b>";
			echo "<pre>".htmlspecialchars(print_r($_GET,true))."</pre>";
		?>
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000">
		<?php
			echo "<br/><b>SESSION<br/></b>";
			echo "<pre>".htmlspecialchars(print_r($_SESSION,true))."</pre>";
		?>
            </td>
        </tr>
		<tr>
            <td style="border:1px solid #000">
		<?php
			echo "<br/><b>\$message<br/></b>";
			print_r($message);
		?>
            </td>
        </tr>
		<tr>
            <td style="border:1px solid #000">
		<?php
			echo "<br/><b>\$isLogged<br/></b>";
			print_r($isLogged);
		?>
            </td>
        </tr>
    </table>
</body>
</html>
