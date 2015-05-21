<?php

/* connect to database */
require_once('mysql.php');
require_once('functions.php');

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

	if (!$sortname) $sortname = 'places_name';
	if (!$sortorder) $sortorder = 'desc';

	$sort = "ORDER BY $sortname $sortorder";

	if(!$page || !$rp)
	{
		$limit = "";
	}else
	{
		$start = (($page-1) * $rp);
		$limit = " LIMIT $start, $rp";
	}

	$query = $_REQUEST['query'];
	$qcond = $_REQUEST['qcond'];
	$qtype = $_REQUEST['qtype'];

	$where = "";
	if ($query) $where = " WHERE $qtype $qcond '$query' "; 

	$sql =	"SELECT	places.id AS places_id,"
				.	"places.name AS places_name,"
				.	"places.description AS places_description,"
				.	"places_types.name AS places_types_name,"
				.	"places.active AS places_active "
		.	"FROM places "
		.	"LEFT JOIN places_types "
		.	"ON places_types.id = places.id_places_types"
		.	"$where $sort $limit";

	$result = runSQL($sql);

	$total = countRec('id',"places $where");

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
		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['places_id']."',";
		$json .= "cell:['".$row['places_name']."'";
		$json .= ",'".addslashes($row['places_description'])."'";
		$json .= ",'".ucfirst(addslashes($row['places_types_name']))."'";
		if($row['places_active']==0)
		{
			$json .= ",'<img src=\"img/stock_draw-circle-red.png\"/>']";
		}
		elseif($row['places_active']==1)
		{
			$json .= ",'<img src=\"img/stock_draw-circle-green.png\"/>']";
		}
		$json .= "}";
		$rc = true;
	}
	$json .= "]\n";
	$json .= "}";

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	echo $json;
}

elseif($func=='newWarehouseForm')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user types query*/
	$query = "SELECT * FROM places_types";

	/*execute user types query*/
	$places_types = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	?>

	<form	id="warehouseForm"
			name="warehouseForm"
			action="include/warehouses.php"
			method="post">
		<input 	type="hidden"
				id="func"
				name="func"
				value="add"/>
		<table>
			<tr>
				<td class="warehouseFormLabel">
					Name
				</td>
				<td>
					<input	type="text"
							id="nameInput"
							name="nameInput"/>
				</td>
				<td	id="warehouseNameCheck" class="userFormCheckCell">
				</td>
			</tr>
			<tr>
				<td class="warehouseFormLabel">
					Description
				</td>
				<td>
					<textarea	cols="20"	rows="3"
								id="warehouseDescriptionInput"
								name="warehouseDescriptionInput"/>
				</td>
				<td	id="descriptionCheck" class="userFormCheckCell">
				</td>
			</tr>
			<tr>
				<td class="warehouseFormLabel">
					Warehouse Type
				</td>
				<td>
					<select	id="placeTypeSelect"
							name="placeTypeSelect">
					<?php
					while($place_type = mysqli_fetch_assoc($places_types))
					{
						?>
						<option value="<?php echo $place_type['id']?>">
							<?php echo $place_type['name']?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="placeTypeCheck" class="userFormCheckCell">
				</td>
			<tr>
				<td colspan="3">
					<input	type="submit"
							id="warehouseFormSubmit"
							value="SUBMIT"/>
					<input	type="reset"
							id="warehouseFormReset"
							value="RESET"/>
				</td>
			</tr>
		</table>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($places_types) || (is_object($places_types) && (get_class($places_types) == "mysqli_result"))) ? true : false);

}

elseif($func=='editWarehouseForm')
{
	$logfile = fopen("log.log", "a+"); 
	
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare places types query*/
	$query = "SELECT * FROM places_types";

	/*execute places types query*/
	$places_types = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare place types query*/
	$query =  "SELECT * FROM places";
	$query .= " WHERE id=\"".$_POST['warehouseId']."\"";
	
	fwrite($logfile,$query."\r\n\r\n");

	/*execute user types query*/
	$places = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$place = mysqli_fetch_assoc($places);
	fwrite($logfile,$place['description']."\r\n\r\n");
	fclose($logfile);

	?>

	<form	id="warehouseForm"
			name="warehouseForm"
			action="include/warehouses.php"
			method="post">
		<input 	type="hidden"
				id="func"
				name="func"
				value="edit"/>
		<input	type="hidden"
				id="warehouseId"
				name="warehouseId"
				value="<?php echo($_POST['warehouseId']); ?>"/>
		<table>
			<tr>
				<td class="warehouseFormLabel">
					Name
				</td>
				<td>
					<input	type="text"
							id="nameInput"
							name="nameInput"
							value="<?php echo $place['name']?>"/>
				</td>
				<td	id="warehouseNameCheck" class="userFormCheckCell">
				</td>
			</tr>
			<tr>
				<td class="warehouseFormLabel">
					Description
				</td>
				<td>
					<textarea	cols="20"	rows="3"
								id="warehouseDescriptionInput"
								name="warehouseDescriptionInput"><?php echo $place['description']?></textarea>
				</td>
				<td	id="descriptionCheck" class="userFormCheckCell">
				</td>
			</tr>
			<tr>
				<td class="warehouseFormLabel">
					Warehouse Type
				</td>
				<td>
					<select	id="placeTypeSelect"
							name="placeTypeSelect">
					<?php
					while($place_type = mysqli_fetch_assoc($places_types))
					{
						?>
						<option value="<?php echo $place_type['id']?>"
							<?php echo(($place['id_places_types']==$place_type['id'])?"selected":"");?>>
							<?php echo $place_type['name']?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="placeTypeCheck" class="userFormCheckCell">
				</td>
			<tr>
				<td colspan="3">
					<input	type="submit"
							id="warehouseFormSubmit"
							value="SUBMIT"/>
					<input	type="reset"
							id="warehouseFormReset"
							value="RESET"/>
				</td>
			</tr>
		</table>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($places_types) || (is_object($places_types) && (get_class($places_types) == "mysqli_result"))) ? true : false);
	((mysqli_free_result($places) || (is_object($places) && (get_class($places) == "mysqli_result"))) ? true : false);
}
elseif($func=='add')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query="INSERT INTO places(	id_places_types,
								name,
								description)
				VALUES(	  \"".$_POST["placeTypeSelect"]."\","
						."\"".$_POST["nameInput"]."\","
						."\"".$_POST["warehouseDescriptionInput"]
						."\")";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo("0Warehouse ".$_POST["userNameInput"]." added successfully.");
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo("No insert executed.");
	}
	else
	{
		echo("Warehouse could not be added, mysql error.");
	}
	return;
}
elseif($func=='edit')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE places";
	$query	.=	" SET name=\"".$_POST['nameInput']."\"";
	$query	.=	",id_places_types=\"".$_POST['placeTypeSelect']."\"";
	$query	.=	",description=\"".$_POST['warehouseDescriptionInput']."\"";
	$query  .=	" WHERE id=\"".$_POST['warehouseId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error: ".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo("0Warehouse updated successfully.");
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo("Nothing to update.");
	}
	else
	{
		echo("Warehouse could not be updated, mysql error.");
	}
	return;
}

elseif($func=='deactivate')
{
	if(!$_POST['warehouseId'])
	{
		die('No warehouse id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE places";
	$query	.=	" SET active=\"0\"";
	$query  .=	" WHERE id=\"".$_POST['warehouseId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0Warehouse successfully deactivated";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "Warehouse already inactive";
	}
	else
	{
		echo "Warehouse could not be deactivated, mysql error";
	}
	return;
}

elseif($func=='activate')
{
	if(!$_POST['warehouseId'])
	{
		die('No warehouse id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE places";
	$query	.=	" SET active=\"1\"";
	$query  .=	" WHERE id=\"".$_POST['warehouseId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0Warehouse successfully activated";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "Warehouse already active";
	}
	else
	{
		echo "Warehouse could not be activated, mysql error";
	}
	return;
}
?>
