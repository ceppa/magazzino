<?
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


$query="START TRANSACTION;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));



$query="select * from gioia WHERE sn is not null order by id_supplier,ordine";
$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));



while($row=mysqli_fetch_assoc($result))
{
	$sn=$row["sn"];
	$location=$row["ref_design"];
	$id_places="14";
	$id_parts=$row["id_parts"];
	$id_movements=$row["id_movements"];
	$q="insert into items(id_parts,id_places,location,sn,id_users_creator,id_users_updater)
		values ('$id_parts','$id_places','$location','$sn',1,1)";
	mysqli_query($GLOBALS["___mysqli_ston"], $q)
 		or die_($conn,"$q<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	echo "eseguito $q<br>";
	$id_items=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	$q="insert into movements_items(id_movements,id_items,new_from_supplier)
		values ('$id_movements','$id_items',1)";
	mysqli_query($GLOBALS["___mysqli_ston"], $q)
 		or die_($conn,"$q<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	echo "eseguito $q<br>";
}

((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
$query="COMMIT;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
echo "done";
?>
