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


$items_in_foto=array();

$sql="SELECT t.* FROM
	(
		SELECT fotografia.id,
				fotografia.ref_design,
				IFNULL(movements_items.id_fotografia,0) AS fotografia,
				items2.id AS id_items,
				parts.pn_supplier,
				items2.sn AS sn
		FROM items JOIN compatible_parts 
			ON items.id_parts=compatible_parts.id_parts AND items.id='$itemId'
			JOIN parts ON compatible_parts.id_parts=parts.id 
			JOIN fotografia ON fotografia.id_compatible=compatible_parts.id_compatible
			JOIN items AS items2 ON compatible_parts.id_parts=items2.id_parts
			JOIN movements_items ON items2.id=movements_items.id_items 
					AND movements_items.id_fotografia=fotografia.id 
				LEFT JOIN movements ON movements_items.id_movements = movements.id 
			ORDER BY items.id,movements.insert_date DESC 
	) t 
	GROUP BY t.id_items,t.id;";
$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
	or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
while($row=mysqli_fetch_assoc($result))
	$items_in_foto[$row["id"]]=$row["id_items"];
((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


$query="SELECT items.id, parts.pn_supplier, parts.description 
			FROM items LEFT JOIN parts ON items.id_parts=parts.id
			WHERE items.sn!='' AND items.id_places IN 
	(SELECT DISTINCT id_places FROM fotografia)";
$res=mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die("Query error $query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
while($r=mysqli_fetch_assoc($res))
{
	$itemId=$r["id"];

	$fotografiaArray=array();
	$query="SELECT fotografia.id,fotografia.ref_design,fotografia.location
		FROM items 
			JOIN compatible_parts ON items.id_parts=compatible_parts.id_parts
			JOIN fotografia ON compatible_parts.id_compatible=fotografia.id_compatible
		WHERE items.id='$itemId'
			AND items.id_places=fotografia.id_places";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error $query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	while($row=mysqli_fetch_assoc($result))
	{
		if($items_in_foto[$row["id"]]!=$itemId)
			$fotografiaArray[$row["id"]]=0;
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	if(count($fotografiaArray)==1)
	{
		echo $r["pn_supplier"]." - ".$r["description"]."<br>";
		$fotografiaArray=array_flip($fotografiaArray);
		$id_fotografia=$fotografiaArray[0];
		$q="UPDATE movements_items INNER JOIN 
				(
					SELECT movements_items.id
					FROM movements_items 
						LEFT JOIN movements ON 
							movements_items.id_movements=movements.id
					WHERE movements_items.id_items='$itemId'
					ORDER BY movements.insert_date DESC
					LIMIT 1
				) AS last_movement
					ON movements_items.id=last_movement.id
				SET movements_items.id_fotografia='$id_fotografia'";
		echo "$q<br>";
		mysqli_query($GLOBALS["___mysqli_ston"], $q)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	}
}
((mysqli_free_result($res) || (is_object($res) && (get_class($res) == "mysqli_result"))) ? true : false);

$query="COMMIT;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>
