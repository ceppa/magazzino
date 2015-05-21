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


/*$fields=array("pn_supplier"=>0,"pn_manufacturer"=>0,
			"description"=>0,"supplier"=>0,"manufacturer"=>0,
			"subsystem"=>0,"qty"=>0,"location"=>0,"sn"=>0,"owner"=>0,
			"note"=>0,"cage_code"=>0);
$mandatory=array("pn_supplier","description","supplier","subsystem","qty");
*/
$fields=array("subsystem"=>0,"pn_supplier"=>0,"pn_manufacturer"=>0,
			"description"=>0,"supplier"=>0,
			"qty"=>0,"imballo"=>0,"sn"=>0,"spedizione"=>0);
$mandatory=array("pn_supplier","description","supplier","qty");

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

		if(substr(trim($linea[$fields["spedizione"]]),0,1)=="(")
		{
			$qty=substr(trim($linea[$fields["spedizione"]]),1,strlen(trim($linea[$fields["spedizione"]]))-3);
			$item["qty"]=$qty;
			$linea[$fields["spedizione"]]="";
		}
		else
		{
			if($linea[$fields["sn"]]!=$linea[$fields["spedizione"]])
				echo $linea[$fields["sn"]]." ____ ".$linea[$fields["spedizione"]]."<br>";
		}
		if(strtolower(trim($linea[$fields["imballo"]]))=="magazzino ronchi")
		{
			echo trim($linea[$fields["imballo"]])."<br>";
		}
		else
		{
			$sns=explode(";",(trim($linea[$fields["spedizione"]])));
			if(strlen($linea[$fields["spedizione"]]))
				$item["qty"]=1;
/*		if((count($sns)!=trim($linea[$fields["qty"]]))
				&& strlen(trim($linea[$fields["sn"]])))
			die(sprintf("quantità non corrisponde in linea %d<br>
					volevo %d ho %d<br>%s",$i,count(explode(";",(trim($linea[$fields["sn"]])))),
							trim($linea[$fields["qty"]]),
							trim($linea[$fields["sn"]])));
		else*/
			{
				foreach($sns as $sn)
				{
					$item["sn"]=$sn;
					if(count($sns)>1)
						$item["qty"]=1;
					$valori[]=array
					(
						"pn_supplier"=>$item["pn_supplier"],
						"pn_manufacturer"=>$item["pn_manufacturer"],
						"sn"=>$item["sn"],
						"qty"=>$item["qty"],
						"imballo"=>$item["imballo"],
						"spedizione"=>$item["spedizione"]
					);
				}
			}
		}
	}
	fclose($handle);


	$serials=array();
	foreach($valori as $val)
	{
		$sn=trim($val["sn"]);
		$pn=trim($val["pn_supplier"]);
		$qty=$val["qty"];
		if(strlen($sn))
		{
			if(isset($serials[$pn][$sn]))
				die("duplicate serial ".$sn." for item $pn");
			else
				$serials[$pn][$sn]=1;
		}
	}

	$male=1;
	?>
	<table>
		<tr>
			<td>pn_supplier</td>
			<td>pn_manufacturer</td>
			<td>sn</td>
			<td>qty</td>
		</tr>
	<?
	foreach($valori as $v)
	{
		$pn_supplier=trim($v["pn_supplier"]);
		$pn_manufacturer=trim($v["pn_manufacturer"]);
		$sn=trim($v["sn"]);
		$spedizione=trim($v["spedizione"]);
		$imballo=trim($v["imballo"]);
		$qty=$v["qty"];
		$sncondition=(strlen($sn)?"items.sn='$sn' ":"items.sn IS NULL ");
		$query="SELECT items.id,items.id_places FROM items 
				LEFT JOIN parts ON items.id_parts=parts.id
				WHERE
				parts.pn_supplier='$pn_supplier'
				AND parts.pn_manufacturer='$pn_manufacturer'
				AND $sncondition
				AND spare=-1 
				LIMIT $qty";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if(mysqli_num_rows($result)<$qty)
		{
		?>
		<tr>
			<td><?="-$pn_supplier-"?></td>
			<td><?="-$pn_manufacturer-"?></td>
			<td><?="-$sn-"?></td>
			<td><?=($qty." - ".mysqli_num_rows($result))?></td>
		</tr>
		<?
		}
		else
		{
			while($row=mysqli_fetch_assoc($result))
			{
				$query="UPDATE items SET spare=0 WHERE id=".$row["id"];
				mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				if($row["id_places"]==2)
				{
					$query="UPDATE items SET 
						id_places=45,
						location='$imballo' 
						WHERE id=".$row["id"];
					mysqli_query($GLOBALS["___mysqli_ston"], $query)
						or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				}
				else
				{?>
				<tr>
					<td><?="-$pn_supplier-"?></td>
					<td><?="-$pn_manufacturer-"?></td>
					<td><?="-$sn-"?></td>
					<td><?=($qty)?></td>
				</tr>
<?
				}

				$query="INSERT INTO movements_items(id_movements,id_items)
						VALUE(1085,".$row["id"].")";
				mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			}
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		
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
	<FORM ENCTYPE="multipart/form-data" ACTION="check.php" METHOD="POST">
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

