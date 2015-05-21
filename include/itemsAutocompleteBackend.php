<?php

/* connect to database */
require_once('mysql.php');
require_once('functions.php');
ini_set ('session.name', 'magazzino');
/* per avere la roba del $_SESSION */
session_start();

$excluded=rtrim($_GET["exclude"],",");
if(!strlen($excluded))
	$excluded="''";
$q = strtolower($_GET["q"]);
if(!$q)
	return;

$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
((bool)mysqli_query($conn, "USE " . $mydb));

$linkAppend="";
if(isset($_GET["from"]))
{
	$linkAppend=" AND items.id_places='".$_GET["from"]."' ";
	if($_REQUEST["mustMatch"]=="0")
		$whereAppend="AND (items.id_places='".$_GET["from"]."' 
			OR parts.id_suppliers='".$_GET["from"]."') ";
	else
		$whereAppend="AND items.id_places='".$_GET["from"]."' ";
}
else
	$whereAppend="";

$whereAppend.=" AND parts.id_simulators=".$_SESSION["simulator_id"];

if(!isset($_GET["sn"]))
{
	$sql =	"(SELECT parts.pn_manufacturer AS parts_pn,
					parts.description AS parts_description,
					parts.id AS id_parts, 
					count(items.id) AS conta,
					parts.pn_supplier AS parts_pn_other,
					1 as manuf 
					FROM parts 
						LEFT JOIN items 
						ON parts.id=items.id_parts 
						$linkAppend 
						WHERE parts.pn_manufacturer LIKE '%".$q."%' 
					$whereAppend 
					AND parts.id NOT IN ($excluded) 
					GROUP BY parts.id)
			UNION 
			(SELECT parts.pn_supplier AS parts_pn,
					parts.description AS parts_description,
					parts.id AS id_parts,
					count(items.id) AS conta,
					parts.pn_manufacturer AS parts_pn_other,
					0 as manuf 
					FROM parts 
						LEFT JOIN items 
						ON parts.id=items.id_parts 
						$linkAppend 
					WHERE parts.pn_supplier LIKE '%".$q."%' 
					$whereAppend 
					AND parts.id NOT IN ($excluded)
					GROUP BY parts.id) 
				ORDER BY (conta>0) desc,parts_pn";
}
else
{
		$sql =	"SELECT	items.sn AS parts_sn,
					parts.description AS parts_description,
					items.id AS items_id 
					FROM parts 
						LEFT JOIN items 
						ON parts.id=items.id_parts 
						WHERE items.sn LIKE '%".$q."%' 
						$whereAppend 
					AND parts.id NOT IN ($excluded) 
			ORDER BY parts_sn";
}
$logfile = fopen("log.log", "a+"); 
fwrite($logfile,$sql."\r\n\r\n");
fclose($logfile);

$resultString="";

if( $result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) )
{
	$arrayResult=array();
	while($row = mysqli_fetch_array($result))
	{
		$arrayResult[$row[2]]=array($row[0],$row[1],$row[3],$row[4],$row[5]);
	}
	foreach($arrayResult as $k=>$v)
		$resultString .= $v[0]."|".nlTobr($v[1])."|".
			$k."|".$v[2]."|".$v[3]."|".$v[4]."\n";
}
else
	die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));


echo $resultString;
?>
