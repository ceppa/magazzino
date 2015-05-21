<?
$table="CT5";
$id_places=1;
$field="id_fotografia";
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

$query="SELECT DISTINCT parts.id_suppliers FROM $table LEFT JOIN parts ON $table.id_parts=parts.id WHERE $table.$field IS NOT NULL";

$suppliers_result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
while($suppliers_row=mysqli_fetch_assoc($suppliers_result))
{
	$id_suppliers=$suppliers_row["id_suppliers"];

	$query="INSERT INTO movements(insert_date,id_places_from,id_places_to,note,id_users)
		VALUES (NOW(),'$id_suppliers','$id_places','Fotografia',1)";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$id_movements=((is_null($___mysqli_res = mysqli_insert_id($conn))) ? false : $___mysqli_res);

	$query="SELECT $table.id_parts,$table.sn,$table.$field AS id_fotografia FROM $table LEFT JOIN parts ON $table.id_parts=parts.id WHERE parts.id_suppliers='$id_suppliers' AND $table.$field IS NOT NULL";
	$items_result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($items_row=mysqli_fetch_assoc($items_result))
	{
		$sn=str_replace("'","\'",$items_row["sn"]);
		$id_parts=$items_row["id_parts"];
		$id_users_creator=1;
		$id_users_updater=1;
		$id_fotografia=$items_row["id_fotografia"];

		$query="INSERT INTO items(id_parts,id_places,sn,id_users_creator,id_users_updater)
			VALUES ('$id_parts','$id_places','$sn','$id_users_creator','$id_users_updater')";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$id_items=((is_null($___mysqli_res = mysqli_insert_id($conn))) ? false : $___mysqli_res);

		$query="INSERT INTO movements_items(id_movements,id_items,new_from_supplier,id_fotografia)
			VALUES ('$id_movements','$id_items','1','$id_fotografia')";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
	((mysqli_free_result($items_result) || (is_object($items_result) && (get_class($items_result) == "mysqli_result"))) ? true : false);

}
((mysqli_free_result($suppliers_result) || (is_object($suppliers_result) && (get_class($suppliers_result) == "mysqli_result"))) ? true : false);
$query="COMMIT;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>