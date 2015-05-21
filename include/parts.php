<?php

/* connect to database */
require_once('mysql.php');
require_once('functions.php');

$crd=array("C"=>"comsumable","R"=>"repairable","D"=>"discardable");

/* per avere la roba del $_SESSION */
ini_set ('session.name', 'magazzino');
session_start();

/* find what function we have to run */
$func = $_REQUEST['func'];

/******************************************************************************/
/*																			*/
/* LIST																	   */
/*																			*/
/******************************************************************************/
if($func=='list')
{
	$conn	=	($GLOBALS["___mysqli_ston"] = mysqli_connect(	$myhost, 
								$myuser, 
								$mypass));

	((bool)mysqli_query(
						$conn , "USE " . $mydb));

	/* table page number */
	$page = $_POST['page'];
	/* records per page */
	$rp = $_POST['rp'];
	/* sort field */
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	$query = @ifnull($_REQUEST['query']);
	$qtype = @ifnull($_REQUEST['qtype']);


	if (!$sortname) $sortname = 'parts_description';
	if (!$sortorder) $sortorder = 'desc';

	$sort = "ORDER BY $sortname $sortorder";

	if(!$page || !$rp)
	{
		$limit = "";
		$page = 1;
	}
	else
	{
		$start = (($page-1) * $rp);
		$limit = " LIMIT $start, $rp";
	}

	$where=" WHERE parts.id_simulators='".$_SESSION["simulator_id"]."' ";

	if(@ifnull($_POST['whereString'])!='')
	{
		$where .= " AND ".$_POST['whereString']." ";
		$where = str_replace('\"','"',$where);
	}

	if($query!='')
	{
		if($qtype == "pn")
			$where .= "AND (pn_manufacturer LIKE '%$query%' OR pn_supplier LIKE '%$query%')";
		elseif($qtype == "parts.description")
			$where .= "AND (parts.description LIKE '%$query%')";
		elseif($qtype == "subsystem")
			$where .= "AND (subsystems.text LIKE '%$query%')";
		elseif($qtype == "supplier")
			$where .= "AND (suppliers.name LIKE '%$query%')";
		elseif($qtype == "manufacturer")
			$where .= "AND (manufacturers.name LIKE '%$query%')";
	}

	$sql = "SELECT parts.id FROM parts
				LEFT JOIN subsystems ON parts.id_subsystems=subsystems.id
				LEFT JOIN places AS suppliers ON suppliers.id = parts.id_suppliers 
					AND (suppliers.id_places_types=3 OR suppliers.id_places_types=4) 
				LEFT JOIN places AS manufacturers ON manufacturers.id = parts.id_manufacturers 
					AND (manufacturers.id_places_types=5 OR suppliers.id_places_types=4) 
				 $where";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$total = mysqli_num_rows($result);

	$sql =	"SELECT	parts.id AS parts_id,
					parts.description AS parts_description,
					parts.pn_manufacturer AS parts_pn_manufacturer,
					subsystems.text AS subsystem,
					subsystems2.text AS subsystem2,
					subsystems3.text AS subsystem3,
					parts.pn_supplier AS parts_pn_supplier,
					suppliers.name AS suppliers_name, 
					manufacturers.name AS manufacturers_name,
					f.location AS fotografia
			FROM parts 
			LEFT JOIN places AS suppliers
				ON suppliers.id = parts.id_suppliers 
				AND (suppliers.id_places_types=3 OR suppliers.id_places_types=4)
			LEFT JOIN places AS manufacturers
				ON manufacturers.id = parts.id_manufacturers 
				AND (manufacturers.id_places_types=5 OR manufacturers.id_places_types=4) 
			LEFT JOIN users 
				ON users.id = parts.id_users_creator 
			LEFT JOIN subsystems 
				ON subsystems.id = parts.id_subsystems 
			LEFT JOIN subsystems AS subsystems2
				ON subsystems2.id = parts.id_subsystems2
			LEFT JOIN subsystems AS subsystems3
				ON subsystems3.id = parts.id_subsystems3
			LEFT JOIN compatible_parts ON parts.id=compatible_parts.id_parts
			LEFT JOIN 
				(
					SELECT id_compatible,GROUP_CONCAT(DISTINCT location) AS location
					FROM fotografia
					GROUP BY id_compatible
				) f ON f.id_compatible=compatible_parts.id_compatible
			$where $sort $limit";

	$logfile = fopen("log.log", "a+");
	fwrite($logfile,$sql."\r\n\r\n");
	fwrite($logfile,print_r($_POST,TRUE)."\r\n\r\n");
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	fwrite($logfile,((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."\r\n\r\n");
	fclose($logfile);

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
		$description=nlTobr($row['parts_description']);
		$subsystems=$row["subsystem"];
		if(strlen($row["subsystem2"]))
			$subsystems.=" / ".$row["subsystem2"];
		if(strlen($row["subsystem3"]))
			$subsystems.=" / ".$row["subsystem3"];
		if ($rc) {
			$json .= ",";
		}
		$row['manufacturers_name']=($row['manufacturers_name']?$row['manufacturers_name']:"---");
		$json .= "\n{";
		$json .= "id:'".$row['parts_id']."',";
		$json .= "cell:['".addslashes($row['parts_pn_supplier'])."'";
		$json .= ",'".addslashes($row['parts_pn_manufacturer'])."'";
		$json .= ",'".addslashes($description)."'";
		$json .= ",'".addslashes($subsystems)."'";
		$json .= ",'".addslashes($row['suppliers_name'])."'";
		$json .= ",'".addslashes($row['manufacturers_name'])."'";
		$json .= ",'".addslashes($row['fotografia'])."'";
		$json .= "]}";
		$rc = true;
	}
	$json .= "]\n";
	$json .= "}";

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	echo $json;
}

/******************************************************************************/
/*																			*/
/* details - Fetch and display the details for a part, including the items	*/
/*		   in inventory for that part									   */
/*																			*/
/******************************************************************************/
elseif($func=='details')
{
	$logfile = fopen("log.log", "a+");
	fwrite($logfile,'---------------------\r\n\r\n');
	fwrite($logfile,'$func=="details"\r\n\r\n');

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if(!is_numeric($_REQUEST['partId']))
		die("No part selected.");

	$sql  = " SELECT parts.*";
	$sql .= ", places.name AS suppliers_name";
	$sql .= ", subsystems.text AS subsystem";
	$sql .= " FROM parts";
	$sql .= " LEFT JOIN places ON places.id = parts.id_suppliers";
	$sql .= " LEFT JOIN subsystems ON subsystems.id = parts.id_subsystems";
	$sql .= " WHERE parts.id = ".$_REQUEST['partId'];

	fwrite($logfile,$sql.'\r\n');

	$part_result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	fwrite($logfile,'\t'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'\r\n');
	$part_row = mysqli_fetch_assoc($part_result);
	fwrite($logfile,'\t'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'\r\n\r\n');
	$shelf_life = $part_row['shelf_life'];
	$shelf_years = $shelf_life%365;
	$shelf_months = ($shelf_life-($shelf_years*365))%30;
	$shelf_days = $shelf_life-($shelf_years*365)-($shelf_months*30);

	/* get total number of items for this part */
	$sql  = "SELECT COUNT(id)";
	$sql .= " FROM `items`";
	$sql .= " WHERE `id_parts` = ".$_REQUEST['partId'];

	fwrite($logfile,$sql.'\r\n');

	$items = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	fwrite($logfile,'\t'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'\r\n\r\n');

	$row = mysqli_fetch_array($items);
	$total_items = $row[0];

	/* get all the items that are this part listing the serialized ones and grouping */
	/* the unserialized ones */
	$sql  = "SELECT COUNT(id) as quantity";
	$sql .= ",sn";
	$sql .= " FROM `items`";
	$sql .= " WHERE `id_parts` = ".$_REQUEST['partId'];
	$sql .= " GROUP BY sn";

	fwrite($logfile,$sql.'\r\n');

	$items = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	fwrite($logfile,'\t'.((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)).'\r\n\r\n');
	fclose($logfile);

	?>
	<fieldset class="partDetails">
	<legend>Part details</legend>
	<table>
		<tr>
			<td colspan="2">
				<h1>
					<?php echo($part_row['description']); ?>
				</h1>
			</td>
		</tr>
		<tr>
			<td>Manufacturer P/N</td>
			<td><b><?php echo($part_row['pn_manufacturer']); ?></td>
		</tr>
		<tr>
			<td>Supplier</td>
			<td><b><?php echo($part_row['suppliers_name']); ?></td>
		</tr>
		<tr>
			<td>Supplier P/N</td>
			<td><b><?php echo($part_row['pn_supplier']); ?></td>
		</tr>
		<tr>
			<td>Subsystem</td>
			<td><b><?=$part_row['subsystem']?></td>
		</tr>
		<tr>
			<td>Cage code</td>
			<td><b><?php echo($part_row['cage_code']); ?></td>
		</tr>
		<tr>
			<td>Deperibility</td>
			<td><b><?=$crd[$part_row['CRD']]?></td>
		</tr>
		<tr>
			<td>Shelf life</td>
			<td><b><?php echo("$shelf_years years ".
						   "$shelf_months months ".
						   "$shelf_days days"); ?></td>
		</tr>
		<tr>
			<td>Criticality</td>
			<td><b><?php echo($part_row['criticality']); ?></td>
		</tr>
		<tr>
			<td>Minimum quantity</td>
			<td><b><?php echo($part_row['minimum_quantity']); ?></td>
		</tr>
		<tr>
			<td>Quantity</td>
			<td><b><?php echo($total_items); ?></td>
		</tr>
		<tr>
			<td>Inventory</td>
			<td><b>
		<?php
		if($total_items == 0) {
			echo("No items in inventory.");
		}else {
			$item = mysqli_fetch_assoc($items);
			$quantity = $item['quantity'];
			$serial = ($item['sn']=="")?"Unserialized":$item['sn'];
			echo($quantity." x ".$serial);
		}
		?>
			</td>
		</tr>
		<?php
		if(mysqli_num_rows($items) > 1) {
			while($item = mysqli_fetch_assoc($items)) {
				$quantity = $item['quantity'];
				$serial = ($item['sn']=="")?"Unserialized":$item['sn'];
				?>
				<tr>
					<td>&nbsp;</td>
					<td><b><?php echo($quantity." x ".$serial); ?></b></td>
				</tr>
				<?php
			}
		}
	?></table>
	</fieldset><?php
}

/******************************************************************************/
/*																			*/
/* editPartForm - Prepare form to edit existing part						  */
/*																			*/
/******************************************************************************/
elseif($func=='editPartForm')
{
	$logfile = fopen("log.log", "a+");
	fwrite($logfile,'---------------------\r\n\r\n');
	fwrite($logfile,'$func=="editPartForm"\r\n\r\n');

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare suppliers query*/
	$query = "SELECT * FROM places
				WHERE 
					id_simulators='".$_SESSION["simulator_id"]."' AND
					(id_places_types=3 OR id_places_types=5 OR id_places_types=4)
					AND active=1 
				ORDER BY name";

	/*execute suppliers query*/
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	while($row=mysqli_fetch_assoc($result))
	{
		if($row["id_places_types"]==3)
			$suppliers[$row["id"]]=$row["name"];
		elseif($row["id_places_types"]==5)
			$manufacturers[$row["id"]]=$row["name"];
		elseif($row["id_places_types"]==4)
		{
			$suppliers[$row["id"]]=$row["name"];
			$manufacturers[$row["id"]]=$row["name"];
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	/*prepare subsystems query*/
	$query = "SELECT * FROM subsystems 
				WHERE id_simulators='".$_SESSION["simulator_id"]."' 
			ORDER BY line_order,text";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subsystems=array();
	while($row = mysqli_fetch_assoc($result))
		$subsystems[$row["id"]]=$row["text"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	/*prepare part query*/
	$query = "SELECT * FROM parts WHERE id=\"".$_POST['partId']."\"";

	/*execute part query*/
	$parts = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$part = mysqli_fetch_assoc($parts);

	/*get who created the part*/
	$query = "SELECT name, surname FROM users "
			."WHERE id =".$part["id_users_creator"];
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$row = mysqli_fetch_assoc($result);
	$creator = ucfirst($row["name"])." ".ucfirst($row["surname"]);
	fwrite($logfile,"CREATOR\r\n\r\n");
	fwrite($logfile,$query."\r\n\r\n");
	fwrite($logfile,$creator."\r\n\r\n");

	/*get who updated the part*/
	$query = "SELECT name, surname FROM users "
			."WHERE id =".$part["id_users_updater"];
	if($result = mysqli_query($GLOBALS["___mysqli_ston"], $query))
	{
		if($gino = mysqli_fetch_assoc($result))
		{
			$updater = ucfirst($gino["name"])." ".ucfirst($gino["surname"]);
		}
	}
	else
	{
		$updater = "None";
	}
	fwrite($logfile,"UPDATER\r\n\r\n");
	fwrite($logfile,$query."\r\n\r\n");
	fwrite($logfile,$updater."\r\n\r\n");

	if(!($part['shelf_life'] % 7))
	{
		$shelfLifeUnit = '7';
		$shelfLife = $part['shelf_life']/7;
	}
	elseif(!($part['shelf_life'] % 30))
	{
		$shelfLifeUnit = '30';
		$shelfLife = $part['shelf_life']/30;
	}
	elseif(!($part['shelf_life'] % 365))
	{
		$shelfLifeUnit = '365';
		$shelfLife = $part['shelf_life']/365;
	}
	else
	{
		$shelfLifeUnit = '1';
		$shelfLife = $part['shelf_life'];
	}
	?>

	<form	id="partForm"
			name="partForm"
			action="include/parts.php"
			method="post">
		<fieldset>
		<legend>Edit Part Details</legend>
		<input 	type="hidden"
				id="func"
				name="func"
				value="edit"/>
		<input 	type="hidden"
				id="partId"
				name="partId"
				value="<?php echo($_POST['partId']); ?>"/>
		<table>
			<tr>
				<td class="fieldLabel">
					Supplier
				</td>
				<td>
					<select	id="supplierSelect"
							name="supplierSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select supplier...</option>
					<?php
					foreach($suppliers as $id=>$name)
					{
						?>
						<option value="<?=$id?>"
							<?=($part['id_suppliers']==$id?"selected":"");?>>
							<?=$name?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="supplierCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Supplier P/N
				</td>
				<td>
					<input	type="text"
							id="pnSupplierInput"
							name="pnSupplierInput"
							class="longTextInput"
							value="<?php echo $part['pn_supplier']?>"/>
				</td>
				<td	id="pnSupplierCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Manufacturer
				</td>
				<td>
					<select	id="manufacturerSelect"
							name="manufacturerSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select manufacturer...</option>
					<?php
					foreach($manufacturers as $id=>$name)
					{
						?>
						<option value="<?=$id?>"
							<?=($part['id_manufacturers']==$id?"selected":"");?>>
							<?=$name?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="manufacturerCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Manufacturer P/N
				</td>
				<td>
					<input	type="text"
							id="pnManInput"
							name="pnManInput"
							class="longTextInput"
							value="<?php echo $part['pn_manufacturer']?>"/>
				</td>
				<td	id="pnManCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Subsystem
				</td>
				<td>
					<select	id="subsystemSelect"
							name="subsystemSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select subsystem...</option>
					<?php
					foreach($subsystems as $id=>$text)
					{
						?>
						<option value="<?=$id?>"
							<?php echo(($part['id_subsystems']==$id)?"selected":"");?>>
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="subsystemCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Alt subsystem
				</td>
				<td>
					<select	id="subsystem2Select"
							name="subsystem2Select"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select subsystem...</option>
					<?php
					foreach($subsystems as $id=>$text)
					{
						?>
						<option value="<?=$id?>"
							<?php echo(($part['id_subsystems2']==$id)?"selected":"");?>>
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Alt subsystem
				</td>
				<td>
					<select	id="subsystem3Select"
							name="subsystem3Select"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select subsystem...</option>
					<?php
					foreach($subsystems as $id=>$text)
					{
						?>
						<option value="<?=$id?>"
							<?php echo(($part['id_subsystems3']==$id)?"selected":"");?>>
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Deperibility
				</td>
				<td>
					<select	id="crdSelect"
							name="crdSelect"
							class="longTextInput">
					<?php
					foreach($crd as $id=>$text)
					{
						?>
						<option value="<?=$id?>"
							<?=(($part['CRD']==$id)?" selected":"");?>>
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="crdCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Cage code
				</td>
				<td>
					<input	type="text"
							id="cageCodeInput"
							name="cageCodeInput"
							class="longTextInput"
							value="<?php echo $part['cage_code']?>"/>
				</td>
				<td	id="cageCodeCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Shelf life
				</td>
				<td>
					<input	type="text"
							id="shelfLifeInput"
							name="shelfLifeInput"
							class="threeDigitInput"
							value="<?php echo $shelfLife?>"/>
					<select id="shelfLifeUnitSelect"
							name="shelfLifeUnitSelect"
							class="besideThreeDigit">
						<option value="1"
						<?php echo(($shelfLifeUnit==1)?" selected":"");?>>days</option>
						<option value="7"
						<?php echo(($shelfLifeUnit==7)?" selected":"");?>>weeks</option>
						<option value="30"
						<?php echo(($shelfLifeUnit==30)?" selected":"");?>>months</option>
						<option value="365"
						<?php echo(($shelfLifeUnit==365)?" selected":"");?>>years</option>
					</select>
				</td>
				<td	id="shelfLifeCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Criticality
				</td>
				<td>
					<input	type="text"
							id="criticalityInput"
							name="criticalityInput"
							class="threeDigitInput"
							value="<?php echo $part["criticality"]?>"/>
				</td>
				<td	id="criticalityCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Minimum quantity
				</td>
				<td>
					<input	type="text"
							id="minimumQuantityInput"
							name="minimumQuantityInput"
							class="threeDigitInput"
							value="<?php echo $part["minimum_quantity"]?>"/>
				</td>
				<td	id="minimumQuantityCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Description
				</td>
				<td>
					<textarea	rows="3"
								id="partDescriptionInput"
								name="partDescriptionInput"
								class="longTextInput"><?php echo $part["description"]?></textarea>
				</td>
				<td	id="descriptionCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Created by
				</td>
				<td class="fieldLabel">
					<?php echo $creator ?>
				</td>
				<td class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Updated by
				</td>
				<td class="fieldLabel">
					<?php echo $updater ? $updater:$creator; ?>
				</td>
				<td class="fieldCheck">
				</td>
			</tr>
			<tr>
		</table>
		</fieldset>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($parts) || (is_object($parts) && (get_class($parts) == "mysqli_result"))) ? true : false);
	fclose($logfile);
}

/******************************************************************************/
/*																			*/
/* EDIT - Perform DB UPDATE with data from editPartForm					   */
/*																			*/
/******************************************************************************/
elseif($func=='edit')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare suppliers query*/
	$description=str_replace("'","\'",trim($_POST['partDescriptionInput']));
	$query	=	"UPDATE parts SET ";
	$query	.=	"id_suppliers='".$_POST['supplierSelect']."'";
	$query	.=	",id_manufacturers='".$_POST['manufacturerSelect']."'";
	$query	.=	",pn_manufacturer='".$_POST['pnManInput']."'";
	$query	.=	",pn_supplier='".$_POST['pnSupplierInput']."'";
	$query	.=	",id_subsystems='".$_POST['subsystemSelect']."'";
	$query	.=	",id_subsystems2='".$_POST['subsystem2Select']."'";
	$query	.=	",id_subsystems3='".$_POST['subsystem3Select']."'";
	$query	.=	",CRD='".$_POST['crdSelect']."'";
	$query	.=	",cage_code='".$_POST['cageCodeInput']."'";
	$query	.=	",shelf_life='".$_POST['shelfLifeInput']*$_POST['shelfLifeUnitSelect']."'";
	$query	.=	",criticality='".$_POST['criticalityInput']."'";
	$query	.=	",minimum_quantity='".$_POST['minimumQuantityInput']."'";
	$query	.=	",description='$description'";
	$query  .=	" WHERE id='".$_POST['partId']."'";

	/*execute suppliers query*/
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if(mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo("0Nothing to update.");
	}
	else
	{
		$query	=	"UPDATE parts";
		$query	.=	" SET id_users_updater=\"".$_SESSION['user_id']."\"";
		$query  .=	" WHERE id=\"".$_POST['partId']."\"";
		mysqli_query($GLOBALS["___mysqli_ston"], $query);
		echo("0Part updated.");
	}
}

/******************************************************************************/
/*																			*/
/* DELETE - Perform DB DELETE of existing part								*/
/*																			*/
/******************************************************************************/
elseif($func=='delete')
{
	if(!$_POST['partId'])
	{
		die('No part id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"DELETE FROM parts";
	$query  .=	" WHERE id=\"".$_POST['partId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0Part successfully deleted";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "No matching part";
	}
	else
	{
		echo "Part could not be deactivated, mysql error";
	}
	return;
}

/******************************************************************************/
/*																			*/
/* DEACTIVATE - Perform DB UPDATE of existing part setting active = 0		 */
/*																			*/
/******************************************************************************/
elseif($func=='deactivate')
{
	if(!$_POST['partId'])
	{
		die('No part id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE parts";
	$query	.=	" SET active=\"0\"";
	$query  .=	" WHERE id=\"".$_POST['partId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0Part successfully deactivated";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "Part already inactive";
	}
	else
	{
		echo "Part could not be deactivated, mysql error";
	}
	return;
}

/******************************************************************************/
/*																			*/
/* ACTIVATE - Perform DB UPDATE of existing part setting active = 1		   */
/*																			*/
/******************************************************************************/
elseif($func=='activate')
{
	if(!$_POST['partId'])
	{
		die('No part id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE parts";
	$query	.=	" SET active=\"1\"";
	$query  .=	" WHERE id=\"".$_POST['partId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0Part successfully activated";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "Part already active";
	}
	else
	{
		echo "Part could not be activated, mysql error";
	}
	return;
}
?>
