<?php
require_once('mysql.php');

function pdfstring($string)
{
	$out="";
	for($i=0;$i<strlen($string);$i++)
	{
		$c=substr($string,$i,1);
		if(ord($c)>128)
			$out.="°";
		else
			$out.=$c;
	}
	return $out;
}


$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
	or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
((bool)mysqli_query($conn, "USE " . $mydb))
	or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

$sql="SELECT id,description FROM fotografia";

$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
	or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
while($row=mysqli_fetch_assoc($result))
{
	$id=$row["id"];
	$description=pdfstring($row["description"]);
	if(strstr($description,"°"))
	{
		echo "$description<br>";
		$query="UPDATE fotografia SET description='$description' WHERE id='$id'";
		mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}
}
((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
?>