<?php

/* connect to database */
require_once('mysql.php');
require_once('functions.php');

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
	
	if (!$sortname) $sortname = 'places.name';
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

	$query = @ifnull($_REQUEST['query']);
	$qtype = @ifnull($_REQUEST['qtype']);

	if(@ifnull($_POST['whereString'])!='')
	{
		$where = "WHERE ".$_POST['whereString']." ";
		$where = str_replace('\"','"',$where);
	}
	else $where = "WHERE 1 ";

	if($qtype) {
		if($qtype == "name" && $query!='') 
			$where .= " AND (places.name LIKE '%$query%') ";
		elseif($qtype == "description" && $query!='') 
			$where .= " AND (places.description LIKE '%$query%') ";
	}
	
	$where .= " AND places.id_places_types != 0 ";
	$where .= " AND simulators.id='".$_SESSION["simulator_id"]."'";

	$sql =	"SELECT	places.id AS places_id "
		.	"FROM places "
		.	"LEFT JOIN simulators ON simulators.id = places.id_simulators "
		.	$where;
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$total = mysqli_num_rows($result);
	
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	
	$sql =	"SELECT	places.id AS places_id,"
				.	"places.name AS places_name,"
				.	"places.description AS places_description,"
				.	"places.address AS places_address,"
				.	"simulators.name AS simulator_name,"
				.	"places_types.name AS places_type,"
				.	"places.contact_name AS contact_name,"
				.	"places.contact_email AS contact_email,"
				.	"places.active AS active "
		.	"FROM places "
		.	"LEFT JOIN simulators ON simulators.id = places.id_simulators "
		.	"LEFT JOIN places_types ON places_types.id = places.id_places_types "
		.	$where . $sort . $limit;
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
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
		$address=nlTobr($row['places_address']);
		$description=nlTobr($row['places_description']);

		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$row['places_id']."',";
		$json .= "cell:['".addslashes(($row['places_name']=="")?"---":$row['places_name'])."'";
		$json .= ",'".addslashes($description==""?"---":$description)."'";
		/* Must replace both \n and \r\n with <br> to maintain JSON syntax */
		$json .= ",'".addslashes($address==""?"---":$address)."'";
		$json .= ",'".addslashes(($row['places_type']=="")?"---":ucfirst($row['places_type']))."'";
		$json .= ",'".addslashes(($row['contact_name']=="")?"---":ucfirst($row['contact_name']))."'";
		$json .= ",'".addslashes(($row['contact_email']=="")?"---":ucfirst($row['contact_email']))."'";
		$json .= ",'".($row['active']==0?"-":"YES")."'";
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
/* newPlaceForm															   */
/*																			*/
/******************************************************************************/
elseif($func=='newPlaceForm')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare places_types query*/
	$query = "SELECT * FROM places_types"
			. " WHERE id != 0";

	/*execute places_types query*/
	$places_types = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	?>
	<form	id="placeForm"
			name="placeForm"
			action="include/places.php"
			method="post">
		<fieldset>
		<legend>New Place</legend>
		<input 	type="hidden"
				id="func"
				name="func"
				value="add"/>
		<table>
			<tr>
				<td class="fieldLabel">
					Place type
				</td>
				<td>
					<select	id="placeTypeSelect"
							name="placeTypeSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select type...</option>
					<?php
					while($places_type = mysqli_fetch_assoc($places_types))
					{
						?>
						<option value="<?php echo $places_type['id']?>">
							<?php echo ucfirst($places_type['name'])?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="placeTypeCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Name
				</td>
				<td>
					<input	type="text"
							id="placeNameInput"
							name="placeNameInput"
							class="longTextInput"/>
				</td>
				<td	id="placeNameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Address
				</td>
				<td>
					<textarea	rows="5"
							id="addressInput"
							name="addressInput"
							class="longTextInput"></textarea>
				</td>
				<td	id="addressCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Contact name
				</td>
				<td>
					<input	type="text"
							id="contactNameInput"
							name="contactNameInput"
							class="longTextInput"/>
				</td>
				<td	id="contactNameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Contact email
				</td>
				<td>
					<input	type="text"
							id="contactEmailInput"
							name="contactEmailInput"
							class="longTextInput"/>
				</td>
				<td	id="contactEmailCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Notes
				</td>
				<td>
					<textarea	rows="5"
								id="placeDescriptionInput"
								name="placeDescriptionInput"
								class="longTextInput"></textarea>
				</td>
				<td	id="placeDescriptionCheck" class="fieldCheck">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($places_types) || (is_object($places_types) && (get_class($places_types) == "mysqli_result"))) ? true : false);
}

/******************************************************************************/
/*																			*/
/* editPlaceForm															   */
/*																			*/
/******************************************************************************/
elseif($func=='editPlaceForm')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare places_types query*/
	$query = "SELECT * FROM places_types"
			. " WHERE id != 0";

	/*execute places_types query*/
	$places_types = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/* get place info to populate form */
	$query = "SELECT * FROM places WHERE id = ".$_POST['placeId'];

	$places = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$place = mysqli_fetch_assoc($places);
	?>
	<form	id="placeForm"
			name="placeForm"
			action="include/places.php"
			method="post">
		<fieldset>
		<legend>New Place</legend>
		<input 	type="hidden"
				id="func"
				name="func"
				value="edit"/>
		<input	type="hidden"
				id="placeId"
				name="placeId"
				value="<?php echo($_POST['placeId']); ?>"/>
		<table>
			<tr>
				<td class="fieldLabel">
					Place type
				</td>
				<td>
					<select	id="placeTypeSelect"
							name="placeTypeSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select type...</option>
					<?php
					while($places_type = mysqli_fetch_assoc($places_types))
					{
						?>
						<option value="<?php echo $places_type['id']?> "
							<?php echo(($place['id_places_types']==$places_type['id'])?"selected":"");?>>
							<?php echo ucfirst($places_type['name'])?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="placeTypeCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Name
				</td>
				<td>
					<input	type="text"
							id="placeNameInput"
							name="placeNameInput"
							class="longTextInput"
							value="<?php echo $place['name']?>"/>
				</td>
				<td	id="placeNameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Address
				</td>
				<td>
					<textarea	rows="5"
							id="addressInput"
							name="addressInput"
							class="longTextInput"><?php echo $place['address']?></textarea>
				</td>
				<td	id="addressCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Contact name
				</td>
				<td>
					<input	type="text"
							id="contactNameInput"
							name="contactNameInput"
							class="longTextInput"
							value="<?php echo $place['contact_name']?>"/>
				</td>
				<td	id="contactNameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Contact email
				</td>
				<td>
					<input	type="text"
							id="contactEmailInput"
							name="contactEmailInput"
							class="longTextInput"
							value="<?php echo $place['contact_email']?>"/>
				</td>
				<td	id="contactEmailCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Notes
				</td>
				<td>
					<textarea	rows="5"
								id="placeDescriptionInput"
								name="placeDescriptionInput"
								class="longTextInput"><?php echo $place['description']?></textarea>
				</td>
				<td	id="placeDescriptionCheck" class="fieldCheck">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($places_types) || (is_object($places_types) && (get_class($places_types) == "mysqli_result"))) ? true : false);
}

/******************************************************************************/
/*																			*/
/* try to INSERT new place into DB											*/
/*																			*/
/******************************************************************************/
elseif($func=='add')
{
	$emailPattern = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/";
	
	/* If email isn't empty check it with a regular expression */
	if($_POST['contactEmailInput']!='') {
		if(!preg_match($emailPattern,$_POST['contactEmailInput'])) {
			die("Bad email format");
		}
	}
	/* Check for other necessary fields */
	if($_POST['placeTypeSelect']=='') {
		die("Place Type not selected");
	}
	if($_POST['placeNameInput']=='') {
		die("Place name required");
	}
	
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	/* Prepare place INSERT */
	$sql = 	"INSERT INTO places ( 
				id_places_types,
				name,
				description,
				address,
				id_simulators,
				contact_name,
				contact_email )
			VALUES ( 
				\"".$_POST['placeTypeSelect']."\",
				\"".$_POST['placeNameInput']."\",
				\"".$_POST['placeDescriptionInput']."\",
				\"".$_POST['addressInput']."\",
				\"".$_SESSION['simulator_id']."\",
				\"".$_POST['contactNameInput']."\",
				\"".$_POST['contactEmailInput']."\")";
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\r\n\r\n$sql\r\n\r\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo("0".$_POST["placeNameInput"]." added successfully.");
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo("No insert executed.");
	}
	else
	{
		echo("Place could not be added, mysql error.");
	}
	return;
}

/******************************************************************************/
/*																			*/
/* try to UPDATE existing place											   */
/*																			*/
/******************************************************************************/
elseif($func=='edit')
{
	$emailPattern = "/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})$/";
	
	/* If email isn't empty check it with a regular expression */
	if($_POST['contactEmailInput']!='') {
		if(!preg_match($emailPattern,$_POST['contactEmailInput'])) {
			die("Bad email format");
		}
	}
	/* Check for other necessary fields */
	if($_POST['placeTypeSelect']=='') {
		die("Place Type not selected");
	}
	if($_POST['placeNameInput']=='') {
		die("Place name required");
	}
	
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	/* Prepare place INSERT */
	$sql = 	"UPDATE places SET 
				id_places_types = \"".$_POST['placeTypeSelect']."\",
				name = \"".$_POST['placeNameInput']."\",
				description = \"".$_POST['placeDescriptionInput']."\",
				address = \"".$_POST['addressInput']."\",
				id_simulators = \"".$_SESSION['simulator_id']."\",
				contact_name = \"".$_POST['contactNameInput']."\",
				contact_email = \"".$_POST['contactEmailInput']."\"
			WHERE id = \"".$_POST['placeId']."\"";
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\r\n\r\n$sql\r\n\r\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo("0".$_POST["placeNameInput"]." updated successfully.");
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo("No update executed.");
	}
	else
	{
		echo("Place could not be updated, mysql error.");
	}
	return;
}


/******************************************************************************/
/*																			*/
/* display place details													  */
/*																			*/
/******************************************************************************/
elseif($func=='details')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	if(!is_numeric($_REQUEST['placeId']))
		die("No part selected.");
	
	$sql =	"SELECT	places.name AS places_name,
				places.description AS places_description,
				places.address AS places_address,
				simulators.name AS simulator_name,
				places_types.name AS places_type,
				places.contact_name AS contact_name,
				places.contact_email AS contact_email
			FROM places
			LEFT JOIN simulators ON simulators.id = places.id_simulators
			LEFT JOIN places_types ON places_types.id = places.id_places_types
			WHERE places.id = \"".$_POST['placeId']."\"";
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\r\n\r\n$sql\r\n\r\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$place = mysqli_fetch_assoc($result);
	
	?><fieldset class="partDetails">
	<legend>Place details</legend>
	<table>
		<tr>
			<td colspan="2">
				<h1>
					<?php echo($place['places_name']); ?>
				</h1>
			</td>
		</tr>
		<tr>
			<td>Address</td>
			<td><b><?php echo(str_replace("\n","<br>",str_replace("\r\n","<br>",$place['places_address']))); ?></td>
		</tr>
		<tr>
			<td>Contact name</td>
			<td><b><?php echo($place['contact_name']); ?></td>
		</tr>
		<tr>
			<td>Contact email</td>
			<td><b><?php echo($place['contact_email']); ?></td>
		</tr>
		<tr>
			<td>Type</td>
			<td><b><?php echo($place['places_type']); ?></td>
		</tr>
		<tr>
			<td>Notes</td>
			<td><b><?php echo(str_replace("\n","<br>",str_replace("\r\n","<br>",$place['places_description']))); ?></td>
		</tr>
	</table>
	</fieldset>
	<?php
	
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	
}
/****************************************************************************/
/*																			*/
/* 							activate / deactivate							*/
/*																			*/
/****************************************************************************/
elseif($func=='activate')
{
	$active=$_POST["active"];
	$placeId=$_POST["placeId"];
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$sql =	"UPDATE places SET active='$active'
				WHERE id='$placeId'";
	
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\r\n\r\n$sql\r\n\r\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	
	$n=mysqli_affected_rows($conn);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	echo "$n";
}
?> 
