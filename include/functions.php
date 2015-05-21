<?php
function make_key() 
{
	$random_string = '';
	for($i=0;$i<32;$i++)
		$random_string .= chr(rand(97,122));
	return $random_string;
}

function runSQL($rsql) 
{
	global $myhost, $myuser, $mypass, $mydb;

	$conn	=	($GLOBALS["___mysqli_ston"] = mysqli_connect(	$myhost, 
								$myuser, 
								$mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	((bool)mysqli_query(
						$conn , "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $rsql) 
		or die ( $rsql.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	return $result;

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}

function countRec($fieldName,$tableName) 
{
	$sql = "SELECT count($fieldName) FROM $tableName ";
	
	$result = runSQL($sql);
	
	while ($row = mysqli_fetch_array($result)) 
	{
		return $row[0];
	}
}

function nlTobr($string)
{
	$nls = array("\r\n", "\r", "\n"); 
	return str_replace($nls,"<br>",$string);
}

function queryWithRollback($query)
{
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
	if(!$result)
	{
		$error=((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
		mysqli_query($GLOBALS["___mysqli_ston"], "ROLLBACK");
		die("$query<br>$error");
	}
	return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

function date_to_sql($date)
{
	if($date=="----")
		return "0000-00-00";
	else
	{
		$explode=explode("/",$date);
		$dd=(int)$explode[0];
		$mm=(int)$explode[1];
		$yy=(int)$explode[2];
		return(date("Y-m-d",mktime(0,0,0,$mm,$dd,$yy)));
	}
}

function isItemInPhoto($id_item)
{
}

function isPnInPhoto($pn)
{
}

function getPhotoArray($id_item)
{
}
function ifnull($var, $default='') {
    return isset($var) ? $var : $default;
}

?>
