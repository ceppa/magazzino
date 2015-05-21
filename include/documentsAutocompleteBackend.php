<?php

/* connect to database */
require_once('mysql.php');
require_once('functions.php');
ini_set ('session.name', 'magazzino');
/* per avere la roba del $_SESSION */
session_start();

$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
((bool)mysqli_query($conn, "USE " . $mydb));

$excluded=rtrim($_GET["exclude"],",");
if(!strlen($excluded))
	$excluded="''";
$q = strtolower($_GET["q"]);

$whereAppend.=" AND documents.id_simulators=".$_SESSION["simulator_id"];

$sql =	"SELECT DISTINCT documents.filename AS filename,"
				.	"documents.description AS description,"
				.	"documents.id AS id_documents "
				.	"FROM documents "
				.	"WHERE (documents.filename LIKE '%".$q."%' "
				.	"OR documents.description LIKE '%".$q."%') "
				.	"$whereAppend "
				.	"AND documents.id NOT IN ($excluded) "
			."ORDER BY filename";

if( $result = mysqli_query($GLOBALS["___mysqli_ston"], $sql) )
{
	$resultString ="";
	while($row = mysqli_fetch_array($result))
		$resultString .= $row[0]."|".nlTobr($row[1])."|".$row[2]."\n";
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}
else
	die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

echo $resultString;
?>
