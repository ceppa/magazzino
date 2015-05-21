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
	if(!isset($_SESSION["simulator_id"]))
	{
		header("location: ../index.php");
		die();
	}


	$func=$_REQUEST["func"];

	$id_simulators=$_SESSION["simulator_id"];
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$query="SELECT * FROM places 
			WHERE  id_simulators='$id_simulators'
			ORDER BY name";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$places=array();
	while($row=mysqli_fetch_assoc($result))
		$places[$row["id"]]=array
			(
				"type"=>$row["id_places_types"],
				"name"=>$row["name"],
				"description"=>$row["description"]
			);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	$query="SELECT * FROM subsystems
			WHERE id_simulators='$id_simulators'
			AND active=1
			ORDER BY line_order,text";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subsystems=array();
	while($row=mysqli_fetch_assoc($result))
		$subsystems[$row["id"]]=$row["text"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	if($func=="reportWareHouseForm")
	{


?>
	<form id="warehouseForm"
			name="warehouseForm"
			action="include/reports.php"
			method="post"
			target="_blank">
		<input 	type="hidden"
				name="func"
				value="printByWarehouse"/>

		<fieldset>
		<legend>stock by warehouse</legend>
		<table>
			<tr>
				<td>select warehouse</td>
				<td>
					<select name="warehouseSelect" id="warehouseSelect">
						<option value="0">all</option>
<?
		foreach($places as $id=>$place)
		{
			if($place["type"]==2)
			{
		?>
						<option value="<?=$id?>">
							<?=$place["name"]?>
						</option>
		<?	}
		}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>select subsystem</td>
				<td>
					<select name="subsystemSelect" id="subsystemSelect">
						<option value="0">all</option>
<?
		foreach($subsystems as $id=>$text)
		{?>
						<option value="<?=$id?>">
							<?=$text?>
						</option>
		<?}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>select sort order</td>
				<td>
					<select name="orderSelect" id="orderSelect">
						<option value="parts.pn_supplier">
							PN Supplier
						</option>
						<option value="items.location,parts.pn_supplier">
							Location, PN Supplier
						</option>
						<option value="subsystems.text,places_sup.name,parts.pn_supplier">
							Subsystem, Supplier, PN Supplier
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="print" value="print">
					<input type="submit" name="csv" value="export">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>
	<form id="deviceForm"
			name="deviceForm"
			action="include/reports.php"
			method="post"
			target="_blank">
		<input 	type="hidden"
				name="func"
				value="printByWarehouse"/>

		<fieldset>
		<legend>stock by device</legend>
		<table>
			<tr>
				<td>select device</td>
				<td>
					<select name="warehouseSelect">
						<option value="0">all</option>
<?
		foreach($places as $id=>$place)
		{
			if($place["type"]==1)
			{
		?>
						<option value="<?=$id?>">
							<?=$place["name"]?>
						</option>
		<?	}
		}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>select subsystem</td>
				<td>
					<select name="subsystemSelect">
						<option value="0">all</option>
<?
		foreach($subsystems as $id=>$text)
		{?>
						<option value="<?=$id?>">
							<?=$text?>
						</option>
		<?}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>select sort order</td>
				<td>
					<select name="orderSelect" id="orderSelect">
						<option value="parts.pn_supplier">
							PN Supplier
						</option>
						<option value="items.location,parts.pn_supplier">
							Location, PN Supplier
						</option>
						<option value="subsystems.text,places_sup.name,parts.pn_supplier">
							Subsystem, Supplier, PN Supplier
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="print" value="print">
					<input type="submit" name="csv" value="export">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>
	<form id="supplierForm"
			name="supplierForm"
			action="include/reports.php"
			method="post"
			target="_blank">
		<input 	type="hidden"
				name="func"
				value="printBySupplier"/>

		<fieldset>
		<legend>stock by supplier / manufacturer</legend>
		<table>
			<tr>
				<td>select supplier</td>
				<td>
					<select name="supplierSelect" id="supplierSelect">
<?
		foreach($places as $id=>$place)
		{
			if($place["type"]>=3)
			{
		?>
						<option value="<?=$id?>">
							<?=$place["name"]?>
						</option>
		<?
			}
		}
?>
					</select>
				</td>
			</tr>
<!--			<tr>
				<td>select sort order</td>
				<td>
					<select name="orderSelect" id="orderSelect">
						<option value="parts.pn_supplier">
							PN Supplier
						</option>
						<option value="items.location,parts.pn_supplier">
							Location, PN Supplier
						</option>
						<option value="subsystems.text,places_sup.name,parts.pn_supplier">
							Subsystem, Supplier, PN Supplier
						</option>
					</select>
				</td>
			</tr>-->
			<tr>
				<td colspan="2">
					<input type="submit" name="print" value="print">
					<input type="submit" name="csv" value="export">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>
	<form id="repairForm"
			name="repairForm"
			action="include/reports.php"
			method="post"
			target="_blank">
		<input 	type="hidden"
				name="func"
				value="printRepair"/>

		<fieldset>
		<legend>items to repair</legend>
		<table>
			<tr>
				<td>select sort order</td>
				<td>
					<select name="orderSelect" id="orderSelect">
						<option value="places.name,parts.description">
							Warehouse
						</option>
						<option value="parts.pn_supplier,items.sn">
							PN Supplier
						</option>
						<option value="parts.description,items.sn">
							Description
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="print" value="print">
					<input type="submit" name="csv" value="export">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>

<?
	}
	elseif($func=="reportMovementsForm")
	{
		$id_simulators=$_SESSION["simulator_id"];
		$fromDate=date("d/m/Y",strtotime("-1 month"));
		$toDate=date("d/m/Y");
		$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $mydb))
			or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query="SELECT * FROM places 
				WHERE  id_simulators='$id_simulators'
				ORDER BY name";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$places=array();
		while($row=mysqli_fetch_assoc($result))
			$places[$row["id"]]=array
				(
					"type"=>$row["id_places_types"],
					"name"=>$row["name"],
					"description"=>$row["description"]
				);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

?>
	<form id="movementForm"
			name="movementForm"
			action="include/reports.php"
			method="post"
			target="_blank">
		<input 	type="hidden"
				name="func"
				value="printByPlace"/>

		<fieldset>
		<legend>movements to/from place</legend>
		<table>
			<tr>
				<td>select warehouse</td>
				<td>
					<select name="placeSelect" id="placeSelect">
						<option value="0">all</option>
<?
		foreach($places as $id=>$place)
		{?>
						<option value="<?=$id?>">
							<?=$place["name"]?>
						</option>
		<?}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td>from date</td>
				<td>
					<input type="text" 
						name="fromDate" 
						id="fromDate"
						value="<?=$fromDate?>" 
						readonly="readonly">
				</td>
			</tr>
			<tr>
				<td>to date</td>
				<td>
					<input type="text" 
						name="toDate" 
						id="toDate"
						value="<?=$toDate?>" 
						readonly="readonly">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="print" value="print">
					<input type="submit" name="csv" value="export">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>
	<form id="pnForm"
			name="pnForm"
			action="include/reports.php"
			method="post"
			target="_blank"
			onsubmit="return ($('#pnInput').val().length>0);">
		<input type="hidden"
				name="func"
				value="printByPn" />

		<fieldset>
		<legend>movements from Part Number</legend>
		<table>
			<tr>
				<td>Select part number</td>
				<td>
					<input type="text"
							name="pnInput"
							id="pnInput">
				</td>
			</tr>
			<tr>
				<td>from date</td>
				<td>
					<input type="text" 
						name="fromDatePn" 
						id="fromDatePn"
						value="<?=$fromDate?>"
						readonly="readonly">
				</td>
			</tr>
			<tr>
				<td>to date</td>
				<td>
					<input type="text" 
						name="toDatePn" 
						id="toDatePn"
						value="<?=$toDate?>">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="print" value="print">
					<input type="submit" name="csv" value="export">
				</td>
			</tr>
		</table>
		</fieldset>
	</form>
	<script type="text/javascript">
		$(function() 
		{
			$("#fromDate").datepicker({ dateFormat: 'dd/mm/yy',firstDay:1 });
			$("#toDate").datepicker({ dateFormat: 'dd/mm/yy',firstDay:1 });
			$("#fromDatePn").datepicker({ dateFormat: 'dd/mm/yy',firstDay:1 });
			$("#toDatePn").datepicker({ dateFormat: 'dd/mm/yy',firstDay:1 });
			$('#pnInput').autocomplete("include/itemsAutocompleteBackend.php", 
				{
					minChars:1, 
					matchSubset:1, 
					matchContains:1, 
					cacheLength:10, 
					formatItem:function(row)
					{
						return "<b>" + row[0] + "</b>" 
						+ "<br><i>" + row[1] + "</i>";
					},
					selectOnly:1,
					mustMatch:1
				}); 

		});
</script>
<?
	}
	elseif($func=="reportFotografia")
	{
		require_once("fotografia.php");
		report_fotografia_menu();
	}
	else
	{
		$csv=isset($_REQUEST["csv"]);
		switch($func)
		{
			case "printByPlace":
				$fromDate=date_to_sql($_POST["fromDate"]);
				$toDate=date_to_sql($_POST["toDate"]);
				if($_POST["placeSelect"]==0)
				{
					$filteredBy=" WHERE (movements.insert_date BETWEEN '$fromDate' AND '$toDate 23:59:59') ";
					$columns[]=array("name"=>"insert_date","text"=>"Date/time","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"user","text"=>"User","width"=>"$shortWidth","align"=>"L");
					$columns[]=array("name"=>"pn_supplier","text"=>"PN Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"part_description","text"=>"Description","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"supplier","text"=>"Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"from","text"=>"From","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"to","text"=>"To","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"note","text"=>"Note","width"=>"$longWidth","align"=>"L");
					$title="Movements overview - from ".$_POST["fromDate"]." to ".$_POST["toDate"];
				}
				else
				{
					$filteredBy="WHERE (places_from.id='".$_POST["placeSelect"]."' 
									OR places_to.id='".$_POST["placeSelect"]."') 
									AND (movements.insert_date BETWEEN '$fromDate' AND '$toDate 23:59:59') ";
					$columns[]=array("name"=>"insert_date","text"=>"Date/time","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"user","text"=>"User","width"=>"$shortWidth","align"=>"L");
					$columns[]=array("name"=>"pn_supplier","text"=>"PN Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"part_description","text"=>"Description","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"supplier","text"=>"Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"from","text"=>"From","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"to","text"=>"To","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"note","text"=>"Note","width"=>"$longWidth","align"=>"L");
		
					$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
						or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					((bool)mysqli_query($conn, "USE " . $mydb))
						or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					$query="SELECT * FROM places 
							WHERE  id='".$_POST["placeSelect"]."'";
					$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
						or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					$row=mysqli_fetch_assoc($result);
					$warehouse=$row["name"];
					((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
					((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
					$title="Movements to/from $warehouse - from ".$_POST["fromDate"]." to ".$_POST["toDate"];
				}
				$orderedBy=" ORDER BY insert_date,pn_supplier,sn ";
				$out=place($filteredBy,$orderedBy);
				break;
			case "printByPn":
				$fromDate=date_to_sql($_POST["fromDatePn"]);
				$toDate=date_to_sql($_POST["toDatePn"]);
				require_once("report_pdf.php");
				$filteredBy=" WHERE (parts.pn_supplier='".$_POST["pnInput"]."' 
								OR parts.pn_manufacturer='".$_POST["pnInput"]."')
								AND (movements.insert_date BETWEEN '$fromDate' AND '$toDate 23:59:59') ";
				$columns[]=array("name"=>"insert_date","text"=>"Date/time","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"user","text"=>"User","width"=>"$shortWidth","align"=>"L");
				$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"supplier","text"=>"Supplier","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"from","text"=>"From","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"to","text"=>"To","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"note","text"=>"Note","width"=>"$longWidth","align"=>"L");
				$title="Movements of part ".$_POST["pnInput"]." - from ".$_POST["fromDatePn"]." to ".$_POST["toDatePn"];
				$orderedBy=" ORDER BY insert_date,pn_supplier,sn ";
				$out=place($filteredBy,$orderedBy);
				break;
			case "printByWarehouse":
				$filteredBy="";
				require_once("report_pdf.php");
				if($_POST["subsystemSelect"]>0)
					$filteredBy=" WHERE parts.id_subsystems='".$_POST["subsystemSelect"]."'";
		
				if($_POST["warehouseSelect"]==0)
				{
					$columns[]=array("name"=>"pn_supplier","text"=>"PN Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"part_description","text"=>"Description","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"supplier","text"=>"Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"pn_manufacturer","text"=>"PN Manufacturer","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"magazzino","text"=>"Warehouse","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"qty","text"=>"Qty","width"=>"10","align"=>"C");
					$columns[]=array("name"=>"location","text"=>"Location","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"subsystem","text"=>"Subsystem","width"=>"$mediumWidth","align"=>"L");
					if(strlen($filteredBy))
						$title=$subsystems[$_POST["subsystemSelect"]]." items";
					else
						$title="Warehouse overview";
				}
				else
				{
					$columns[]=array("name"=>"pn_supplier","text"=>"PN Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"part_description","text"=>"Description","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"supplier","text"=>"Supplier","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"pn_manufacturer","text"=>"PN Manufacturer","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"qty","text"=>"Qty","width"=>"10","align"=>"C");
					$columns[]=array("name"=>"location","text"=>"Location","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
					$columns[]=array("name"=>"subsystem","text"=>"Subsystem","width"=>"$mediumWidth","align"=>"L");
		
					$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
						or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					((bool)mysqli_query($conn, "USE " . $mydb))
						or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					$query="SELECT * FROM places 
							WHERE  id='".$_POST["warehouseSelect"]."'";
					$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
						or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
					$row=mysqli_fetch_assoc($result);
					$warehouse=$row["name"];
					((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
					((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
					if(strlen($filteredBy))
					{
						$filteredBy.=" AND ";
						$title="Items of ".$subsystems[$_POST["subsystemSelect"]]." in warehouse $warehouse";
					}
					else
					{
						$filteredBy=" WHERE ";
						$title="Items in warehouse $warehouse";
					}
					$filteredBy.="places_ware.id='".$_POST["warehouseSelect"]."'";
				}
				$orderedBy="ORDER BY ".$_POST["orderSelect"]." ";
				$out=warehouse($filteredBy,$orderedBy);
				break;
			case "printBySupplier":
				$csv=isset($_REQUEST["csv"]);
		
				$filteredBy="WHERE parts.id_suppliers='".$_POST["supplierSelect"]."'";
				$columns[]=array("name"=>"pn_supplier","text"=>"PN Supplier","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"pn_manufacturer","text"=>"PN Manufacturer","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"part_description","text"=>"Description","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"magazzino","text"=>"Warehouse","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"qty","text"=>"Qty","width"=>"10","align"=>"C");
				$columns[]=array("name"=>"location","text"=>"Location","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"subsystem","text"=>"Subsystem","width"=>"$mediumWidth","align"=>"L");
		
				$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
					or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				((bool)mysqli_query($conn, "USE " . $mydb))
					or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				$query="SELECT * FROM places 
						WHERE id='".$_POST["supplierSelect"]."'";
				$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
					or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				$row=mysqli_fetch_assoc($result);
				$supplier=$row["name"];
				((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
				((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
				$title="Items from supplier : $supplier";
		//		$orderedBy="ORDER BY ".$_POST["orderSelect"]." ";
				$orderedBy="ORDER BY parts.pn_supplier,items.sn,items.location ";
				$out=warehouse($filteredBy,$orderedBy);
				break;
			case "printRepair":
				$csv=isset($_REQUEST["csv"]);
				$id_simulators=$_SESSION["simulator_id"];
				$filteredBy=" WHERE parts.id_simulators='$id_simulators'";
				$columns[]=array("name"=>"part_description","text"=>"Description","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"pn_supplier","text"=>"PN Supplier","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"pn_manufacturer","text"=>"PN Manufacturer","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"sn","text"=>"Serial Number","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"magazzino","text"=>"Warehouse","width"=>"$mediumWidth","align"=>"L");
				$columns[]=array("name"=>"location","text"=>"Note","width"=>"$mediumWidth","align"=>"L");
				$title="Items to repair";
				$orderedBy=$_POST["orderSelect"];
				$orderedBy="ORDER BY $orderedBy";
				$out=itemsToRepair($filteredBy,$orderedBy);
				break;
			default:
				die();
		}
		if($csv)
		{
			require_once("report_csv.php");
		}
		else
		{
			require_once("report_pdf.php");
			do_report($columns,$out,$title,'L');
		}
	}

	function warehouse($filteredBy,$orderedBy)
	{
		global $myhost,$myuser,$mypass,$mydb;

		$id_simulators=$_SESSION["simulator_id"];
		$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $mydb))
			or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		if(!strlen($filteredBy))
			$filteredBy=" WHERE ";
		else
			$filteredBy.=" AND ";
		$filteredBy.=" parts.id_simulators='$id_simulators'";
		$query="SELECT 
					parts.pn_supplier,
					parts.description AS part_description,
					places_sup.name AS supplier,
					parts.pn_manufacturer,
					places_man.name AS manufacturer,
					places_ware.name AS warehouse,
					count(items.id) AS qty,
					items.location,
					items.sn,
					subsystems.text AS subsystem
				FROM
					items 
					LEFT JOIN parts 
						ON items.id_parts=parts.id
					LEFT JOIN subsystems
						ON parts.id_subsystems=subsystems.id
					LEFT JOIN places AS places_sup 
						ON parts.id_suppliers=places_sup.id
					LEFT JOIN places AS places_man
						ON parts.id_manufacturers=places_man.id
					LEFT JOIN places AS places_ware
						ON items.id_places=places_ware.id
				$filteredBy 
				GROUP BY items.id_parts,items.sn,items.location 
				$orderedBy";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$out=array();
		while($row=mysqli_fetch_assoc($result))
		{
			$out[]=array
				(
					"pn_supplier"=>$row["pn_supplier"],
					"part_description"=>$row["part_description"],
					"supplier"=>$row["supplier"],
					"pn_manufacturer"=>$row["pn_manufacturer"],
					"manufacturer"=>$row["manufacturer"],
					"magazzino"=>$row["warehouse"],
					"qty"=>$row["qty"],
					"location"=>$row["location"],
					"sn"=>$row["sn"],
					"subsystem"=>$row["subsystem"]
				);
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		return $out;
	}


	function place($filteredBy,$orderedBy)
	{
		global $myhost,$myuser,$mypass,$mydb;

		$id_simulators=$_SESSION["simulator_id"];
		$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $mydb))
			or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		if(!strlen($filteredBy))
			$filteredBy=" WHERE ";
		else
			$filteredBy.=" AND ";
		$filteredBy.=" parts.id_simulators='$id_simulators'";
		$query="SELECT 
					movements.insert_date,
					movements.note,
					CONCAT(users.name,' ',users.surname) as user,
					parts.pn_supplier,
					parts.description AS part_description,
					places_sup.name AS supplier,
					items.sn AS sn,
					count(items.id) as qty,
					places_from.name AS placeFrom,
					places_to.name AS placeTo
					FROM
					movements
					LEFT JOIN movements_items
						ON movements.id=movements_items.id_movements
					LEFT JOIN users 
						ON movements.id_users=users.id
					LEFT JOIN items
						ON movements_items.id_items=items.id
					LEFT JOIN parts 
						ON items.id_parts=parts.id
					LEFT JOIN places AS places_from
						ON movements.id_places_from=places_from.id
					LEFT JOIN places AS places_to
						ON movements.id_places_to=places_to.id
					LEFT JOIN places AS places_sup
						ON parts.id_suppliers=places_sup.id

				$filteredBy 
				GROUP BY movements.id,parts.pn_supplier,items.sn
				$orderedBy";

		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$out=array();
		while($row=mysqli_fetch_assoc($result))
		{
			$out[]=array
				(
					"insert_date"=>$row["insert_date"],
					"user"=>$row["user"],
					"pn_supplier"=>$row["pn_supplier"],
					"part_description"=>$row["part_description"],
					"sn"=>$row["sn"],
					"qty"=>$row["qty"],
					"supplier"=>$row["supplier"],
					"from"=>$row["placeFrom"],
					"to"=>$row["placeTo"],
					"note"=>$row["note"]
				);
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		return $out;
	}



	function itemsToRepair($filteredBy,$orderedBy)
	{
		global $myhost,$myuser,$mypass,$mydb;

		$id_simulators=$_SESSION["simulator_id"];
		$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $mydb))
			or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		$query="SELECT  items.sn AS sn,
								items.location,
								parts.description AS part_description,
								simulators.name AS simulators_name,
								places.name AS magazzino,
								manufacturers.name AS manufacturer_name,
								owners.name AS owners_name,
								suppliers.name AS supplier_name,
								parts.pn_supplier AS pn_supplier,
								parts.pn_manufacturer AS pn_manufacturer,
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
									AND parts.id_simulators='$id_simulators' 
						$filteredBy
								GROUP BY magazzino, sn,
                                        owners_name,location,part_description,
                                        pn_manufacturer,pn_supplier 
                 HAVING to_repair='1' AND replaced_itemId=0  
                        $orderedBy";

		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$out=array();
		while($row=mysqli_fetch_assoc($result))
		{
			$out[]=array
				(
					"part_description"=>$row["part_description"],
					"pn_supplier"=>$row["pn_supplier"],
					"pn_manufacturer"=>$row["pn_manufacturer"],
					"sn"=>$row["sn"],
					"supplier"=>$row["supplier_name"],
					"manufacturer"=>$row["manufacturer_name"],
					"magazzino"=>$row["magazzino"],
					"location"=>$row["location"]
				);
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
		return $out;
	}


?>
