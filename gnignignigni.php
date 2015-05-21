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

$query='SELECT fotografia.id as id_fotografia,items.id as id_items 
	FROM `fotografia` 
		LEFT JOIN compatible_parts 
			ON fotografia.id_compatible=compatible_parts.id_compatible
		LEFT JOIN parts 
			ON compatible_parts.id_parts=parts.id
	JOIN items ON parts.id=items.id_parts 
		AND fotografia.sn=items.sn 
		AND fotografia.id_places=items.id_places';

$items_result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
while($items_row=mysqli_fetch_assoc($items_result))
{
	$id_items=$items_row["id_items"];
	$id_fotografia=$items_row["id_fotografia"];
	$query="UPDATE movements_items INNER JOIN 
					(
						SELECT movements_items.id
						FROM movements_items 
							LEFT JOIN movements ON 
								movements_items.id_movements=movements.id
						WHERE movements_items.id_items='$id_items'
						ORDER BY movements.insert_date DESC
						LIMIT 1
					) AS last_movement
						ON movements_items.id=last_movement.id
					SET movements_items.id_fotografia='$id_fotografia'";
	echo "updates: $query<br>";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
}
((mysqli_free_result($items_result) || (is_object($items_result) && (get_class($items_result) == "mysqli_result"))) ? true : false);
$query="COMMIT;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>
