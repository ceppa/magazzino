<?php

/* connect to database */
require_once('mysql.php');
require_once('functions.php');

/* per avere la roba del $_SESSION */
ini_set ('session.name', 'magazzino');
session_start();

$func = $_REQUEST['func'];

if($func=='list')
{
	/* table page number */
	$page = $_POST['page'];
	/* records per page */
	$rp = $_POST['rp'];
	/* sort field */
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) $sortname = 'items.location';
	if (!$sortorder) $sortorder = 'desc';

	$sort = "ORDER BY $sortname $sortorder";

	if(!$page || !$rp)
	{
		$limit = "";
		$page = 1;
	}else
	{
		$start = (($page-1) * $rp);
		$limit = " LIMIT $start, $rp";
	}

	$query = $_REQUEST['query'];
	$qtype = $_REQUEST['qtype'];

	$where = "";
	$having= " HAVING 1";

	if($qtype) 
	{
		if($qtype == "pn" && $query!='')
			$where = " WHERE (parts.pn_manufacturer LIKE '%$query%' 
				OR parts.pn_supplier LIKE '%$query%') ";
		elseif($qtype == "sn" && $query!='') 
			$where = " WHERE items.sn LIKE '%$query%' ";
		elseif($qtype == "place" && $query!='') 
			$where = " WHERE places.name LIKE '%$query%' ";
		elseif($qtype == "description" && $query!='') 
			$where = " WHERE parts.description LIKE '%$query%' ";
		elseif($qtype == "location" && $query!='') 
			$where = " WHERE items.location LIKE '%$query%' ";
		elseif($qtype == "manufacturer" && $query!='') 
			$where = " WHERE manufacturers.name LIKE '%$query%' ";
		elseif($qtype == "supplier" && $query!='') 
			$where = " WHERE suppliers.name LIKE '%$query%' ";
		elseif($qtype == "to_repair" && $query!='') 
			$having.= " AND to_repair='$query' ";
		elseif($qtype == "replaced_itemId" && $query!='') 
			$where= "WHERE replaced_itemId='$query' ";
		else
			$where = " WHERE 1";
	}
	else
		$where = " WHERE 1";

	$where .= " AND parts.id_simulators='".$_SESSION["simulator_id"]."'";


	if(isset($_POST['whereString'])&&($_POST['whereString']!=''))
	{
		$where = " WHERE ".$_POST['whereString']." ";
		$where = str_replace('\"','"',$where);
	}

	$conn	=	($GLOBALS["___mysqli_ston"] = mysqli_connect(	$myhost, 
								$myuser, 
								$mypass));

	((bool)mysqli_query(
						$conn , "USE " . $mydb));

//	mysql_query("SET NAMES utf8");

	$sql =	"SELECT	items.id AS items_id,
					items.location AS items_location,
					items.sn AS items_sn,
					parts.description AS parts_name,
					owners.name AS owners_name,
					places.name AS places_name,
					manufacturers.name AS manufacturer_name,
					suppliers.name AS supplier_name,
					parts.pn_supplier AS parts_pn_supplier,
					parts.pn_manufacturer AS parts_pn_manufacturer,
					SUBSTRING(GROUP_CONCAT( movements_items.to_repair 
						ORDER BY movements.insert_date DESC SEPARATOR ''),1,1) AS to_repair,
					SUBSTRING(GROUP_CONCAT( IF(movements_items.replaced_itemId>0,1,0)
						ORDER BY movements.insert_date DESC SEPARATOR ''),1,1) AS replaced_itemId
			FROM items 
			LEFT JOIN parts ON items.id_parts = parts.id 
			LEFT JOIN owners ON items.id_owners = owners.id 
			LEFT JOIN places ON items.id_places = places.id 
			LEFT JOIN places AS manufacturers ON parts.id_manufacturers = manufacturers.id 
			LEFT JOIN places AS suppliers ON parts.id_suppliers = suppliers.id 
			LEFT JOIN movements_items ON items.id = movements_items.id_items
			LEFT JOIN movements ON movements.id = movements_items.id_movements 
			$where GROUP BY places_name, items_sn,
					owners_name,items_location,parts_name,
					parts_pn_manufacturer,parts_pn_supplier
			$having";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$total = mysqli_num_rows($result);

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$items_in_foto=array();
	$sql="SELECT t.fotografia,
					t.ref_design,
					t.location,
					t.id_items 
			FROM
			(
				SELECT IFNULL(movements_items.id_fotografia,0) AS fotografia,
						fotografia.ref_design,
						fotografia.location,
						movements_items.id_items 

					FROM items
						LEFT JOIN movements_items 
							ON items.id = movements_items.id_items
						LEFT JOIN movements 
							ON movements.id = movements_items.id_movements
						LEFT JOIN compatible_parts 
							ON items.id_parts = compatible_parts.id_parts
						LEFT JOIN fotografia 
							ON compatible_parts.id_compatible = fotografia.id_compatible
					AND fotografia.id = movements_items.id_fotografia
					ORDER BY movements.insert_date DESC 
			) t
			GROUP BY t.id_items;";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
	{
		if($row["fotografia"]>0)
		{
			$line=$row["ref_design"];
			if(strlen($line))
				$line.=" - ";
			$line.=$row["location"];
			$items_in_foto[$row["id_items"]]=$line;
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


	$sql =	"SELECT	items.id AS items_id,
					items.location AS items_location,
					items.sn AS items_sn,
					COUNT(DISTINCT items.id) AS items_quantity,
					parts.description AS parts_name,
					simulators.name AS simulators_name,
					owners.name AS owners_name,
					places.name AS places_name,
					manufacturers.name AS manufacturer_name,
					suppliers.name AS supplier_name,
					parts.pn_supplier AS parts_pn_supplier,
					parts.pn_manufacturer AS parts_pn_manufacturer,
					SUBSTRING(GROUP_CONCAT( movements_items.to_repair 
						ORDER BY movements.insert_date DESC SEPARATOR ''),1,1) AS to_repair,
					SUBSTRING(GROUP_CONCAT( IF(movements_items.replaced_itemId>0,1,0)
						ORDER BY movements.insert_date DESC SEPARATOR ''),1,1) AS replaced_itemId
			FROM items 
				LEFT JOIN parts ON items.id_parts = parts.id 
				LEFT JOIN simulators ON parts.id_simulators = simulators.id 
				LEFT JOIN owners ON items.id_owners = owners.id 
				LEFT JOIN places ON items.id_places = places.id 
				LEFT JOIN places AS manufacturers ON parts.id_manufacturers = manufacturers.id 
				LEFT JOIN places AS suppliers ON parts.id_suppliers = suppliers.id 
				LEFT JOIN movements_items ON items.id = movements_items.id_items
				LEFT JOIN movements ON movements.id = movements_items.id_movements 
		$where GROUP BY places_name, items_sn,
					owners_name,items_location,parts_name,
					parts_pn_manufacturer,parts_pn_supplier 
		$having 
			$sort $limit ";

	$logfile = fopen("log.log", "a+"); 
	fwrite($logfile,$sql."\r\n\r\n");

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	fwrite($logfile,((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."\r\n\r\n");

//	fwrite($logfile,print_r($items_in_foto,true));
//	fwrite($logfile,"\r\n\r\n");

	fclose($logfile);
//	$total = mysql_num_rows($result);

	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
	header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
	header("Cache-Control: no-cache, must-revalidate" );
	header("Pragma: no-cache" );
	header("Content-type: text/x-json");
	$json = "";
	$json .= "{\n";
	$json .= "page: $page,\n";
	$json .= "total: $total,\n";
	$json .= "rows: [";
	$rc = false;
	while ($row = mysqli_fetch_array($result)) 
	{
		$items_id=$row['items_id'];
		if(isset($items_in_foto[$items_id]))
			$fotografia_location=$items_in_foto[$items_id];
		else
			$fotografia_location="---";

		$part_name=nlTobr(trim($row['parts_name']));
		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['items_id']."',";
		$json .= "cell:['".addslashes($part_name==""?"---":$part_name)."'";
		$json .= ",'".addslashes(($row['supplier_name']=="")?"---":$row['supplier_name'])."'";
		$json .= ",'".addslashes(($row['parts_pn_supplier']=="")?"---":$row['parts_pn_supplier'])."'";
		$json .= ",'".addslashes(($row['manufacturer_name']=="")?"---":$row['manufacturer_name'])."'";
		$json .= ",'".addslashes(($row['parts_pn_manufacturer']=="")?"---":$row['parts_pn_manufacturer'])."'";
		$json .= ",'".addslashes(($row['items_quantity']=="")?"---":$row['items_quantity'])."'";
		$json .= ",'".addslashes(($row['items_sn']=="")?"---":$row['items_sn'])."'";
		$json .= ",'".addslashes(($row['places_name']=="")?"---":$row['places_name'])."'";
		$json .= ",'".addslashes(($row['items_location']=="")?"---":$row['items_location'])."'";
		$json .= ",'".addslashes($fotografia_location)."'";
		$json .= ",'".$row['to_repair']."'";
		$json .= ",'".$row['replaced_itemId']."'";
//		$json .= ",'".addslashes(($row['simulators_name']=="")?"---":$row['simulators_name'])."'";
		$json .= "]}";
		$rc = true;
	}
	$json .= "]\n";
	$json .= "}";

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	echo $json;
}
elseif($func=='detailItemForm')
{
	$itemId=$_POST["itemId"];
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));


	$query = "SELECT items.* 
				,CONCAT(user_creator.name,' ',user_creator.surname) AS creator
				,CONCAT(user_updater.name,' ',user_updater.surname) AS updater
				,parts.description AS parts_description
				,parts.pn_manufacturer AS parts_pn_manufacturer
				,parts.pn_supplier AS parts_pn_supplier
				,simulators.name AS simulator
			FROM items
				LEFT JOIN parts ON items.id_parts=parts.id
				LEFT JOIN simulators ON parts.id_simulators=simulators.id
				LEFT JOIN users user_creator ON items.id_users_creator = user_creator.id
				LEFT JOIN users user_updater ON items.id_users_updater = user_updater.id
			WHERE items.id='$itemId'";

	/*execute part query*/
	$items = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error $query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$item = mysqli_fetch_assoc($items);

	if(strlen(trim($item["creator"])))
		$creator=ucwords(trim($item["creator"]));
	else
		$creator="none";
	if(strlen(trim($item["updater"])))
		$updater=ucwords(trim($item["updater"]));
	else
		$updater="none";
	((mysqli_free_result($items) || (is_object($items) && (get_class($items) == "mysqli_result"))) ? true : false);


	$query="SELECT movements.insert_date,
					movements.note,
					places_source.name as source,
					places_dest.name as dest,
					movements_items.to_repair, 
					GROUP_CONCAT(DISTINCT CONCAT(documents.id,'§',
						documents.filename,'§',documents.description)) as docs 
					FROM movements LEFT JOIN movements_items 
						ON movements.id=movements_items.id_movements
					LEFT JOIN movements_documents 
						ON movements.id=movements_documents.id_movements
					LEFT JOIN documents
						ON movements_documents.id_documents=documents.id
					LEFT JOIN places places_source
						ON movements.id_places_from=places_source.id
					LEFT JOIN places places_dest
						ON movements.id_places_to=places_dest.id
				WHERE movements_items.id_items='$itemId'
				GROUP BY movements.id
				ORDER BY movements.insert_date DESC,movements.id DESC";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

?>
	<fieldset id="item_header_fieldset">
		<legend>Item header</legend>
		<table>
			<tr>
				<td class="fieldLabel">
					Manufacturer P/N
				</td>
				<td>
					<?=$item["parts_pn_manufacturer"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Supplier P/N
				</td>
				<td>
					<?=$item["parts_pn_supplier"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Description
				</td>
				<td>
					<?=$item["parts_description"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Serial Number
				</td>
				<td>
					<?=$item["sn"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Location / note
				</td>
				<td>
					<?=$item['location']?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Owner
				</td>
				<td>
					<?=$owner["name"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Created by
				</td>
				<td>
					<?php echo $creator ?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Updated by
				</td>
				<td>
					<?php echo $updater ? $updater:$creator; ?>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="item_header_fieldset">
		<legend>Item movements history</legend>
		<table class="movement_details">
			<tr class="header">
				<td>
					<b>Date</b>
				</td>
				<td>
					<b>From</b>
				</td>
				<td>
					<b>To</b>
				</td>
				<td>
					<b>Note</b>
				</td>
				<td>
					<b>Documents</b>
				</td>
				<td>
					<b>To fix</b>
				</td>
			</tr>
<?
	while($row=mysqli_fetch_assoc($result))
	{
		$exploded=explode(",",$row["docs"]);
		$docs="";
	
		foreach($exploded as $line)
		{
			$attachment=explode("§",$line);
			$id=$attachment[0];
			$desc=$attachment[2];
			$link="include/movements.php?func=showDoc&amp;id=$id";
			$docs.="<a href='$link'>$desc</a><br />";
		}
		

?>
			<tr>
				<td>
					<?=$row["insert_date"]?>
				</td>
				<td>
					<?=$row["source"]?>
				</td>
				<td>
					<?=$row["dest"]?>
				</td>
				<td>
					<?=$row["note"]?>
				</td>
				<td>
					<?=$docs?>
				</td>
				<td>
					<?=($row["to_repair"]?"x":"")?>
				</td>
			</tr>
<?	
}

?>

		</table>
	</fieldset>
<?
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}
elseif($func=='editItemForm')
{
	$itemId=$_POST["itemId"];

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare owners query*/
	$query = "SELECT * FROM owners 
				WHERE id_simulators='".$_SESSION["simulator_id"]."'
				ORDER BY name";

	/*execute owners query*/
	$owners = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error $query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare part query*/
	$query = "SELECT t.* FROM
			(
				SELECT items.*
					,places.id_places_types
					,CONCAT(user_creator.name,' ',user_creator.surname) AS creator
					,CONCAT(user_updater.name,' ',user_updater.surname) AS updater
					,parts.description AS parts_description
					,parts.pn_manufacturer AS parts_pn_manufacturer
					,parts.pn_supplier AS parts_pn_supplier
					,simulators.name AS simulator
					,movements_items.to_repair
					,movements_items.replaced_itemId
					,items2.sn AS replaced_itemId_text
					,IFNULL(movements_items.id_fotografia,0) AS fotografia
				FROM items
					LEFT JOIN parts ON items.id_parts=parts.id
					LEFT JOIN places ON items.id_places=places.id
					LEFT JOIN simulators ON parts.id_simulators=simulators.id
					LEFT JOIN users user_creator ON items.id_users_creator = user_creator.id
					LEFT JOIN users user_updater ON items.id_users_updater = user_updater.id
					LEFT JOIN movements_items ON items.id = movements_items.id_items
					LEFT JOIN movements ON movements_items.id_movements = movements.id 
					LEFT JOIN items items2 ON movements_items.replaced_itemId=items2.id 
				WHERE items.id='$itemId' 
				ORDER BY movements.insert_date DESC
			)t
			GROUP BY t.id";

	/*execute part query*/
	$items = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error $query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$item = mysqli_fetch_assoc($items);

	if(strlen(trim($item["creator"])))
		$creator=ucwords(trim($item["creator"]));
	else
		$creator="none";
	if(strlen(trim($item["updater"])))
		$updater=ucwords(trim($item["updater"]));
	else
		$updater="none";

	if(strlen(trim($item["sn"]))==0)
	{
		$query="SELECT items2.sn,count(items2.id) AS qty 
					FROM items LEFT JOIN items AS items2
						ON items.id_owners=items2.id_owners
						AND items.id_places=items2.id_places
						AND items.location=items2.location
						AND items.id_parts=items2.id_parts
					WHERE items.id='$itemId'
					GROUP BY items2.sn
					HAVING items2.sn IS NULL";

		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("Query error $query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$row=mysqli_fetch_assoc($result);
		$qty=$row["qty"];
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	}
	else
		$qty=1;

	require_once("fotografia.php");
	$items_in_foto=get_items_in_foto($conn,$itemId);
	$fotografiaArray=get_fotografia_array($conn,$itemId);

	asort($fotografiaArray);
	$fotografiaRow="";
	if(count($fotografiaArray)>1)
	{
		$fotografiaCombo="";

		$id_fotografia=$item["fotografia"];
		$fotografiaCombo="<select name='id_fotografia' id='id_fotografia'>";
		foreach($fotografiaArray as $id=>$line)
		{
			if(($items_in_foto[$id]==$itemId)
					||(!isset($items_in_foto[$id])))
				$fotografiaCombo.=sprintf("<option value=\"%d\"%s>%s</option>",
					$id,($id==$id_fotografia?" selected='selected'":""),$line);
		}
		$fotografiaCombo.="</select>";
		
		$fotografiaRow='
			<tr id="fotografiaRow">
				<td class="fieldLabel">
					Fotografia
				</td>
				<td>
					'.$fotografiaCombo.'
				</td>
				<td	id="fotografiaCheck" class="fieldCheck">
				</td>
			</tr>';
	}
	if(($item["id_places_types"]>2)&&(strlen(trim($item["sn"]))))
	{
		$checked=($item["to_repair"]?' checked="checked"':'');
		$repairRow="
			<tr id='repairRow'>
				<td class='fieldLabel'>
					To repair
				</td>
				<td>
					<input type='checkbox' id='to_repair' name='to_repair'$checked >
				</td>
				<td	id='repairCheck' class='fieldCheck'>
				</td>
			</tr>
			<tr id='replacedRow'>
				<td class='fieldLabel'>
					Replaced with
				</td>
				<td>
					<input type='text' name='replaced_itemId_text' id='replaced_itemId_text' 
						value='".$item["replaced_itemId_text"]."'>
					<input type='hidden' name='replaced_itemId' 
						id='replaced_itemId' value='".$item["replaced_itemId"]."'>
				</td>
				<td	id='repairCheck' class='fieldCheck'>
				</td>
			</tr>";
	}

	?>
	<form	id="itemForm"
			name="itemForm"
			action="include/items.php"
			method="post">
		<input type="hidden" id="query" name="query" value="<?=$_REQUEST['query']?>" />
		<input type="hidden" id="qtype" name="qtype" value="<?=$_REQUEST['qtype']?>" />
		<fieldset>
		<legend>Edit Item Details</legend>
		<input 	type="hidden"
				id="func"
				name="func"
				value="edit"/>
		<input 	type="hidden"
				id="id_simulators"
				name="id_simulators"
				value="<?=$_SESSION["simulator_id"]?>"/>
		<input 	type="hidden"
				id="itemId"
				name="itemId"
				value="<?=$itemId?>"/>
		<input 	type="hidden"
				id="partId"
				name="partId"
				value="<?=$item['id_parts']?>"/>
		<table>
			<tr>
				<td class="fieldLabel">
					Manufacturer P/N
				</td>
				<td>
					<?=$item["parts_pn_manufacturer"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Supplier P/N
				</td>
				<td>
					<?=$item["parts_pn_supplier"]?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Description
				</td>
				<td>
					<?=$item["parts_description"]?>
				</td>
			</tr>
			<tr id="qtyRow" style="<?=($qty>1?"":"display:none")?>">
				<td class="fieldLabel">
					Affected items
				</td>
				<td>
					<input	type="text" 
							id="qtyInput"
							name="qtyInput"
							class="threeDigitInput"
							value="<?=$qty?>" 
							onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"
							onchange="qtyChanged(this,<?=$qty?>)" />
				</td>
				<td	id="qtyCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Serial Number
				</td>
				<td>
					<input	type="text" 
							id="snInput" 
							name="snInput" 
							class="longTextInput" 
							value="<?=$item['sn']?>" 
							onchange="
								if($.trim(this.value).length)
								{
									$('#qtyInput').val(1);
									$('#qtyRow').hide(); 
								}
								else
								{
									$('#qtyInput').val(<?=$qty?>);
									$('#qtyRow').show();
								}" 
									/>
				</td>
				<td	id="snCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Location / note
				</td>
				<td>
					<input	type="text"
							id="locationInput"
							name="locationInput"
							class="longTextInput"
							value="<?=$item['location']?>"/>
				</td>
				<td	id="locationCheck" class="fieldCheck">
				</td>
			</tr>
			<?=@ifnull($fotografiaRow)?>
			<?=@ifnull($repairRow)?>
			<tr>
				<td class="fieldLabel">
					Owner
				</td>
				<td>
					<select	id="ownerSelect"
							name="ownerSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select owner...</option>
					<?
					while($owner=mysqli_fetch_assoc($owners))
					{
						?>
						<option value="<?=$owner["id"]?>"
							<?=(($item['id_owners']==$owner["id"])?"selected":"")?>>
							<?=$owner["name"]?>
						</option>
						<?
					}?>
					</select>
				</td>
				<td	id="ownerCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Created by
				</td>
				<td>
					<?php echo $creator ?>
				</td>
				<td class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Updated by
				</td>
				<td>
					<?php echo $updater ? $updater:$creator; ?>
				</td>
				<td class="fieldCheck">
				</td>
			</tr>
		</fieldset>
		</table>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($owners) || (is_object($owners) && (get_class($owners) == "mysqli_result"))) ? true : false);
	((mysqli_free_result($items) || (is_object($items) && (get_class($items) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}

elseif($func=='edit')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$snInput=str_replace(array(" ","§"),"_",$_POST["snInput"]);
	$locationInput=str_replace("§","_",$_POST["locationInput"]);
	$qty=$_POST["qtyInput"];
	$to_repair=(isset($_POST["to_repair"])?1:0);
	$sn=trim($snInput);
	$ownerSelect=$_POST["ownerSelect"];
	$itemId=$_POST["itemId"];
	$id_fotografia=$_POST["id_fotografia"];
	if(strlen($sn))
		$sn="'".htmlspecialchars($sn)."'";
	else
		$sn="NULL";
	if((isset($_POST["replaced_itemId"]))&&
			(((int)$_POST["replaced_itemId"])>0)&&
			$to_repair)
		$replaced_itemId=$_POST["replaced_itemId"];
	else
		$replaced_itemId="NULL";

	$queries=array();
	if($sn!="NULL")
	{
		$queries[]="UPDATE items 
				SET
				items.location='$locationInput',
				items.sn=$sn,
				items.id_owners='$ownerSelect'
			WHERE id='$itemId'";

		$queries[]="UPDATE movements_items INNER JOIN 
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
					SET movements_items.id_fotografia='$id_fotografia',
						movements_items.to_repair='$to_repair',
						movements_items.replaced_itemId=$replaced_itemId";
	}
	else
		$queries[]="UPDATE items INNER JOIN
				(
					SELECT items2.id 
						FROM items LEFT JOIN items AS items2
						ON items.id_owners=items2.id_owners
						AND items.id_places=items2.id_places
						AND items.location=items2.location
						AND items.id_parts=items2.id_parts
						WHERE items.id='$itemId'
						LIMIT $qty
				) AS items3
					ON items.id=items3.id
				SET 
					location='$locationInput',
					sn=null,
					id_owners='$ownerSelect'";

	/*execute suppliers query*/

	if(!mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION"))
		die("START TRANSACTION");

	$logfile = fopen("log.log", "a+"); 
	$affected_rows=0;
	foreach($queries as $query)
	{
		fwrite($logfile,$query."\r\n\r\n");
		if(!mysqli_query($GLOBALS["___mysqli_ston"], $query))
		{
			echo ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
			mysqli_query($GLOBALS["___mysqli_ston"], "ROLLBACK");
			die();
		}
		$affected_rows+=mysqli_affected_rows($GLOBALS["___mysqli_ston"]);
	}
	fclose($logfile);
	mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT");

	if($affected_rows==0)
		echo("0Nothing to update.");
	else
	{
		$user_id=$_SESSION["user_id"];
		$query="UPDATE items SET 
					id_users_updater='$user_id' 
				WHERE id='$itemId'";
		mysqli_query($GLOBALS["___mysqli_ston"], $query);
		echo("0Item updated.");
	}
}

elseif($func=='getItemDetails') 
{
	$itemId=$_POST["itemId"];

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$sql  = "SELECT items.sn as sn";
	$sql .= ", items.location as location";
	$sql .= ", parts.pn_manufacturer as pn_manufacturer";
	$sql .= ", parts.pn_supplier as pn_supplier";
	$sql .= ", suppliers.name as suppliers_name";
	$sql .= ", CONCAT(user_creator.name,' ',user_creator.surname) AS creator";
	$sql .= ", CONCAT(user_updater.name,' ',user_updater.surname) AS updater";
	$sql .= " FROM items";
	$sql .= " LEFT JOIN parts ON items.parts_id = parts.id";
	$sql .= " LEFT JOIN suppliers ON suppliers.id = parts.id_suppliers";
	$sql .= " LEFT JOIN users user_creator ON items.id_users_creator = users.id";
	$sql .= " LEFT JOIN users user_updater ON items.id_users_updater = users.id";
	$sql .= " WHERE items.id = '$itemId'";
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
	$item = mysqli_fetch_assoc($result);
	
	?>
	<fieldset id="part_details_fieldset">
	<legend>Item details</legend>
	<table class="part_details">
		<tr>
			<td class="fieldLabel"><i>Manufacturer P/N</i></td>
			<td><?php echo($item['pn_manufacturer']); ?></td>
		</tr>
		<tr>
			<td class="fieldLabel"><i>Supplier P/N</i></td>
			<td><?php echo($item['pn_supplier']); ?></td>
		</tr>
		<tr>
			<td class="fieldLabel"><i>Serial Number</i></td>
			<td><?php echo($item['sn']); ?></td>
		</tr>
		<tr>
			<td class="fieldLabel"><i>Location / note</i></td>
			<td><?php echo($item['location']); ?></td>
		</tr>
		<tr>
			<td class="fieldLabel"><i>Supplier</i></td>
			<td><?php echo($item['suppliers_name']); ?></td>
		</tr>
		<tr>
			<td class="fieldLabel"><i>Created by</i></td>
			<td><?php echo($item['creator']); ?></td>
		</tr>
		<tr>
			<td class="fieldLabel"><i>Updated by</i></td>
			<td><?php echo($item['updater']); ?></td>
		</tr>
	</table>
	</fieldset>
	<?php
}
elseif($func=='delete')
{
	$items_id=$_POST["items_id"];

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$query="SELECT id_movements FROM movements_items WHERE id_items='$items_id'";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$query");
	$movements=array();
	while($row=mysqli_fetch_assoc($result))
		$movements[]=$row["id_movements"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	if(!mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION"))
		die("START TRANSACTION");

	$query="DELETE FROM items WHERE id='$items_id'";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$query");
		
	$query="DELETE FROM movements_items WHERE id_items='$items_id'";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$query");

	$movements_imploded=implode(",", $movements);
	$query="SELECT movements.id,
				GROUP_CONCAT(movements_items.id_items) AS mi
				FROM movements LEFT JOIN movements_items
					ON movements.id=movements_items.id_movements
				WHERE movements.id IN($movements_imploded)
			GROUP BY movements.id";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$query");
	while($row=mysqli_fetch_assoc($result))
	{
		if(strlen($row["mi"])==0)
		{
			$id_movements=$row["id"];
			$query="DELETE FROM movements WHERE id='$id_movements'";
			mysqli_query($GLOBALS["___mysqli_ston"], $query)
				or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$query");
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	if(!mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT"))
		die("COMMIT");
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}
?>
