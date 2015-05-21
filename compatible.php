<?
$tabella="CT5";


function die_($conn,$text)
{
	$query="ROLLBACK;";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	die($text);
}
require_once('include/mysql.php');
$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
	or die_($conn,"Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
((bool)mysqli_query($conn, "USE " . $mydb))
	or die_($conn,"Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

$query="SELECT MAX(id_compatible) as maxid FROM compatible_parts";
$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
if($row=mysqli_fetch_array($result))
	$id_compatible=1+$row["maxid"];
else
	$id_compatible=1;
((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

$query="START TRANSACTION;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

$query="SELECT DISTINCT `$tabella`.id_parts 
			FROM $tabella LEFT JOIN compatible_parts
			ON `$tabella`.id_parts=compatible_parts.id_parts
			WHERE compatible_parts.id_parts IS NULL AND `$tabella`.id_parts IS NOT NULL";

$parts_result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

while($parts_row=mysqli_fetch_assoc($parts_result))
{
	$id_parts=$parts_row["id_parts"];

	
	$query="INSERT INTO compatible_parts(id_compatible,id_parts)
		VALUES ('$id_compatible','$id_parts')";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$id_compatible++;

}
((mysqli_free_result($parts_result) || (is_object($parts_result) && (get_class($parts_result) == "mysqli_result"))) ? true : false);
$query="COMMIT;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>
