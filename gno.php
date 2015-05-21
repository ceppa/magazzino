<?
$gino=array(array(2933,'BE',1),
array(2989,'BE',1),
array(3048,'AB',32),
array(3049,'AB',32),
array(3050,'AB',32),
array(3051,'AB',32),
array(3052,'AB',32),
array(3053,'AB',32),
array(3054,'AB',32),
array(3055,'AB',32),
array(3056,'AB',32),
array(3057,'AB',32),
array(3058,'AB',32),
array(3059,'AB',32),
array(3064,'BB',1),
array(3065,'BB',1),
array(3066,'BB',1),
array(3067,'BB',1),
array(3068,'BB',80),
array(3069,'BB',1),
array(3070,'BB',1),
array(3071,'BB',1),
array(3072,'BB',1),
array(3073,'BB',1),
array(3074,'BB',2),
array(3075,'BB',1),
array(3076,'BB',1),
array(3077,'BB',1),
array(3078,'BB',1),
array(3079,'BB',1),
array(3080,'BB',1));

$movements=array();

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

foreach($gino as $parts)
{
	$id_parts=$parts[0];
	$location=$parts[1];
	$qty=$parts[2];
	$query="SELECT  id FROM items WHERE id_places=45 AND id_parts='$id_parts' AND location='$location'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$qty_ware=mysqli_num_rows($result);
	if($qty_ware<$qty)
		echo "NONONONO";
	echo "id_parts:$id_parts, location:$location, required:$qty, found:$qty_ware<br>";

	$i=0;
	while($i<$qty && ($item=mysqli_fetch_assoc($result)))
	{
		$i++;
		$id_items=$item["id"];
		$query="SELECT id_movements FROM movements_items WHERE id_items='$id_items'";
		$rm=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($mm=mysqli_fetch_assoc($rm))
			$movements[$mm["id_movements"]]=1;
		$query="DELETE FROM items WHERE id='$id_items'";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		$query="DELETE FROM movements_items WHERE id_items='$id_items'";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		
//		print_r($movements);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}
	print_r($movements);
	echo "<br>";
	foreach($movements as $id_movement=>$foo)
	{
		$query="SELECT count(movements_items.id) as qty FROM 
			movements LEFT JOIN movements_items ON movements.id=movements_items.id_movements
			WHERE id_movements='$id_movement'
			GROUP BY movements.id";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$r=mysqli_fetch_assoc($result);
		$qty=$r["qty"];
		echo "movement: $id_movement, qty: $qty<br>";
	}
		
$query="COMMIT;";
mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die_($conn,"$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>

