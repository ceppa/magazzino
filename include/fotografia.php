<?
function get_items_in_foto($conn,$itemId)
{
	// array con items compatibili con itemId già presenti in fotografia
	$items_in_foto=array();
	
		$sql="SELECT t . * 
			FROM (
				SELECT items.id AS id_items, IFNULL( movements_items.id_fotografia, 0 ) AS id
				FROM items
					LEFT JOIN movements_items ON items.id = movements_items.id_items
					LEFT JOIN movements ON movements_items.id_movements = movements.id
				WHERE items.id
				IN (
					SELECT DISTINCT items2.id
					FROM items
					JOIN compatible_parts ON items.id_parts = compatible_parts.id_parts
						AND items.id =  '$itemId'
					JOIN compatible_parts comps2 ON compatible_parts.id_compatible = comps2.id_compatible
					JOIN items AS items2 ON comps2.id_parts = items2.id_parts
				)
				ORDER BY movements.insert_date DESC
			)t
			GROUP BY t.id_items
			HAVING t.id>0";
	$result = mysqli_query($conn, $sql)
		or die("$sql<br>".((is_object($conn)) ? mysqli_error($conn) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$items_in_foto[$row["id"]]=$row["id_items"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	return $items_in_foto;
}

function get_fotografia_array($conn,$itemId)
{
	// array con items compatibili con itemId
	$fotografiaArray=array(0=>"---");
	$query="SELECT fotografia.id,fotografia.ref_design,fotografia.location
		FROM items 
			JOIN compatible_parts ON items.id_parts=compatible_parts.id_parts
			JOIN fotografia ON compatible_parts.id_compatible=fotografia.id_compatible
		WHERE items.id='$itemId'
			AND items.id_places=fotografia.id_places";
	$result=mysqli_query($conn, $query)
		or die("Query error $query<br>".((is_object($conn)) ? mysqli_error($conn) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	while($row=mysqli_fetch_assoc($result))
	{
		$line=$row["ref_design"];
		if(strlen($line))
			$line.=" - ";
		$line.=$row["location"];
		$fotografiaArray[$row["id"]]=$line;
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	return $fotografiaArray;
}

function getChildrenTree($id_fotografia,&$children)
{
	$sql="SELECT id FROM fotografia WHERE parent_id='$id_fotografia'";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
	while($row=mysqli_fetch_assoc($result))
	{
		$id=$row["id"];
		$children.=",$id";
		$children=ltrim($children,",");
		getChildrenTree($id,$children);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}

function get_fotografia_id($itemId)
{
	$out=0;
	$sql = "SELECT t.* FROM
			(
				SELECT items.id,
					 IFNULL(movements_items.id_fotografia,0) AS fotografia
				FROM items
					LEFT JOIN movements_items ON items.id = movements_items.id_items
					LEFT JOIN movements ON movements_items.id_movements = movements.id 
				WHERE items.id='$itemId' 
				ORDER BY movements.insert_date DESC
			)t
			GROUP BY t.id";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
	if(mysqli_num_rows($result)>0)
	{
		$row=mysqli_fetch_assoc($result);
		$out=$row["fotografia"];
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	return $out;
}

function get_item_id($id_fotografia)
{
	$out=array();
	$query="SELECT t.fotografia, 
				t.id_items,
				t.id_parts,
				t.pn_supplier,
				t.description
			FROM (
				SELECT IFNULL( movements_items.id_fotografia, 0 ) AS fotografia, 
					items.id AS id_items,
					items.id_parts AS id_parts,
					parts.pn_supplier,
					parts.description 
				FROM items
					LEFT JOIN parts ON items.id_parts = parts.id
					LEFT JOIN movements_items ON items.id = movements_items.id_items
					LEFT JOIN movements ON movements.id = movements_items.id_movements
					LEFT JOIN compatible_parts ON items.id_parts = compatible_parts.id_parts
					LEFT JOIN fotografia ON compatible_parts.id_compatible = fotografia.id_compatible
						AND fotografia.id = movements_items.id_fotografia
				ORDER BY movements.insert_date DESC
				)t
			GROUP BY t.id_items
			HAVING t.fotografia =$id_fotografia";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
	if(mysqli_num_rows($result)==1)
	{
		$row=mysqli_fetch_assoc($result);
		$description=str_replace("\n"," ",$row["description"]);
		$description=str_replace('"',"'",$description);

		$out=array(
				"id_items"=>$row["id_items"],
				"id_parts"=>$row["id_parts"],
				"pn_supplier"=>$row["pn_supplier"],
				"description"=>$description);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	return $out;
}

function get_children_items($itemId)
{
	$children="";
	$out="";
	$id_fotografia=get_fotografia_id($itemId);
	if($id_fotografia>0)
		getChildrenTree($id_fotografia,$children);
	if(strlen($children))
	{
		$exploded=explode(",",$children);
		foreach($exploded as $id_fotografia_child)
		{
			$itemId_child=get_item_id($id_fotografia_child);
			if(count($itemId_child))
			{
				$curitem=sprintf("%s§%s§%s§%s",
					$itemId_child["id_items"],
					$itemId_child["id_parts"],
					$itemId_child["pn_supplier"],
					$itemId_child["description"]);
				$out=ltrim(sprintf("%s,%s",
					$out,$curitem),",");
			}
		}
	}
	return $out;
}

function report_fotografia_menu()
{
	global $myhost,$myuser,$mypass,$mydb;
	require_once("mysql.php");

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$systems=array();
	$sql="SELECT DISTINCT places.name,places.description FROM places 
		JOIN fotografia ON places.id=fotografia.id_places 
		WHERE places.id_places_types=1 ORDER BY places.name";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($row=mysqli_fetch_assoc($result))
		$systems[$row["name"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$subsystems=array();
	$sql="SELECT DISTINCT location FROM fotografia ORDER BY location";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $sql);
	while($row=mysqli_fetch_assoc($result))
		$subsystems[$row["location"]]=$row["location"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

?>
		<fieldset>
		<legend>ASTA System Snapshot</legend>
		<table>
			<tr>
				<td>Select Device</td>
				<td>
					<select name="snapshotSystemSelect" id="snapshotSystemSelect">
						<option value="%">all</option>
<?
	foreach($systems as $name=>$system)
	{?>
		<option value="<?=$name?>"><?=$system?></option>
	<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Select Subsystem</td>
				<td>
					<select name="snapshotSubsystemSelect" id="snapshotSubsystemSelect">
						<option value="%">all</option>
<?
	foreach($subsystems as $name=>$subsystem)
	{?>
		<option value="<?=$subsystem?>"><?=$subsystem?></option>
	<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<input type="button" name="print_video" 
						value="display" onclick="update_fotografia_output()">
					<input type="button" name="print_pdf"
						value="print" onclick="print_fotografia()" >
					<input type="button" name="print_csv"
						value="export"  onclick="export_fotografia()" >
				</td>
			</tr>
		</table>
		</fieldset>

	<div id="fotografia_output">
	</div>
<?
}

function do_report_fotografia_array($system="%",$subsystem="%")
{
	global $myhost,$myuser,$mypass,$mydb;
	require_once("mysql.php");

	$out=array();
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	mysqli_query($GLOBALS["___mysqli_ston"], "SET NAMES utf8");

	$fotografiaArray=array();
	$query="SELECT t.fotografia, 
			t.pn_supplier, 
			t.pn_manufacturer, 
			t.description, 
			t.sn
		FROM (
			SELECT IFNULL( movements_items.id_fotografia, 0 ) AS fotografia, 
				parts.pn_supplier, 
				parts.pn_manufacturer, 
				parts.description, 
				items.sn, 
				items.id AS id_items
			FROM items
				LEFT JOIN parts ON items.id_parts = parts.id
				LEFT JOIN movements_items ON items.id = movements_items.id_items
				LEFT JOIN movements ON movements.id = movements_items.id_movements
				LEFT JOIN compatible_parts ON items.id_parts = compatible_parts.id_parts
				LEFT JOIN fotografia ON compatible_parts.id_compatible = fotografia.id_compatible
					AND fotografia.id = movements_items.id_fotografia
			ORDER BY movements.insert_date DESC
			)t
		GROUP BY t.id_items
		HAVING t.fotografia >0";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$fotografiaArray[$row["fotografia"]]=$row;
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$sostituzioni=array();
	$query="SELECT fotografia.id, 
				COUNT( movements_items.id_items ) -1 AS conta, 
				GROUP_CONCAT( CAST( movements.insert_date AS CHAR ) 
					ORDER BY movements.insert_date ASC) AS dates, 
				GROUP_CONCAT(parts.pn_supplier
					ORDER BY movements.insert_date ASC) AS pns,
				GROUP_CONCAT(items.sn 
					ORDER BY movements.insert_date ASC) AS sns
			FROM fotografia
				LEFT JOIN movements_items ON fotografia.id = movements_items.id_fotografia
				LEFT JOIN movements ON movements_items.id_movements = movements.id
				LEFT JOIN items ON movements_items.id_items = items.id
				LEFT JOIN parts ON items.id_parts = parts.id
			GROUP BY fotografia.id
			HAVING conta >0";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$sostituzioni[$row["id"]]=$row;
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$query="SELECT fotografia.id,fotografia.parent_id,
			fotografia.description,
			fotografia.ref_design,
			fotografia.location,
			places.name AS place
			FROM fotografia
				LEFT JOIN places ON fotografia.id_places=places.id
			WHERE places.name LIKE '$system'
				AND fotografia.location LIKE '$subsystem' 
			ORDER BY fotografia.id_places,fotografia.ordine";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$levels=array();
	while($row=mysqli_fetch_assoc($result))
	{
		$foto_id=$row["id"];
		$parent_id=(int)$row["parent_id"];
		$livello=0;
		if($parent_id>0)
		{
			if(isset($levels[$parent_id]))
				$livello=$levels[$parent_id]+1;
			else
				$livello=1;
			$levels[$foto_id]=$livello;
		}
		$spaces="";
		if($livello>0)
		{
			if($livello>1)
				$spaces=str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;",$livello-1);
			$spaces.="&nbsp;&nbsp;&nbsp;&nbsp;";
		}
		$pns="";
		$pnm="";
		$description="";
		$sn="";
		$ss=0;
		if(isset($fotografiaArray[$foto_id]))
		{
			$pns=$fotografiaArray[$foto_id]["pn_supplier"];
			$pnm=$fotografiaArray[$foto_id]["pn_manufacturer"];
			$description=$fotografiaArray[$foto_id]["description"];
			$sn=$fotografiaArray[$foto_id]["sn"];

			if(isset($sostituzioni[$foto_id]))
				$ss=$sostituzioni[$foto_id];
		}

		$out[$foto_id]=array(
				"foto_description"=>$spaces.$row["description"],
				"ref_design"=>$row["ref_design"],
				"location"=>$row["location"],
				"place"=>$row["place"],
				"description"=>$description,
				"pns"=>$pns,
				"pnm"=>$pnm,
				"sn"=>$sn,
				"sostituzioni"=>$ss
			);
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	return $out;
}

function do_report_fotografia($system="%",$subsystem="%")
{
	$lines=do_report_fotografia_array($system,$subsystem);

	printf("<table id='table_id'>
				<tr class='even'>
					<td class='fieldTitle' colspan='4'>
						STRUTTURA BSD
					</td>
					<td class='fieldTitle' colspan='3'>
						FOTOGRAFIA
					</td>
					<td style='background-color:#fff;border-right-width:0px;border-top-width:0px;'>
						&nbsp;
					</td>
				</tr>
				<tr class='even'>
					<td class='fieldTitle'>place</td>
					<td class='fieldTitle'>location</td>
					<td class='fieldTitle'>description</td>
					<td class='fieldTitle'>position</td>
					<td class='fieldTitle'>pns</td>
					<td class='fieldTitle'>pnm</td>
					<td class='fieldTitle'>sn</td>
					<td class='fieldTitle'>rep</td>
				</tr>");

	foreach($lines as $foto_id=>$line)
	{
		$foto_description=$line["foto_description"];
		$ref_design=$line["ref_design"];
		$location=$line["location"];
		$place=$line["place"];
		$description=$line["description"];
		$pns=$line["pns"];
		$pnm=$line["pnm"];
		$sn=$line["sn"];
		$sostituzioni=$line["sostituzioni"];

		$sostituzioni_cell="-";
		if(is_array($sostituzioni))
		{
			$dates=$sostituzioni["dates"];
			$spns=$sostituzioni["pns"];
			$sns=$sostituzioni["sns"];
			$dates_array=explode(",",$dates);
			$sns_array=explode(",",$sns);
			$spns_array=explode(",",$spns);
			$body="<table>
					<tr>
						<td>data</td>
						<td>pn</td>
						<td>sn</td>
					</tr>";
			foreach($dates_array as $id=>$date)
			{
				$date_exploded=explode(" ",$date);
				$date=$date_exploded[0];
				$body.=sprintf("<tr>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						</tr>"
					,$date,$spns_array[$id],$sns_array[$id]);
			}
			$body.="</table>";
			$sostituzioni_cell=sprintf("
				%d
				<div id='rep_%d' style='display:none'>
					%s
				</div>",
				$sostituzioni["conta"],
				$foto_id,
				$body);
		}

		printf("	<tr class='odd'>
						<td style='white-space:nowrap;'>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td>%s</td>
						<td class='replacement'
						id='cel_%d'>%s</td>
					</tr>",
					$place,
					$location,
					$foto_description,
					$ref_design,
					$pns,
					$pnm,
					$sn,
					$foto_id,
					$sostituzioni_cell);
	}
	printf("</table>");
?>
<div id="DivToShow">
</div>
<script>
	$(".replacement").mouseover(function(e)
	{
		
		var id=$(this).attr("id").split("_")[1];
		var p=$(this).offset();
		var mouseX = e.pageX; 
		var mouseY = e.pageY;

		$("#DivToShow").html($('#rep_'+id).html());
		if($("#DivToShow").html().length)
			$("#DivToShow").css({'top':p.top,'left':p.left-300}).show();
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

function do_print_fotografia($system="%",$subsystem="%",$csv)
{
	$mediumWidth=25;
	$shortWidth=15;
	$longWidth=200;
	ini_set('include_path',get_include_path().PATH_SEPARATOR.'/home/hightecs/php');
	$lines=do_report_fotografia_array($system,$subsystem);

	foreach($lines as $foto_id=>$line)
	{
		$sostituzioni=$line["sostituzioni"];

		$sostituzioni_cell="-";
		if(is_array($sostituzioni))
		{
			$dates=$sostituzioni["dates"];
			$sns=$sostituzioni["sns"];
			$dates_array=explode(",",$dates);
			$sns_array=explode(",",$sns);
			$body="";
			foreach($dates_array as $id=>$date)
				$body=sprintf("%s\n%s - %s",
					$body,$date,$sns_array[$id]);
			$body=ltrim($body,"\n");
			$sostituzioni_cell=$body;
		}
		$lines[$foto_id]["sostituzioni_cell"]=$sostituzioni_cell;
	}

	$columns[]=array("name"=>"place","text"=>"place","width"=>"$shortWidth","align"=>"L");
	$columns[]=array("name"=>"location","text"=>"location","width"=>"$shortWidth","align"=>"L");
	$columns[]=array("name"=>"foto_description","text"=>"description","width"=>"$longWidth","align"=>"L");
	$columns[]=array("name"=>"ref_design","text"=>"position","width"=>"$shortWidth","align"=>"L");
	$columns[]=array("name"=>"pns","text"=>"pns","width"=>"$shortWidth","align"=>"L");
	$columns[]=array("name"=>"pnm","text"=>"pnm","width"=>"$shortWidth","align"=>"L");
	$columns[]=array("name"=>"sn","text"=>"sn","width"=>"$shortWidth","align"=>"L");
	$columns[]=array("name"=>"sostituzioni_cell","text"=>"rep","width"=>"$shortWidth","align"=>"C","newline"=>true);


	if($csv)
	{
		$out=$lines;
		require_once("report_csv.php");
	}
	else
	{
		require_once("report_pdf.php");
		do_report($columns,$lines,"fotografia",'L');
	}
}



$func=$_REQUEST["func"];
switch($func)
{
	case "doReportFotografia":
		$system=$_REQUEST["system"];
		$subsystem=$_REQUEST["subsystem"];
		do_report_fotografia($system,$subsystem);
		break;
	case "doPrintFotografia":
		$system=$_REQUEST["system"];
		$subsystem=$_REQUEST["subsystem"];
		do_print_fotografia($system,$subsystem,0);
		break;
	case "doExportFotografia":
		$system=$_REQUEST["system"];
		$subsystem=$_REQUEST["subsystem"];
		do_print_fotografia($system,$subsystem,1);
		break;
}

?>
