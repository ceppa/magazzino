<?php
$commit=strlen($_REQUEST["commit"]);
$clean=strlen($_REQUEST["clean"]);

function queryWithRollback($query)
{
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
	if(!$result)
	{
		$error=((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
		mysqli_query($GLOBALS["___mysqli_ston"], "ROLLBACK");
		die("$query<br>$error");
	}
	return ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

$myhost='localhost';
$myuser='root';
$mypass='minair';
$mydb='warehouse';
$id_simulators=1;


$fields=array("pn_supplier"=>0,"pn_manufacturer"=>0,
			"description"=>0,"supplier"=>0,"manufacturer"=>0,
			"subsystem"=>0,"qty"=>0,"location"=>0,"sn"=>0,"owner"=>0,
			"note"=>0,"cage_code"=>0);
$mandatory=array("pn_supplier","description","supplier","subsystem","qty");

$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
	or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
((bool)mysqli_query($conn, "USE " . $mydb))
	or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

if($clean)
{
	$query="delete from places where id_places_types in (3,5)";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$query="truncate table items";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$query="truncate table movements";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));	$query="truncate table movements";
	$query="truncate table movements_items";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));	$query="truncate table movements";
	$query="truncate table parts";
	mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
}

if(count($_FILES))
{
	mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION")
		or die("Start Transaction<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$row = 1;
	$handle = fopen($_FILES["userfile"]["tmp_name"], "r");
	$headers = array_flip(fgetcsv($handle));
	if($headers===FALSE)
		die("file male");

	$male=0;

	foreach($fields as $field=>$foo)
	{
		if(!isset($headers[$field]))
		{
			$male=1;
			echo "manca il campo $field<br>";
		}
		else
			$fields[$field]=$headers[$field];
	}

	if($male)
		die();

	$valori=array();
	$item=array();
	$i=1;
	$subsystems=array();
	$manufacturers=array();
	$suppliers=array();
	$owners=array();
	while (($linea = fgetcsv($handle)) !== FALSE)
	{
		$row++;
		foreach($mandatory as $m)
			if(!strlen(trim($linea[$fields[$m]])))
				die("manca $m in linea $i");
		if(trim($linea[$fields["qty"]])==0)
			die("quantità nulla in linea $i");
		if(strlen(trim($linea[$fields["sn"]])))
		{
			if(count(explode(";",(trim($linea[$fields["sn"]]))))
					!=trim($linea[$fields["qty"]]))
				die(sprintf("quantità non corrisponde in linea %d<br>
						volevo %d ho %d<br>%s",$i,count(explode(";",(trim($linea[$fields["sn"]])))),
									trim($linea[$fields["qty"]]),
									trim($linea[$fields["sn"]])));
		}
		$suppliers[trim($linea[$fields["supplier"]])]=$i;
		$subs=explode(";",trim($linea[$fields["subsystem"]]));
		for($j=0;$j<count($subs);$j++)
			$subsystems[trim($subs[$j])]=$i;
		if(strlen(trim($linea[$fields["manufacturer"]])))
			$manufacturers[trim($linea[$fields["manufacturer"]])]=$i;
		if(strlen(trim($linea[$fields["owner"]])))
			$owners[trim($linea[$fields["owner"]])]=$i;
		$i++;
		foreach($fields as $field=>$id)
			$item[$field]=trim($linea[$id]);

//		$valori[trim($linea[$fields["supplier"]])][$item["note"]][]=$item;
		$valori[trim($linea[$fields["supplier"]])][0][]=$item;
	}
	fclose($handle);


	foreach($owners as $owner=>$foo)
	{
		$query="SELECT id FROM owners 
			WHERE id_simulators=$id_simulators
			AND name='$owner'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
		$row=mysqli_fetch_assoc($result);
		if(!$row)
		{
			echo "inserisci owner '$owner'<br>";
			$male=1;
		}
		else
			$owners[$owner]=$row["id"];
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	}

	foreach($suppliers as $supplier=>$foo)
	{
		$query="SELECT id FROM places 
			WHERE id_simulators=$id_simulators
			AND name='$supplier'
			AND id_places_types=3";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
		$row=mysqli_fetch_assoc($result);
		if(!$row)
		{
			echo "inserisci supplier '$supplier'<br>";

			$query="INSERT INTO places(id_places_types,name,id_simulators)
					VALUES(3,'$supplier',$id_simulators)";
			$id=queryWithRollback($query);
			$male=1;
		}
		else
			$id=$row["id"];
		$suppliers[$supplier]=$id;
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	}

	foreach($subsystems as $subsystem=>$foo)
	{
		$query="SELECT id FROM subsystems 
			WHERE id_simulators=$id_simulators
			AND text='$subsystem'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
		$row=mysqli_fetch_assoc($result);
		if(!$row)
		{
			echo "inserisci subsystem '$subsystem'<br>";
			$query="INSERT INTO subsystems(id_simulators,text)
					VALUES($id_simulators,'$subsystem')";
			$id=queryWithRollback($query);
			$male=1;
		}
		else
			$id=$row["id"];
		$subsystems[$subsystem]=$id;
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	}

	foreach($manufacturers as $manufacturer=>$foo)
	{
		$query="SELECT id FROM places 
			WHERE id_simulators=$id_simulators
			AND name='$manufacturer'
			AND id_places_types=5";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);
		$row=mysqli_fetch_assoc($result);
		if(!$row)
		{
			echo "inserisci manufacturer '$manufacturer'<br>";
			$query="INSERT INTO places(id_places_types,name,id_simulators)
					VALUES(5,'$manufacturer',$id_simulators)";
			$id=queryWithRollback($query);
			$male=1;
		}
		else
			$id=$row["id"];
		$manufacturers[$manufacturer]=$id;
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	}
/*	if($male)
		die();*/


	$serials=array();
	foreach($valori as $supplier=>$v)
	{
		if(isset($new_items))
			unset($new_items);
		$new_items=array();
		foreach($v as $owner=>$val)
		{
			foreach($val as $note=>$parts)
			{
				$sn=$parts["sn"];
				$pn=trim($parts["pn_supplier"]);
				$qty=$parts["qty"];

				if(strlen(trim($sn)))
				{
					$sns=explode(";",$sn);
					for($i=0;$i<$qty;$i++)
					{
						if(isset($serials[$pn][trim($sns[$i])]))
							die("duplicate serial ".$sns[$i]." for item $pn");
						else
							$serials[$pn][trim($sns[$i])]=1;
					}
				}
			}
		}
	}


//	$male=0;
	foreach($valori as $supplier=>$v)
	{
		if(isset($new_items))
			unset($new_items);
		$new_items=array();
		foreach($v as $owner=>$val)
		{
			foreach($val as $note=>$parts)
			{
				$id_suppliers=$suppliers[$supplier];
				$id_manufacturers=$manufacturers[$parts["manufacturer"]];
				$id_owners=$owners[$parts["owner"]];
				$subs=explode(";",trim($parts["subsystem"]));
				$id_subsystems=$subsystems[trim($subs[0])];
				$id_subsystems2=$subsystems[trim($subs[1])];
				$id_subsystems3=$subsystems[trim($subs[2])];
				$description=$parts["description"];
				$pnManufacturer=$parts["pn_manufacturer"];
				$pnSupplier=$parts["pn_supplier"];
				$id_user_creator=$id_simulators;
				$id_user_updater=$id_simulators;
				$cage_code=$parts["cage_code"];
				$deperibility=$parts["deperibility"];
				if(!strlen($deperibility))
					$deperibility='R';


				$sn=$parts["sn"];
				$qty=$parts["qty"];
				$location=$parts["location"];


				$query="SELECT id FROM parts WHERE
							id_suppliers='$id_suppliers'
							AND id_manufacturers='$id_manufacturers'
							AND id_simulators='$id_simulators'
							AND pn_manufacturer='$pnManufacturer'
							AND pn_supplier='$pnSupplier'";
				$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				if($row=mysqli_fetch_assoc($result))
				{
					echo "part $pnSupplier already present<br>";
					$male++;
					$id_parts=$row["id"];
					((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
				}
				else
				{
					$query="INSERT INTO parts
						(
							id_suppliers,
							id_manufacturers,
							id_simulators,
							description,
							pn_manufacturer,
							pn_supplier,
							cage_code,
							id_users_creator,
							id_users_updater,
							id_subsystems,
							id_subsystems2,
							id_subsystems3,
							CRD
						)
						VALUES
						(
							'$id_suppliers',
							'$id_manufacturers',
							'$id_simulators',
							'$description',
							'$pnManufacturer',
							'$pnSupplier',
							'$cage_code',
							'$id_user_creator',
							'$id_user_updater',
							'$id_subsystems',
							'$id_subsystems2',
							'$id_subsystems3',
							'$deperibility'
						)";
					$id_parts=queryWithRollback($query);
				}

				if(strlen(trim($sn)))
				{
					$sns=explode(";",$sn);
					for($i=0;$i<$qty;$i++)
					{
						$new_items[]=array
						(
							"id_parts"=>$id_parts,
							"sn"=>$sns[$i],
							"qty"=>1,
							"location"=>$location,
							"owner"=>$owner
						);
					}
				}
				else
				{
					$new_items[]=array
					(
						"id_parts"=>$id_parts,
						"sn"=>"",
						"qty"=>$qty,
						"location"=>$location,
						"owner"=>$owner
					);
				}
			}
		}

		$query="INSERT INTO movements
			(
				insert_date,
				id_places_from,
				id_places_to,
				in_transit,
				note,
				id_users,
				id_simulators
			)
			VALUES
			(
				'".$_POST["data"]."',
				'$id_suppliers',
				'".$_POST["to"]."',
				0,
				'',
				'$id_simulators',
				'$id_simulators'
			)";
		$id_movement=queryWithRollback($query);
//print_r($new_items);
echo "<br>movement:$id_movement<br>";
		foreach($new_items as $item)
		{
			$id_parts=$item["id_parts"];
			$sn=$item["sn"];
			$qty=$item["qty"];
			$location=$item["location"];
			$owner=$item["owner"];

			for($i=0;$i<$qty;$i++)
			{
				if(strlen(trim($sn)))
					$query="INSERT INTO items
						(
							id_parts,
							id_places,
							id_owners,
							sn,
							location,
							id_users_creator,
							id_users_updater
						)
						VALUES
						(
							'$id_parts',
							'".$_POST["to"]."',
							'$owner',
							'$sn',
							'$location',
							'$id_simulators',
							'$id_simulators'
						)";
				else
					$query="INSERT INTO items
						(
							id_parts,
							id_places,
							id_owners,
							sn,
							location,
							id_users_creator,
							id_users_updater
						)
						VALUES
						(
							'$id_parts',
							'".$_POST["to"]."',
							'$owner',
							null,
							'$location',
							'$id_simulators',
							'$id_simulators'
						)";
				$id_item=queryWithRollback($query);

				$query="INSERT INTO movements_items
					( 
						id_movements, 
						id_items,
						new_from_supplier
					)
					VALUES
					(
						'$id_movement',
						'$id_item',
						1
					)";
				queryWithRollback($query);
			}
		}
	}
	if((!$male)||($commit))
	{
		mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT")
			or die("Commit<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		echo "commit";
	}

}
else
{
	$query="SELECT * FROM places";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$places=array();
	while($row=mysqli_fetch_assoc($result))
		$places[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
?> 
	<FORM ENCTYPE="multipart/form-data" ACTION="upload.php" METHOD="POST">
	<INPUT TYPE="hidden" NAME="MAX_FILE_SIZE" VALUE="200000">
	<table>
		<tr>
			<td>Data</td>
			<td>
				<input name="data" type="text" value="<?=date("Y-m-d")?>">
			</td>
		</tr>
		<tr>
			<td>To:</td>
			<td>
				<select name="to">
<?
		foreach($places as $id=>$place)
		{?>
					<option value="<?=$id?>"><?=$place?></option>
		<?
		}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Seleziona File</td>
			<td><INPUT NAME="userfile" TYPE="file"></td>
		</tr>
		<tr>
			<td>commit</td>
			<td><INPUT NAME="commit" TYPE="checkbox"></td>
		</tr>
		<tr>
			<td>clean</td>
			<td><INPUT NAME="clean" TYPE="checkbox"></td>
		</tr>
		<tr>
			<td colspan="2">
				<INPUT TYPE="submit" NAME="submit" VALUE="Invia File">
			</td>
		</tr>
	</table>
	</form>
<?php
}
((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
?>

