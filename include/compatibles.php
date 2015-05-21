<?
function compatibles_menu()
{
	global $myhost,$myuser,$mypass,$mydb;
	require_once("mysql.php");

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$subsystems=array();
	$sql="SELECT DISTINCT location FROM fotografia ORDER BY location";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($row=mysqli_fetch_assoc($result))
		$subsystems[$row["location"]]=$row["location"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

?>
		<fieldset>
		<legend>ASTA System Snapshot - Compatible parts management </legend>
		<table>
			<tr>
				<td>Select Subsystem</td>
				<td>
					<select name="snapshotSubsystemSelect" id="snapshotSubsystemSelect"							
						onchange="updateCompatibles()">
						<option value="%">all</option>
<?
	foreach($subsystems as $name=>$subsystem)
	{?>
		<option value="<?=$subsystem?>"><?=$subsystem?></option>
	<?}?>
					</select>
				</td>
			</tr>
		</table>
		</fieldset>

	<div id="compatibles_output">
	</div>
<?
}

function updateCompatibles($subsystem="%")
{
	global $myhost,$myuser,$mypass,$mydb;
	require_once("mysql.php");

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$fotografiaArray=array();
	$query="SELECT compatible_parts.id_compatible,
					fotografia.location,parts.description,
					GROUP_CONCAT(DISTINCT 
						CONCAT(parts.id,'|',parts.pn_supplier,'|',parts.pn_manufacturer,'|',parts.description) 
						ORDER BY parts.id SEPARATOR 'Ł') AS parts
			FROM
				compatible_parts 
					JOIN parts ON compatible_parts.id_parts=parts.id
					JOIN fotografia ON compatible_parts.id_compatible=fotografia.id_compatible
			WHERE fotografia.location LIKE '$subsystem'
			GROUP BY compatible_parts.id_compatible,fotografia.location
			ORDER BY fotografia.location,parts.description";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));


	printf("<table id='table_id'>
				<tr class='even'>
					<td class='fieldTitle'>location</td>
					<td class='fieldTitle'>description</td>
					<td class='fieldTitle'>parts</td>
				</tr>");

	$levels=array();
	while($row=mysqli_fetch_assoc($result))
	{
		$id_compatible=$row["id_compatible"];
		$parts=$row["parts"];
		$parts_exploded=explode("Ł",$parts);
		$parts_table="<table>
						  <tr class='even'>
								<td class='fieldTitle'></td>
								<td class='fieldTitle'>pn_supplier</td>
								<td class='fieldTitle'>pn_manufacturer</td>
								<td class='fieldTitle'>description</td>
						  </tr>
						  ";
		foreach($parts_exploded as $part)
		{
			$part_exploded=explode("|",$part);
			$id_parts=$part_exploded[0];
			$location=$row["location"];
			$parts_table.="<tr id='row_".$id_compatible."_".$id_parts."'>
								<td><input type='button' value='delete' 
									onclick='deleteCompatible($id_compatible,$id_parts)'>
								</td>
								<td>".$part_exploded[1]."</td>
								<td>".$part_exploded[2]."</td>
								<td>".$part_exploded[3]."</td>
							</tr>";
			
		}
		$parts_table.="<tr>
							<td colspan='4'>
								<input type='button' value='add'
									id='add_".$id_compatible."_".$location."' disabled>
								<input size='80' maxlength='80' type='text' id='pn_".$id_compatible."_".$location."'>
							</td>
						</tr>";
		$parts_table.="</table>";
		printf("	<tr class='odd'>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
					</tr>",
					$location,
					$row["description"],
					$parts_table);
	}
	printf("</table>");
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
?>
<div id="DivToShow">
</div>
<script>
	var mouseX;
	var mouseY;
	$(document).mousemove( function(e) {
	   mouseX = e.pageX; 
	   mouseY = e.pageY;
	});  
	$(".replacement").mouseover(function()
	{
		var id=$(this).attr("id").split("_")[1];
		$("#DivToShow").html($('#rep_'+id).html());
		if($("#DivToShow").html().length)
			$("#DivToShow").css({'top':mouseY+20,'left':mouseX-320}).show();
		else
			$("#DivToShow").hide();
	});
	$(".replacement").mouseout(function()
	{
		$("#DivToShow").html("");
		$('#DivToShow').hide();
	});
</script>
<?
}

$func=$_REQUEST["func"];
if($func=="updateCompatibles")
{
	$subsystem=$_REQUEST["subsystem"];
	updateCompatibles($subsystem);
}
elseif($func=="addCompatible")
{
	$id_compatible=$_POST["id_compatible"];
	$id_parts=$_POST["id_parts"];
	if(addCompatible($id_compatible,$id_parts))
		echo "ok";
}
elseif($func=="deleteCompatible")
{
	$id_compatible=$_POST["id_compatible"];
	$id_parts=$_POST["id_parts"];
	if(deleteCompatible($id_compatible,$id_parts))
		echo "ok";
}

function addCompatible($id_compatible,$id_parts)
{
	require_once("mysql.php");

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$sql="INSERT INTO compatible_parts(id_compatible,id_parts)
		VALUES ('$id_compatible','$id_parts')";
	$out=mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	return $out;
}

function deleteCompatible($id_compatible,$id_parts)
{
	require_once("mysql.php");

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$sql="DELETE FROM compatible_parts WHERE
			id_compatible='$id_compatible' 
			AND id_parts='$id_parts'";
	$out=mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	return $out;
}

?>

