<?
	date_default_timezone_set("Europe/Rome");
	ini_set('include_path',get_include_path().PATH_SEPARATOR.'/home/hightecs/php');
	ini_set('error_reporting',E_ALL & ~E_NOTICE );

	/* connect to database */
	require_once('mysql.php');
	require_once('functions.php');

	$mediumWidth=25;
	$shortWidth=20;
	$longWidth=40;
	/* per avere la roba del $_SESSION */
	ini_set ('session.name', 'magazzino');
	ini_set('error_reporting',E_ALL);
	session_start();

	$func=(isset($_REQUEST["func"])?$_REQUEST["func"]:"");
	if($func=="compatiblesForm")
	{
		require_once("compatibles.php");
		compatibles_menu();
	}
	elseif($func=="mergeForm")
	{
		mergeForm();
	}
	elseif($func=="doMerge")
	{
		$id_part_alive=$_POST["id_part_alive"];
		$id_part_dead=$_POST["id_part_dead"];
		if(	doMerge($id_part_alive,$id_part_dead))
			echo "ok";
	}


function doMerge($id_part_alive,$id_part_dead)
{
	global $myhost,$myuser,$mypass,$mydb;
	require_once('mysql.php');
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($conn , "USE " . $mydb));
	mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION")
		or die("Start Transaction<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$query="UPDATE items SET 
				id_parts='$id_part_alive'
			WHERE id_parts='$id_part_dead'";
	queryWithRollback($query);
	$query="SELECT count(id) AS conta FROM items 
			WHERE id_parts='$id_part_dead'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$row=mysqli_fetch_assoc($result);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	if($row["conta"]==0)
	{
		$compatibles=array();
		$query="SELECT id_parts,id_compatible FROM compatible_parts 
				WHERE id_parts='$id_part_alive' OR id_parts='$id_part_dead'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		while($row=mysqli_fetch_assoc($result))
			$compatibles[$row["id_parts"]]=$row["id_compatible"];
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

		if(isset($compatibles[$id_part_dead]))
		{
			if(isset($compatibles[$id_part_alive]))
			{
				if($compatibles[$id_part_alive]!=$compatibles[$id_part_dead])
					die("Compatible parts don't match");
				 
			}
			else
			{
				$query="UPDATE compatible_parts SET id_parts='$id_part_alive' WHERE id_parts='$id_part_dead'";
				queryWithRollback($query);
			}
		}
		$query="DELETE FROM parts WHERE id='$id_part_dead'";
		queryWithRollback($query);

		$query="DELETE FROM compatible_parts WHERE id_parts='$id_part_dead'";
		queryWithRollback($query);
		mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT")
			or die("Commit<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	}



	return 1;
}

function mergeForm()
{
	global $myhost,$myuser,$mypass,$mydb;
	require_once('mysql.php');
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($conn , "USE " . $mydb));
	$query="SELECT pn_supplier,
				GROUP_CONCAT(parts.id ORDER BY parts.id SEPARATOR 'ŧ') AS id,
				GROUP_CONCAT(IFNULL(pn_manufacturer,'') 
					ORDER BY parts.id SEPARATOR 'ŧ') AS pn_manufacturer,
				GROUP_CONCAT(IFNULL(parts.description,'') ORDER BY parts.id SEPARATOR 'ŧ') AS description,
				GROUP_CONCAT(IFNULL(manufacturer.name,'') ORDER BY parts.id SEPARATOR 'ŧ') AS manufacturer,
				GROUP_CONCAT(IFNULL(supplier.name,'') ORDER BY parts.id SEPARATOR 'ŧ') AS supplier
			FROM parts 
				LEFT JOIN places AS manufacturer
					ON parts.id_manufacturers=manufacturer.id
				LEFT JOIN places AS supplier
					ON parts.id_suppliers=supplier.id
			WHERE parts.id_simulators='".$_SESSION["simulator_id"]."'
			GROUP BY pn_supplier
			HAVING count(pn_supplier)>1";
	
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

?>
		<fieldset>
		<legend>Parts merge</legend>
		<table>
			<tr>
				<td>Part that will survive</td>
				<td>
					<input size='80' maxlength='80' type="text" 
						id="alivePart">
					<input type="text" id="id_part_alive" value="">
				</td>
			</tr>
			<tr>
				<td>Part that will die</td>
				<td>
					<input size='80' maxlength='80' type="text" 
						id="deadPart">
					<input type="text" id="id_part_dead" value="">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="button" value="merge" onclick="doMerge()">
				</td>
			</tr>
		</table>
		</fieldset>

		<fieldset>
		<legend>Possible duplications</legend>

		<table>
			<tr>
				<td class="fieldTitle">P/N supplier</td>
				<td class="fieldTitle">Parts</td>
			</tr>
<?
	while($row=mysqli_fetch_array($result))
	{
		$pn_supplier=$row["pn_supplier"];
		$id_parts=explode("ŧ",$row["id"]);
		$pn_manufacturers=explode("ŧ",$row["pn_manufacturer"]);
		$descriptions=explode("ŧ",$row["description"]);
		$manufacturers=explode("ŧ",$row["manufacturer"]);
		$suppliers=explode("ŧ",$row["supplier"]);
?>
			<tr>
				<td><?=$pn_supplier?></td>
				<td>
					<table class="compatible">
						<tr>
							<td class="fieldTitle">id_parts</td>
							<td class="fieldTitle">pn_manufacturer</td>
							<td class="fieldTitle">descriptions</td>
							<td class="fieldTitle">manufacturer</td>
							<td class="fieldTitle">supplier</td>
							<td class="fielsTable"></td>
							<td class="fielsTable"></td>
						</tr>

<?
					foreach($id_parts as $id=>$id_part)
					{?>
						<tr>
							<td><?=$id_part?></td>
							<td><?=$pn_manufacturers[$id]?></td>
							<td><?=$descriptions[$id]?></td>
							<td><?=$manufacturers[$id]?></td>
							<td><?=$suppliers[$id]?></td>
							<td>
								<input type="button" value="survive"
									onclick="survive_part(<?=$id_part?>,
										'<?=$pn_manufacturers[$id]?>')">
							</td>
							<td>
								<input type="button" value="die"
									onclick="kill_part(<?=$id_part?>,
										'<?=$pn_manufacturers[$id]?>')">
							</td>
						</tr>
					<?}?>
					</table>
				</td>
			</tr>
	<?}?>		
		</table>

		</fieldset>
<?
}

?>
