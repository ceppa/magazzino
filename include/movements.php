<?php
$upload_max_filesize=ini_get("upload_max_filesize");
$post_max_size=ini_get("post_max_size");

$km=substr($post_max_size,-1);
$post_max_size_bytes=substr($post_max_size,0,-1);
if($km=="M")
	$post_max_size_bytes*=(1024*1024);
elseif($km=="K")
	$post_max_size_bytes*=1024;


if($_SERVER["CONTENT_LENGTH"] > $post_max_size_bytes)
	die("post data too large");

/* connect to database */
require_once('mysql.php');
require_once('functions.php');

$crd=array("C"=>"comsumable","R"=>"repairable","D"=>"discardable");

/* per avere la roba del $_SESSION */
ini_set ('session.name', 'magazzino');
session_start();

if(isset($_POST["ajax_sn"]))	// get pn from sn ajax_sn è in realtà itemId
	$func="getPnFromSn";
elseif(isset($_POST["ajax_pn"])
		&& isset($_POST["ajax_id_places"])
		&& isset($_POST["ajax_id_places_to"])
		&& isset($_POST["ajax_index"]))
	$func="fillRightBlock";
else
	$func = $_REQUEST['func'];

if($func=='getPnFromSn')
	getPnFromSn();
elseif($func=='fillRightBlock')
	fillRightBlock();
elseif($func=='editMovementForm')
{
	$movementId=$_POST["movementId"];
	$id_simulators=$_SESSION["simulator_id"];

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$sql="SELECT movements.insert_date as insert_date,
				movements.note as movementNote,
				places_from.id as fromId,
				places_from.name as fromName,
				places_to.name as toName,
				GROUP_CONCAT(DISTINCT CONCAT(documents.id,'§',
						documents.filename,'§',documents.description) 
					ORDER BY documents.description SEPARATOR '§') AS documents 
			FROM movements 
			LEFT JOIN movements_documents 
				ON movements.id = movements_documents.id_movements 
			LEFT JOIN documents 
				ON movements_documents.id_documents = documents.id 
			LEFT JOIN places places_from 
				ON movements.id_places_from = places_from.id
			LEFT JOIN places places_to 
				ON movements.id_places_to = places_to.id
			WHERE movements.id = '$movementId' 
			GROUP BY movements.id";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
	$movement = mysqli_fetch_assoc($result);
	$movement_date=$movement["insert_date"];
	$movement_note=$movement["movementNote"];
	$movement_from_id=$movement["fromId"];
	$movement_from=$movement["fromName"];
	$movement_to=$movement["toName"];
	$exploded=explode("§",$movement["documents"]);
	$documents=array();
	for($i=0;$i<count($exploded);$i+=3)
	{
		if(strlen($exploded[$i])&&strlen($exploded[$i+1]))
		{
			$documents[$exploded[$i]]=array
						(
							"file"=>$exploded[$i+1],
							"desc"=>$exploded[$i+2]
						);
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$sql="SELECT parts.pn_manufacturer as pn_manufacturer, 
				parts.pn_supplier as pn_sup,
				parts.description as description,
				count( items.id ) AS qty,
				items.sn AS sn,
				owners.name as owner 
			FROM movements_items 
			LEFT JOIN items
				ON movements_items.id_items = items.id
			LEFT JOIN parts 
				ON items.id_parts = parts.id
			LEFT JOIN owners 
				ON items.id_owners = owners.id
			WHERE movements_items.id_movements ='$movementId' 
			GROUP BY parts.pn_supplier,parts.pn_manufacturer,items.sn,items.id_owners";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$km=substr($upload_max_filesize,-1);
	$n=substr($upload_max_filesize,0,-1);
	if($km=="M")
		$n*=(1024*1024);
	elseif($km=="K")
		$n*=1024;
?>

	<form id="movementForm"
			name="movementForm"
			action="include/movements.php"
			method="post"
			enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="<?=$n?>">
		<input 	type="hidden" name="func" value="edit"/>
		<input 	type="hidden" name="movementId" value="<?=$movementId?>"/>

		<fieldset id="movement_header_fieldset">
		<legend>Movement header</legend>
		<table>
			<tr>
				<td class="fieldLabel">
					<i>date</i>
				</td>
				<td>
					<?=$movement_date;?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					<i>From</i>
				</td>
				<td>
					<?=$movement_from;?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					<i>To</i>
				</td>
				<td>
					<?=$movement_to;?>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					<i>Note</i>
				</td>
				<td>
					<textarea	rows="3"
						id="movementDescriptionInput"
						name="movementDescriptionInput"
						class="longTextInput"><?=trim($movement_note)?></textarea>
				</td>
			</tr>
		</table>
		</fieldset>
		<fieldset id="movement_details_fieldset">
		<legend>Movement details</legend>
		<table class="movement_details">
			<tr class="header">
				<td>
					<b>Supplier PN</b>
				</td>
				<td>
					<b>Manufacturer PN</b>
				</td>
				<td>
					<b>Description</b>
				</td>
				<td>
					<b>Serial Number</b>
				</td>
				<td>
					<b>Owner</b>
				</td>
				<td>
					<b>Quantity</b>
				</td>
			</tr>
		<?
		while($row=mysqli_fetch_assoc($result))
		{
		?>
			<tr>
				<td>
					<?=(strlen(trim($row["pn_sup"]))?trim($row["pn_sup"]):"----")?>
				</td>
				<td>
					<?=(strlen(trim($row["pn_manufacturer"]))?trim($row["pn_manufacturer"]):"----")?>
				</td>
				<td>
					<?=(strlen(trim($row["description"]))?trim($row["description"]):"----")?>
				</td>
				<td>
					<?=(strlen(trim($row["sn"]))?trim($row["sn"]):"----")?>
				</td>
				<td>
					<?=(strlen(trim($row["owner"]))?trim($row["owner"]):"----")?>
				</td>
				<td>
					<?=$row["qty"]?>
				</td>
			</tr>
		<?
		}
		?>
		</table>
		</fieldset>
		<?
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
?>
		<fieldset>
		<legend>Document(s) from files (max <?=$upload_max_filesize?> each, <?=$post_max_size?> total)</legend>
		<table id="documents">
			<tr>
				<td>&nbsp;</td>
				<td class="fieldTitle">file</td>
				<td class="fieldTitle">description</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		</fieldset>
		<fieldset>
		<legend>Document(s) from database</legend>
		<table id="documents_ex">
			<tr>
				<td class="fieldTitle">file</td>
				<td class="fieldTitle">description</td>
			</tr>
		</table>
		</fieldset>
	</form>
	<script language="JavaScript" type="text/javascript">
		addDocumentExRow();
		var li=new Object;
<?
	$i=0;
	foreach($documents as $id=>$array)
	{
		$desc=$array["desc"];
		$filename=$array["file"];
?>
		li.rowNumber=<?=$i?>;
		li.text="<?=$filename?>";
		li.extra=new Array("<?=$desc?>","<?=$id?>");

		addDocumentExRow(li);
<?
		$i++;
	}
?>
	</script>
<?
}
elseif($func=='newMovementForm')
{
	if($_POST["itemId"]) //move item chosen from items list
	{
		$result=runSQL("SELECT items.id_parts,
				parts.pn_manufacturer,parts.id_simulators,
				parts.pn_supplier,parts.description,
				items.sn,id_places,items.location,items.id_owners
			FROM items LEFT JOIN parts ON items.id_parts=parts.id
			WHERE items.id=".$_POST["itemId"]);
		$row=mysqli_fetch_assoc($result);
		$id_places=$row["id_places"];
		$id_parts=$row["id_parts"];
		$id_simulators=$row["id_simulators"];
		$pn=$row["pn_supplier"];
		$description=str_replace("\n"," ",$row["description"]);
		$description=str_replace('"',"'",$description);
		$sn=$row["sn"];
		$location=$row["location"];
		$owner=$row["id_owners"];
	}
	else 
		$id_simulators=$_SESSION["simulator_id"];

	$result=runSQL("SELECT * FROM places 
						WHERE id!=0
						 AND id_simulators='$id_simulators'
						 AND active=1 
						 ORDER BY name") ;
	?>
	<form	id="movementForm"
			name="movementForm"
			action="include/movements.php"
			method="post"
			enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
		<input 	type="hidden"
				id="func"
				name="func"
				value="add"/>
		<input 	type="hidden"
				name="simulator_id"
				id="simulator_id"
				value="<?=$_SESSION["simulator_id"]?>"/>
		<fieldset>
		<legend>New Movement</legend>
			Movement date
			<input 	type="text"
				name="insert_date"
				id="insert_date"
				value="<?=date('d/m/Y')?>"/>
			<fieldset>
			<legend>From/To</legend>
			<table>
				<tr>
					<td class="fieldLabel">
						From
					</td>
					<td>
						<select	id="fromPlaceSelect"
								name="fromPlaceSelect"
								class="longTextInput"
								onchange="removeElements(0);
									avoidFromToCollision(this);
									toPlaceSelect.focus();">
							<option value="-1"<?=$selected?>>
								...select source...
							</option>

					<?
						while($row=mysqli_fetch_assoc($result))
						{
							$selected=($id_places==$row["id"]?" selected='selected'":"");
						?>
							<option value="<?=$row["id"]."_".$row["id_places_types"]."_".$row["id_tspm_systems"]?>"<?=$selected?>>
								<?=$row["name"]?>
							</option>
						<?
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel">
						To
					</td>
					<td>
						<select	id="toPlaceSelect"
								name="toPlaceSelect"
								class="longTextInput"
								onchange="updateFotografiaCombo();
									avoidFromToCollision(this)">

							<option value="-1"<?=$selected?>>
								...select destination...
							</option>
					<?
						mysqli_data_seek($result, 0);
						while($row=mysqli_fetch_assoc($result))
						{
						?>
							<option value="<?=$row["id"]."_".$row["id_places_types"]."_".$row["id_tspm_systems"]?>"<?=$selected?>>
								<?=$row["name"]?>
							</option>
						<?
						}
						((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false)
						?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fieldLabel">
						Details
					</td>
					<td>
						<textarea	rows="3"
							id="movementDescriptionInput"
							name="movementDescriptionInput"
							class="longTextInput"></textarea>
					</td>
				</tr>
				
			</table>
			</fieldset>
			<fieldset>
			<legend>Document(s) from files (max <?=$upload_max_filesize?> each, <?=$post_max_size?> total)</legend>
			<table id="documents">
				<tr>
					<td>&nbsp;</td>
					<td class="fieldTitle">file</td>
					<td class="fieldTitle">description</td>
					<td>&nbsp;</td>
				</tr>
			</table>
			</fieldset>
			<fieldset>
			<legend>Document(s) from database</legend>
			<table id="documents_ex">
				<tr>
					<td class="fieldTitle">file</td>
					<td class="fieldTitle">description</td>
				</tr>
			</table>
			</fieldset>
			<fieldset>
			<legend>Items</legend>
			<table id="table_id">
			</table>
			</fieldset>
		</fieldset>
	</form>
	<script language="JavaScript" type="text/javascript">
		$("#insert_date").datepicker({ dateFormat: 'dd/mm/yy',firstDay:1 });
		addElement(<?=$_POST["itemId"]?>);
		<?
		if($_POST["itemId"])
		{?>
			fillPnAndSn(0,"<?=$id_parts?>","<?=$pn?>","<?=$description?>");
			addElement();
			$('#toPlaceSelect').focus();
		<?}
		else
		{?>
			$('#fromPlaceSelect').focus();
		<?}?>
	</script>
<?php
}
elseif($func=='list')
{
	/* table page number */
	$page = $_POST['page'];
	/* records per page */
	$rp = $_POST['rp'];
	/* sort field */
	$sortname = $_POST['sortname'];
	$sortorder = $_POST['sortorder'];

	if (!$sortname) 
		$sortname = 'movements.insert_date';
	if (!$sortorder) 
		$sortorder = 'asc';
	$sort = "ORDER BY $sortname $sortorder";

	$sort.=",movements.id";


	if(!$page || !$rp)
	{
		$limit = "";
		$page = 1;
	}else
	{
		$start = (($page-1) * $rp);
		$limit = " LIMIT $start,$rp";
	}

	$query = @ifnull($_REQUEST['query']);
	$qtype = @ifnull($_REQUEST['qtype']);

	$where = " WHERE movements.id_simulators='".$_SESSION["simulator_id"]."'";
	if($qtype) 
	{
		if($qtype == "date")
			$where .= " AND movements.insert_date LIKE '%$query%' ";
		elseif($qtype == "from")
			$where .= " AND places_from.name LIKE '%$query%' ";
		elseif($qtype == "to")
			$where .= " AND places_to.name LIKE '%$query%' ";
		elseif($qtype == "note")
			$where .= " AND note LIKE '%$query%' ";
		elseif($qtype == "user")
			$where .= " AND users.username LIKE '%$query%' ";
		elseif($qtype == "pn")
			$where .= " AND (parts.pn_supplier LIKE '%$query%' OR parts.pn_manufacturer LIKE '%$query%')";
		elseif($qtype == "description")
			$where .= " AND parts.description LIKE '%$query%' ";
		elseif($qtype == "sn")
			$where .= " AND items.sn LIKE '%$query%' ";
	}
	$logfile = fopen("log.log", "a+"); 

	$conn	=	($GLOBALS["___mysqli_ston"] = mysqli_connect(	$myhost, 
								$myuser, 
								$mypass));

	((bool)mysqli_query($conn , "USE " . $mydb));
	$sql="SELECT movements.id FROM movements
			LEFT JOIN movements_items ON movements.id = movements_items.id_movements 
			LEFT JOIN items ON movements_items.id_items = items.id 
			LEFT JOIN parts ON items.id_parts = parts.id 
			LEFT JOIN places places_from ON movements.id_places_from = places_from.id 
			LEFT JOIN places places_to ON movements.id_places_to = places_to.id 
			LEFT JOIN users ON movements.id_users = users.id 
			$where
			GROUP BY movements.id";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$total = mysqli_num_rows($result);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	
	$sql =	"SELECT movements.id AS id,
				GROUP_CONCAT(DISTINCT CONCAT(documents.id,'§',documents.description) 
					ORDER BY documents.description SEPARATOR '§') AS documents 
			FROM movements 
			LEFT JOIN movements_items ON movements.id = movements_items.id_movements 
			LEFT JOIN items ON movements_items.id_items = items.id 
			LEFT JOIN movements_documents ON movements.id = movements_documents.id_movements 
			LEFT JOIN documents ON movements_documents.id_documents = documents.id 
			LEFT JOIN parts ON items.id_parts = parts.id 
			LEFT JOIN places places_from ON movements.id_places_from = places_from.id 
			LEFT JOIN places places_to ON movements.id_places_to = places_to.id 
			LEFT JOIN users ON movements.id_users = users.id 
			$where GROUP BY movements.id
			$sort $limit";

	fwrite($logfile,$sql."\r\n\r\n");
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$documents=array();
	while($row=mysqli_fetch_assoc($result))
	{
		$id=$row["id"];
		$exploded=explode("§",$row["documents"]);
		$documents[$id]="";
		for($i=0;$i<count($exploded);$i+=2)
		{
			if(strlen($exploded[$i])&&strlen($exploded[$i+1]))
			{
				$documents[$id].="<a href='include/movements.php";
				$documents[$id].="?func=showDoc&amp;id=".$exploded[$i];
				$documents[$id].="' target='_blank'>";
				$documents[$id].=$exploded[$i+1];
				$documents[$id].="</a><br>";
			}
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	$in="";
	foreach($documents as $id=>$foo)
		$in.="$id,";
	$in=rtrim($in,",");
	if(!strlen($in))
		$in="''";
	$where=" WHERE movements.id in ($in)";

	$sql =	"SELECT movements.id AS id,
				parts.pn_supplier AS pn,
				parts.description AS description,
				items.sn AS sn,
				count(items.id) AS qty
			FROM movements 
			LEFT JOIN movements_items ON movements.id = movements_items.id_movements 
			LEFT JOIN items ON movements_items.id_items = items.id 
			LEFT JOIN parts ON items.id_parts = parts.id 
			$where
			GROUP BY movements.id,parts.pn_supplier,items.sn";
	fwrite($logfile,$sql."\r\n\r\n");
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$pnsn=array();
	$id="";
	$conta=0;
	while($row=mysqli_fetch_assoc($result))
	{
		if($id!=$row["id"])
		{
			$conta=0;
			$id=$row["id"];
			$pnsn[$id]=array("pn"=>"","desc"=>"","sn"=>"","qty"=>"");
		}
		else
			$conta++;
		if($conta<5)
		{
			$pnsn[$id]["pn"].=$row["pn"]."<br>";
			$pnsn[$id]["desc"].=trim(nlTobr($row["description"]))."<br>";
			$pnsn[$id]["sn"].=$row["sn"]."<br>";
			$pnsn[$id]["qty"].=$row["qty"]."<br>";
		}
		elseif($conta<6)
		{
			$pnsn[$id]["pn"].="...";
			$pnsn[$id]["desc"].="...";
			$pnsn[$id]["sn"].="...";
			$pnsn[$id]["qty"].="...";
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


	$sql="SELECT movements.id AS movements_id,
				movements.insert_date AS insert_date,
				places_from.name AS fromText,
				places_to.name AS toText,
				movements.note AS note,
				users.username AS username
			FROM movements 
			LEFT JOIN places places_from ON movements.id_places_from = places_from.id 
			LEFT JOIN places places_to ON movements.id_places_to = places_to.id 
			LEFT JOIN users ON movements.id_users = users.id
			$where $sort";

	fwrite($logfile,$sql."\r\n\r\n");

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
	while($row=mysqli_fetch_assoc($result))
	{
		$id=$row['movements_id'];
		$note=trim(nlTobr($row['note']));
		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "id:'".$id."',";
		$json .= "cell:['".addslashes(($row['insert_date']=="")?"---":$row['insert_date'])."'";
		$json .= ",'".addslashes(($row['fromText']=="")?"---":$row['fromText'])."'";
		$json .= ",'".addslashes(($row['toText']=="")?"---":$row['toText'])."'";
		$json .= ",'".addslashes($note==""?"---":$note)."'";
		$json .= ",'".addslashes($pnsn[$id]['pn'])."'";
		$json .= ",'".addslashes($pnsn[$id]['desc'])."'";
		$json .= ",'".addslashes($pnsn[$id]['sn'])."'";
		$json .= ",'".addslashes($pnsn[$id]['qty'])."'";
		$json .= ",'".addslashes(($row['username']=="")?"---":$row['username'])."'";
		$json .= ",'".addslashes(($documents[$id]=="")?"---":$documents[$id])."'";
		$json .= "]}";
		$rc = true;
	}
	$json .= "]\n";
	$json .= "}";
	echo $json;
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}
elseif($func=="edit")
{
	$description=addslashes($_POST["movementDescriptionInput"]);
	$id_movement=$_POST["movementId"];
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($conn , "USE " . $mydb));
	$uploads=array();
	$docs=array();

	$errorString="";
	foreach($_POST as $k=>$v)
	{
		if(substr($k,0,9)=="doc_desc_")
		{
			if(strlen(trim($v)))
			{
				$n=substr($k,9);
				$desc=str_replace("§","_",$v);
				$filename=$_FILES["doc_$n"]["name"];
				$tmp_name=$_FILES["doc_$n"]["tmp_name"];
				$type=$_FILES["doc_$n"]["type"];
				$size=$_FILES["doc_$n"]["size"];

				if((int)$_FILES["doc_$n"]["error"]==0)
				{
					$uploads[]=array
						(
							"desc"=>$desc,
							"filename"=>$filename,
							"tmp_name"=>$tmp_name,
							"type"=>$type,
							"size"=>$size
						);
				}
				else
					$errorString.="file $filename is too large to be uploaded\n";
			}
		}
		elseif((substr($k,0,9)=="doc_ex_id")&&(strlen($v)))
			$docs[]=$v;
	}
	mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION")
		or die("Start Transaction<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$query="UPDATE movements SET 
				note='$description'
			WHERE id='$id_movement'";
	queryWithRollback($query);
	$query="DELETE FROM movements_documents
			WHERE id_movements='$id_movement'";
	queryWithRollback($query);
	foreach($uploads as $k=>$v)
	{
		$checksum=hash_file('crc32', $v["tmp_name"]);
		$fp=fopen($v["tmp_name"],'r');
		$content = fread($fp, filesize($v["tmp_name"]));
		$content = addslashes($content);
		fclose($fp);
		$filename=addslashes($v["filename"]);
		$desc=addslashes($v["desc"]);
		$size=$v["size"];
		$type=$v["type"];

		$query="SELECT id FROM documents 
				WHERE checksum='$checksum' AND id_simulators='".$_SESSION["simulator_id"]."'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$row=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		if($row)
			$id_documents=$row["id"];
		else
		{
			$query = "INSERT INTO documents
					(
						description,
						filename, 
						size, 
						type, 
						content,
						checksum,
						id_simulators
					)
					VALUES 
					(
						'$desc',
						'$filename', 
						'$size', 
						'$type', 
						'$content',
						'$checksum',
						'".$_SESSION["simulator_id"]."'
					)";
			$id_documents=queryWithRollback($query);
		}
		$docs[]=$id_documents;
	}

	$docs=array_unique($docs);
	foreach($docs as $id_documents)
	{
		$query = "INSERT INTO movements_documents
					(
						id_movements,
						id_documents
					)
					VALUES
					(
						'$id_movement',
						'$id_documents'
					)";
		queryWithRollback($query);
	}
	mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT")
		or die("Commit<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if(!strlen($errorString))
		echo "0Movement updated";
	else
		echo "$errorString\nMovement Updated";
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}
elseif($func=="add")
{
	$logbook_entries=array();
	list($from,$from_place_type,$id_tspm_systems_from)=explode("_",$_POST["fromPlaceSelect"]);
	list($to,$to_place_type,$id_tspm_systems_to)=explode("_",$_POST["toPlaceSelect"]);

	$insert_date=date_to_sql($_POST["insert_date"]);
	if($insert_date==date("Y-m-d"))
		$insert_date=date("Y-m-d H:i:s");
	else
		$insert_date.=" 12:00:00";

	$description=addslashes($_POST["movementDescriptionInput"]);
	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($conn, "USE " . $mydb));

	if($id_tspm_systems_from+$id_tspm_systems_to)
	{
		$id_place=($id_tspm_systems_from>0?$from:$to);
		$query="SELECT name FROM places where id=$id_place";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$row=mysqli_fetch_assoc($result);
		$simulator=$row["name"];
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	}

	$new_items=array();
	$movements=array();
	$uploads=array();
	$docs=array();
	$errorString="";

	foreach($_POST as $k=>$v)
	{
		if(substr($k,0,8)=="newPart_")
		{
			$n=substr($k,8);
			$id_suppliers=$from;
			$pnSupplier=trim($_POST["pnInput_$n"]);
			$id_manufacturers=$_POST["np_".$n."_manufacturerSelect"];
			if(!strlen($id_manufacturers))
				$id_manufacturers="NULL";
			$pnManufacturer=trim($_POST["np_".$n."_pnManInput"]);
			$id_subsystems=$_POST["np_".$n."_subsystemSelect"];
			$id_subsystems2=$_POST["np_".$n."_subsystem2Select"];
			$id_subsystems3=$_POST["np_".$n."_subsystem3Select"];
			$cageCode=trim($_POST["np_".$n."_cageCodeInput"]);
			$deperibility=$_POST["np_".$n."_crdSelect"];
			$shelfLife=$_POST["np_".$n."_shelfLifeInput"];
			$shelfLifeUnit=$_POST["np_".$n."_shelfLifeUnitInput"];
			$criticality=trim($_POST["np_".$n."_criticalityInput"]);
			$minimumQuantity=$_POST["np_".$n."_minimumQuantityInput"];
			$partDescription=trim($_POST["np_".$n."_partDescriptionInput"]);
			$sn=trim($_POST["np_".$n."_snInput"]);
			$qty=trim($_POST["np_".$n."_quantityInput"]);
			$location=trim($_POST["np_".$n."_locationInput"]);
			$owner=$_POST["np_".$n."_ownerSelect"];
			$query="INSERT INTO parts
					(
						id_suppliers,
						id_manufacturers,
						id_simulators,
						description,
						pn_manufacturer,
						pn_supplier,
						cage_code,
						shelf_life,
						criticality,
						minimum_quantity,
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
						$id_manufacturers,
						'".$_SESSION["simulator_id"]."',
						'$partDescription',
						'$pnManufacturer',
						'$pnSupplier',
						'$cageCode',
						'$shelfLife',
						'$criticality',
						'$minimumQuantity',
						'".$_SESSION["user_id"]."',
						'".$_SESSION["user_id"]."',
						'$id_subsystems',
						'$id_subsystems2',
						'$id_subsystems3',
						'$deperibility'
					)";
			mysqli_query($GLOBALS["___mysqli_ston"], $query)
				or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
			$id_parts=((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

			$new_items[]=array
			(
				"id_parts"=>$id_parts,
				"sn"=>$sn,
				"qty"=>$qty,
				"location"=>$location,
				"owner"=>$owner
			);
		}
		elseif(substr($k,0,8)=="newItem_")
		{
			$splitted=explode("_",$k);
			$n=$splitted[1];
			$m=$splitted[2];
			$id_parts=$_POST["id_parts_$n"];
			$sn=trim($_POST["ni_".$n."_snInput_".$m]);
			$qty=trim($_POST["ni_".$n."_quantityInput_".$m]);
			$location=trim($_POST["ni_".$n."_locationInput_".$m]);
			$owner=$_POST["ni_".$n."_ownerSelect_".$m];
			$new_items[]=array
			(
				"id_parts"=>$id_parts,
				"sn"=>$sn,
				"qty"=>$qty,
				"location"=>$location,
				"owner"=>$owner
			);
		}
		elseif(substr($k,0,3)=="sn_")
		{
			$exploded=explode("_",$k);
			$n=$exploded[1];
			$sn_string=substr($k,4+strlen($n));
			$repair_field=sprintf("repair_%s",substr($k,3));
			$to_repair=(isset($_POST[$repair_field])?1:0);

			list($sn,$owner,$location)=explode("§",$sn_string);

			$pn=$_POST["pnInput_$n"];
			$id_parts=$_POST["id_parts_$n"];
			$qty=$_POST["qty_".$n."§".$owner."§".$location];
			$location_to=trim($_POST["location_".$n."_".$sn_string]);
			$id_fotografia=trim($_POST["fotografia_".$n."_".$sn_string]);
			if((!strlen(trim($sn)))||(!strlen($id_fotografia)))
				$id_fotografia=0;
			if((!strlen($qty))||strlen(trim($sn)))
				$qty=1;
				
			$movements[]=array
			(
				"id_parts"=>$id_parts,
				"pn"=>$pn,
				"sn"=>$sn,
				"qty"=>$qty,
				"owner"=>$owner,
				"location"=>$location,
				"location_to"=>$location_to,
				"id_fotografia"=>$id_fotografia,
				"to_repair"=>$to_repair
			);
		}
		elseif(substr($k,0,9)=="doc_desc_")
		{
			if(strlen(trim($v)))
			{
				$n=substr($k,9);
				$desc=str_replace("§","_",$v);
				$filename=$_FILES["doc_$n"]["name"];
				$tmp_name=$_FILES["doc_$n"]["tmp_name"];
				$type=$_FILES["doc_$n"]["type"];
				$size=$_FILES["doc_$n"]["size"];
				if((int)$_FILES["doc_$n"]["error"]==0)
				{
					$uploads[]=array
						(
							"desc"=>$desc,
							"filename"=>$filename,
							"tmp_name"=>$tmp_name,
							"type"=>$type,
							"size"=>$size
						);
				}
				else
					$errorString.="file $filename is too large to be uploaded\n";

			}
		}
		elseif((substr($k,0,9)=="doc_ex_id")&&(strlen($v)))
			$docs[]=$v;
	}
	if(!(count($movements)+count($new_items)))
		die("No movement to store");
	mysqli_query($GLOBALS["___mysqli_ston"], "START TRANSACTION")
		or die("Start Transaction<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
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
				'$insert_date',
				'$from',
				'$to',
				0,
				'$description',
				'".$_SESSION["user_id"]."',
				'".$_SESSION["simulator_id"]."'
			)";
	$id_movement=queryWithRollback($query);

	foreach($new_items as $item)
	{
		$id_parts=$item["id_parts"];
		$sn=$item["sn"];
		$qty=$item["qty"];
		$location=$item["location"];
		$owner=$item["owner"];

		if(strlen($sn)&&($id_tspm_systems_from+$id_tspm_systems_to)&&($_SESSION["tspm_user_id"]>0))
			$logbook_entries[]=addLogbook($id_parts,$sn,$id_tspm_systems_from,
				$id_tspm_systems_to,$insert_date,$location,$simulator,$description);

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
						'$to',
						'$owner',
						'$sn',
						'$location',
						'".$_SESSION["user_id"]."',
						'".$_SESSION["user_id"]."'
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
						'$to',
						'$owner',
						null,
						'$location',
						'".$_SESSION["user_id"]."',
						'".$_SESSION["user_id"]."'
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
	foreach($movements as $movement)
	{
		$id_parts=$movement["id_parts"];
		$pn=$movement["pn"];
		$sn=$movement["sn"];
		$qty=$movement["qty"];
		$location=str_replace(" ","_",$movement["location"]);
		$location_to=$movement["location_to"];
		$id_fotografia=$movement["id_fotografia"];
		$to_repair=$movement["to_repair"];
		$owner=$movement["owner"];
		if(strlen($sn)==0)
			$snCondition="AND sn IS NULL ";
		else
		{
			$snCondition="AND REPLACE(REPLACE(sn,' ','_'),'.','_')='$sn' ";

			if(($id_tspm_systems_from+$id_tspm_systems_to)&&($_SESSION["tspm_user_id"]>0))
			{
				$loc=($id_tspm_systems_from>0?$location:$location_to);
				$logbook_entries[]=addLogbook($id_parts,$sn,$id_tspm_systems_from,
					$id_tspm_systems_to,$insert_date,$loc,$simulator,$description);
			}
		}
		$locationCondition="AND REPLACE(REPLACE(location,' ','_'),'.','_')='$location' ";
		$query="
			INSERT INTO movements_items( id_movements, id_items, id_fotografia, to_repair )
				SELECT $id_movement , items.id, $id_fotografia, $to_repair
				FROM items
					INNER JOIN (
						SELECT id
						FROM `items`
						WHERE id_parts='$id_parts'
						AND id_places='$from'
						$snCondition
						AND id_owners='$owner'
						$locationCondition
						ORDER BY id
						LIMIT $qty
					) AS items2 
					ON items.id = items2.id";
		queryWithRollback($query);
		if(mysqli_affected_rows($GLOBALS["___mysqli_ston"])!=$qty)
			echo "$query<br>";
		$query="
			UPDATE items 
				INNER JOIN(
					SELECT id 
					FROM `items` 
					WHERE id_parts='$id_parts' 
					AND id_places='$from' 
					$snCondition
					AND id_owners='$owner'
					$locationCondition
					ORDER BY id
					LIMIT $qty
				) AS items2 
				ON items.id=items2.id
			SET items.id_places='$to',
				items.location='$location_to'";
		queryWithRollback($query);
		if(mysqli_affected_rows($GLOBALS["___mysqli_ston"])!=$qty)
			echo "$query<br>";
	}

	foreach($uploads as $k=>$v)
	{
		$checksum=hash_file('crc32', $v["tmp_name"]);
		$fp=fopen($v["tmp_name"],'r');
		$content = fread($fp, filesize($v["tmp_name"]));
		$content = addslashes($content);
		fclose($fp);
		$filename=addslashes($v["filename"]);
		$desc=addslashes($v["desc"]);
		$size=$v["size"];
		$type=$v["type"];

		$query="SELECT id FROM documents 
				WHERE checksum='$checksum' AND id_simulators='".$_SESSION["simulator_id"]."'";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$row=mysqli_fetch_assoc($result);
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		if($row)
			$id_documents=$row["id"];
		else
		{
			$query = "INSERT INTO documents
					(
						description,
						filename, 
						size, 
						type, 
						content,
						checksum,
						id_simulators
					)
					VALUES 
					(
						'$desc',
						'$filename', 
						'$size', 
						'$type', 
						'$content',
						'$checksum',
						'".$_SESSION["simulator_id"]."'
					)";
			$id_documents=queryWithRollback($query);
		}
		$docs[]=$id_documents;
	}

	$docs=array_unique($docs);
	foreach($docs as $id_documents)
	{
		$query = "INSERT INTO movements_documents
					(
						id_movements,
						id_documents
					)
					VALUES
					(
						'$id_movement',
						'$id_documents'
					)";
		queryWithRollback($query);
	}

	$checkFrom=checkWarehouse($from);
	$checkTo=checkWarehouse($to);
	if((!$checkFrom)||(!$checkTo))
	{
		mysqli_query($GLOBALS["___mysqli_ston"], "ROLLBACK");
		echo "Movement not completed";
		if(!$checkFrom)
			echo "<br>Source not consistent";
		if(!$checkTo)
			echo "<br>Destination not consistent";
	}
	else
	{
		mysqli_query($GLOBALS["___mysqli_ston"], "COMMIT")
			or die("Commit<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		if(!strlen($errorString))
		{
			// inserimento in logbook
			echo "0Movement completed";

			if(count($logbook_entries))
			{
				$logbook_conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
					or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				((bool)mysqli_query($logbook_conn, "USE " . $mydb_tspm))
					or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				foreach($logbook_entries as $entry)
				{
					$data=$entry["data"];
					$simulatore=$entry["simulatore"];
					$sistema=1<<$entry["sistema"];
					$descrizione=substr($entry["descrizione"],0,255);
					$user_id=$_SESSION["tspm_user_id"];
					$query="INSERT INTO logbook
						(
							system_id,
							subsystem_id,
							logtype_id,
							date,
							user_id,
							description
						)
						VALUES
						(
							'$simulatore',
							'$sistema',
							2,
							'$data',
							'$user_id',
							'$descrizione'
						)";
					mysqli_query($logbook_conn, $query)
						or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
				}
				((is_null($___mysqli_res = mysqli_close($logbook_conn))) ? false : $___mysqli_res);
			}
		}
		else
			echo "$errorString\nMovement not completed";
	}
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}
elseif($func=='getMovementDetails') 
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));


//	checkWarehouse(1);

	$sql  = "SELECT movements.insert_date as insert_date,
				movements.note as movementNote,
				places_from.name as fromName,
				places_to.name as toName,
				GROUP_CONCAT(DISTINCT CONCAT(documents.id,'§',
						documents.filename,'§',documents.description) 
					ORDER BY documents.description SEPARATOR '§') AS documents 
			FROM movements
			LEFT JOIN movements_documents 
				ON movements.id = movements_documents.id_movements 
			LEFT JOIN documents 
				ON movements_documents.id_documents = documents.id 
			LEFT JOIN places places_from 
				ON movements.id_places_from = places_from.id
			LEFT JOIN places places_to 
				ON movements.id_places_to = places_to.id
			WHERE movements.id = '".$_REQUEST['movementId']."'
			GROUP BY movements.id";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
	$movement = mysqli_fetch_assoc($result);
	$movement_date=$movement["insert_date"];
	$movement_note=$movement["movementNote"];
	$movement_from=$movement["fromName"];
	$movement_to=$movement["toName"];
	$exploded=explode("§",$movement["documents"]);
	$documents=array();
	for($i=0;$i<count($exploded);$i+=3)
	{
		if(strlen($exploded[$i])&&strlen($exploded[$i+1]))
		{
			$documents[$exploded[$i]]=array
						(
							"file"=>$exploded[$i+1],
							"desc"=>$exploded[$i+2]
						);
		}
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	$sql  = "SELECT parts.pn_manufacturer as pn_manufacturer, parts.pn_supplier as pn_sup";
	$sql .= ", count( items.id ) AS qty, items.sn AS sn, owners.name as owner";
	$sql .= " FROM movements_items LEFT JOIN items";
	$sql .= " 	ON movements_items.id_items = items.id";
	$sql .= " LEFT JOIN parts ON items.id_parts = parts.id";
	$sql .= " LEFT JOIN owners ON items.id_owners = owners.id";
	$sql .= " WHERE movements_items.id_movements =".$_REQUEST['movementId'];
	$sql .= " GROUP BY parts.pn_supplier,parts.pn_supplier,items.sn,items.id_owners";
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."<br>$sql");
	
	?>
	<fieldset id="movement_header_fieldset">
	<legend>Movement header</legend>
	<table>
		<tr>
			<td class="fieldLabel">
				<i>date</i>
			</td>
			<td>
				<?=$movement_date;?>
			</td>
		</tr>
		<tr>
			<td class="fieldLabel">
				<i>From</i>
			</td>
			<td>
				<?=$movement_from;?>
			</td>
		</tr>
		<tr>
			<td class="fieldLabel">
				<i>To</i>
			</td>
			<td>
				<?=$movement_to;?>
			</td>
		</tr>
		<tr>
			<td class="fieldLabel">
				<i>Note</i>
			</td>
			<td>
					<?=$movement_note;?>
			</td>
		</tr>
		<tr>
			<td class="fieldLabel">
				<i>Attachments</i>
			</td>
			<td>
				<table>
<?
	foreach($documents as $id=>$details)
	{
		$link="include/movements.php?func=showDoc&amp;id=$id";
?>
					<tr>
						<td style="padding-right:10px">
							<?=$details["desc"]?>
						</td>						
						<td>
							<a href="<?=$link?>">
								<?=$details["file"]?>
							</a>
						</td>
					</tr>
<?	}?>
				</table>
			</td>
		</tr>
	</table>
	</fieldset>

	<fieldset id="movement_details_fieldset">
	<legend>Movement details</legend>
	<table class="movement_details">
		<tr class="header">
			<td>
				<b>Supplier PN</b>
			</td>
			<td>
				<b>Manufacturer PN</b>
			</td>
			<td>
				<b>Serial Number</b>
			</td>
			<td>
				<b>Owner</b>
			</td>
			<td>
				<b>Quantity</b>
			</td>
		</tr>
	<?
	while($row=mysqli_fetch_assoc($result))
	{
	?>
		<tr>
			<td>
				<?=(strlen(trim($row["pn_sup"]))?trim($row["pn_sup"]):"----")?>
			</td>
			<td>
				<?=(strlen(trim($row["pn_manufacturer"]))?trim($row["pn_manufacturer"]):"----")?>
			</td>
			<td>
				<?=(strlen(trim($row["sn"]))?trim($row["sn"]):"----")?>
			</td>
			<td>
				<?=(strlen(trim($row["owner"]))?trim($row["owner"]):"----")?>
			</td>
			<td>
				<?=$row["qty"]?>
			</td>
		</tr>
	<?
	}
	?>
	</table>
	<?
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
}
elseif($func=="addPart")
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$suppliers=array();
	$manufacturers=array();
	$owners=array();

	$query = "SELECT * FROM owners 
				WHERE id_simulators='".$_SESSION["simulator_id"]."'
				ORDER BY name";

	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$owners[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	/*prepare suppliers query*/
	$query = "SELECT * FROM places
				WHERE id_simulators='".$_SESSION["simulator_id"]."' 
					AND (id_places_types=5 OR id_places_types=3 OR id_places_types=4)
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

	/*execute subsystems query*/
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$subsystems=array();
	while($row = mysqli_fetch_assoc($result))
		$subsystems[$row["id"]]=$row["text"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);


	$exploded=explode("_",$_POST["ajax_id_places_from"]);
	$id_suppliers=$exploded[0];
	$index=$_POST["ajax_index"];
	?>
	<input type="hidden" name="newPart_<?=$index?>">
	<fieldset>
		<legend>New Part</legend>
		<table>
			<tr>
				<td class="fieldLabel">
					Supplier
				</td>
				<td>
					<?=$suppliers[$id_suppliers]?>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Supplier P/N
				</td>
					<?=$_POST["ajax_pn_supplier"]?>
				<td>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Manufacturer
				</td>
				<td>
					<select	id="np_<?=$index?>_manufacturerSelect"
							name="np_<?=$index?>_manufacturerSelect"
							class="longTextInput">
						<!-- default option -->
						<option value="">Select manufacturer...</option>
					<?php
					foreach($manufacturers as $id=>$name)
					{
						?>
						<option value="<?=$id?>">
							<?=$name?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td>
				</td>
			</tr>			<tr>
				<td class="fieldLabel">
					Manufacturer P/N
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_pnManInput"
							name="np_<?=$index?>_pnManInput"
							class="longTextInput"/>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Subsystem
				</td>
				<td>
					<select	id="np_<?=$index?>_subsystemSelect" 
							name="np_<?=$index?>_subsystemSelect" 
							class="longTextInput" 
							onchange="canAddElement_newPart(<?=$index?>)">
						<!-- default option -->
						<option value="">Select subsystem...</option>
					<?php
					foreach($subsystems as $id=>$text)
					{
						?>
						<option value="<?=$id?>">
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="np_<?=$index?>_subsystemCheck" class="subsystemCheck dark_red">
					must be selected
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Alt subsystem
				</td>
				<td>
					<select	id="np_<?=$index?>_subsystem2Select" 
							name="np_<?=$index?>_subsystem2Select" 
							class="longTextInput">
						<!-- default option -->
						<option value="">Select subsystem...</option>
					<?php
					foreach($subsystems as $id=>$text)
					{
						?>
						<option value="<?=$id?>">
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Alt subsystem
				</td>
				<td>
					<select	id="np_<?=$index?>_subsystem3Select" 
							name="np_<?=$index?>_subsystem3Select" 
							class="longTextInput">
						<!-- default option -->
						<option value="">Select subsystem...</option>
					<?php
					foreach($subsystems as $id=>$text)
					{
						?>
						<option value="<?=$id?>">
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Cage code
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_cageCodeInput"
							name="np_<?=$index?>_cageCodeInput"
							class="longTextInput"/>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Deperibility
				</td>
				<td>
					<select	id="np_<?=$index?>_crdSelect" 
							name="np_<?=$index?>_crdSelect"
							class="longTextInput">
					<?
					foreach($crd as $id=>$text)
					{
						?>
						<option value="<?=$id?>"
							<?=($id=='R'?" selected":"");?>>
							<?=$text?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Shelf life
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_shelfLifeInput" 
							name="np_<?=$index?>_shelfLifeInput"
							class="threeDigitInput"/>
					<select id="np_<?=$index?>_shelfLifeUnitSelect"
							name="np_<?=$index?>_shelfLifeUnitSelect">
						<option value="1">days</option>
						<option value="7">weeks</option>
						<option value="30">months</option>
						<option value="365">years</option>
					</select>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Criticality
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_criticalityInput" 
							name="np_<?=$index?>_criticalityInput"
							class="threeDigitInput"/>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Minimum quantity
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_minimumQuantityInput" 
							name="np_<?=$index?>_minimumQuantityInput"
							class="threeDigitInput"/>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Description
				</td>
				<td>
					<textarea	rows="3"
								id="np_<?=$index?>_partDescriptionInput" 
								name="np_<?=$index?>_partDescriptionInput"
								class="longTextInput"></textarea>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset>
		<legend>Items properties</legend>
		<table>
			<tr>
				<td class="fieldLabel">
					S/N
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_snInput"
							name="np_<?=$index?>_snInput"
							class="longTextInput"
							onkeyup="
							if(jQuery.trim(this.value)!='') 
							{
								$('#np_<?=$index?>_quantityInput').val('1');
								$('#np_<?=$index?>_quantityInput').attr('readonly','readonly');
								$('#np_<?=$index?>_quantityCheck').text('Quantity forced to 1 when item is serialized.');
							} 
							else 
							{
								$('#np_<?=$index?>_quantityInput').attr('readonly','');
								$('#np_<?=$index?>_quantityCheck').text('');
							}
							"/>
				</td>
				<td	id="snCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Quantity
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_quantityInput"
							name="np_<?=$index?>_quantityInput"
							class="threeDigitInput"
							value='1'
							onkeyup="
								this.value=this.value.replace(/[^0-9]/g,'');
								if(jQuery.trim($('#np_<?=$index?>_quantityInput').val())!='1') 
								{
									$('#np_<?=$index?>_snInput').val('');
								}"
							onchange="
								if(this.value==0)
									if(this.value=1);"
							/>
				</td>
				<td	id="np_<?=$index?>_quantityCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Location / note
				</td>
				<td>
					<input	type="text"
							id="np_<?=$index?>_locationInput" 
							name="np_<?=$index?>_locationInput"
							/>
				</td>
				<td	id="np_<?=$index?>_locationCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Owner
				</td>
				<td>
					<select	id="np_<?=$index?>_ownerSelect" 
						name="np_<?=$index?>_ownerSelect"
						class="longTextInput">
					<!-- default option -->
					<option value="0">Select owner...</option>
					<?
					foreach($owners as $id=>$owner)
					{
						?>
							<option value="<?=$id?>">
								<?=$owner?>
							</option>
						<?
					}?>
					</select>
				</td>
				<td	id="ownerCheck" class="fieldCheck">
				</td>
			</tr>
		</table>
	</fieldset>
<?
}
elseif(($func=="showDoc")&&(isset($_GET["id"])))
{
	$id=$_GET["id"];

	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$query="SELECT filename, type, size, content FROM documents WHERE id=$id";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if(mysqli_num_rows($result)==1)
	{
		list($name, $type, $size, $content)=mysqli_fetch_array($result);
		header("Content-type: $type");
		header("Content-Disposition: attachment; filename=\"$name\"");
		echo $content;
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
}
elseif($func=='updateFotografiaCombo') 
{
	buildFotografiaCombo(
		$_POST["id_places_to"],
		$_POST["ajax_pn"],
		$_POST["sn"],
		$_POST["index"],
		$_POST["id_owners"],
		$_POST["location"]);
}
elseif($func="newItemTable")
{
	$index=$_POST["index"];
	$n=$_POST["n"];
	echo new_item_table($index,$n);
}

function checkWarehouse($places_id)
{
	$arrayMovements=array();
	$queryMovements="
		SELECT items.id_parts, items.sn, 
			sum( if( (movements.id_places_from =$places_id) 
					and (movements_items.new_from_supplier=0 ), -1, 0 ) 
				+ if( movements.id_places_to =$places_id, 1, 0 ) ) AS n
		FROM movements
		LEFT JOIN movements_items 
			ON movements.id = movements_items.id_movements
		LEFT JOIN items 
			ON movements_items.id_items = items.id
		WHERE movements.id_places_from =$places_id
			OR movements.id_places_to =$places_id
		GROUP BY items.id_parts
		HAVING n>0";
	$resultMovements=mysqli_query($GLOBALS["___mysqli_ston"], $queryMovements)
		or die("$queryMovements<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if(!$resultMovements)
		return false;

	while($row=mysqli_fetch_assoc($resultMovements))
		$arrayMovements[$row["id_parts"]]=$row["n"];
	((mysqli_free_result($resultMovements) || (is_object($resultMovements) && (get_class($resultMovements) == "mysqli_result"))) ? true : false);

	$queryItems="
		SELECT id_parts,count( id ) AS n
		FROM items
		WHERE id_places =$places_id
		GROUP BY id_parts";
	$resultItems=mysqli_query($GLOBALS["___mysqli_ston"], $queryItems)
		or die("$queryItems<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	if(!$resultItems)
		return false;

	while($row=mysqli_fetch_assoc($resultItems))
	{
		$key=$row["id_parts"];
		if((!isset($arrayMovements[$key]))
			||($arrayMovements[$key]!=$row["n"]))
		{
			echo 'places_id='.$places_id.'\n';
			echo 'key='.$key.'\n';
			echo '$arrayMovements[$key]='.$arrayMovements[$key].'\n';
			echo '$row["n"]='.$row["n"].'\n';

			((mysqli_free_result($resultItems) || (is_object($resultItems) && (get_class($resultItems) == "mysqli_result"))) ? true : false);
			return(false);
		}
		else
			unset($arrayMovements[$key]);
	}
	((mysqli_free_result($resultItems) || (is_object($resultItems) && (get_class($resultItems) == "mysqli_result"))) ? true : false);
	return(count($arrayMovements)==0);
}


function addLogbook($id_parts,$sn,$id_tspm_systems_from,$id_tspm_systems_to,
			$insert_date,$location,$simulator,$description)
{
//				query su id_parts per ottenere id_subsystems
	$query="SELECT subsystems.id_tspm_subsystems,
				subsystems.text as subsystem,
				parts.pn_supplier,
				parts.description
			FROM parts 
				LEFT JOIN subsystems
				ON parts.id_subsystems=subsystems.id
			WHERE parts.id='$id_parts'";
	$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$row=mysqli_fetch_assoc($result);
	$id_subsystems=$row["id_tspm_subsystems"];
	$subsystem=$row["subsystem"];
	$part_description=$row["description"];
	$pn_supplier=$row["pn_supplier"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	if($id_tspm_systems_from>0)
	{
		$id_sim=$id_tspm_systems_from;
		$azione="Rimosso";
		$inda="da";
	}
	else
	{
		$id_sim=$id_tspm_systems_to;
		$azione="Installato";
		$inda="in";
	}
	$descrizione=sprintf("%s %s (%s) S/N: %s %s Simulatore %s su "
		,$azione,$part_description,$pn_supplier,$sn,$inda,$simulator);
	if(strlen(trim($location)))
		$descrizione.="$location di ";
	$descrizione.=$subsystem;
	if(strlen($description))
		$descrizione.=" - $description";

	return(array
		(
			"data"=>$insert_date,
			"simulatore"=>$id_sim,
			"sistema"=>$id_subsystems,
			"descrizione"=>$descrizione
		));
}

function buildFotografiaCombo($id_places_to,$ajax_pn,$sn,$index,$id_owners,$location)
{
	$locations=array();
	$locations[0]="---";

	if(strlen($sn))
	{
		$query="SELECT id_places_types FROM places 
				WHERE id='$id_places_to'";
		$result=runSQL($query);
		if($row=mysqli_fetch_assoc($result))
		{
			((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
			if($row["id_places_types"]==1)	// è un device, potrebbe essere in fotografia
			{
				$query="SELECT
							fotografia.id,
							fotografia.ref_design,
							fotografia.location
						FROM
							fotografia JOIN compatible_parts 
								ON compatible_parts.id_compatible=fotografia.id_compatible 
							JOIN parts ON compatible_parts.id_parts=parts.id 
							WHERE (parts.pn_manufacturer='$ajax_pn' OR parts.pn_supplier='$ajax_pn') 
									AND fotografia.id_places='$id_places_to' 
						GROUP BY fotografia.id
						ORDER BY fotografia.id";

				$result=runSQL($query);
	
				while($row=mysqli_fetch_assoc($result))
				{
					$line=$row["ref_design"];
					if(strlen($line))
						$line.=" - ";
					$line.=$row["location"];
					$locations[$row["id"]]=$line;
				}
				((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

				$query="SELECT t.fotografia
						FROM
						(
							SELECT IFNULL(movements_items.id_fotografia,0) AS fotografia,
									movements_items.id_items 		
								FROM items
									JOIN parts
										ON items.id_parts = parts.id AND 
										(parts.pn_manufacturer='$ajax_pn' 
											OR parts.pn_supplier='$ajax_pn') 
									LEFT JOIN movements_items 
										ON items.id = movements_items.id_items
									LEFT JOIN compatible_parts 
										ON items.id_parts = compatible_parts.id_parts
									LEFT JOIN movements 
										ON movements.id = movements_items.id_movements
									LEFT JOIN fotografia 
										ON compatible_parts.id_compatible = fotografia.id_compatible
											AND fotografia.id_places='$id_places_to'
											AND fotografia.id = movements_items.id_fotografia
								ORDER BY movements.insert_date DESC 
						) t
						GROUP BY t.id_items
						HAVING t.fotografia>0";

				$result=runSQL($query);
	
				while($row=mysqli_fetch_assoc($result))
					unset($locations[$row["fotografia"]]);
				((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
			}
		}
	}
	$visible=(count($locations)>1);

	if(!$visible)
		$visibleString=" style='display:none'";
	else
	{
		$visibleString="";
		?>
		<select name="fotografia_<?=$index?>_<?=$sn?>§<?=$id_owners?>§<?=$location?>" 
			<?=$visibleString?> onchange="doChangeFotografia(this)">
	<?
		foreach($locations as $id=>$text)
		{?>
			<option value="<?=$id?>">
				<?=$text?>
		<?}?>
		</select>
		<?
	}
}


function fillRightBlock()
{
	$id_parts=0;
	$ajax_id_places_to=$_POST["ajax_id_places_to"];
	list($id_places_to,$places_type,$foo)=explode("_",$ajax_id_places_to);
	$ajax_pn=$_POST["ajax_pn"];
	$ajax_id_places_exploded=explode("_",$_POST["ajax_id_places"]);
	$ajax_id_places=$ajax_id_places_exploded[0];

	$index=$_POST["ajax_index"];
	$out="";
	$query="SELECT id FROM parts
			WHERE
				(pn_manufacturer='$ajax_pn'
					OR pn_supplier='$ajax_pn')
				AND 
				(id_suppliers='$ajax_id_places'
					OR id_manufacturers='$ajax_id_places')";

	$result=runSQL($query);
	$showNewItem=(mysqli_num_rows($result)>0);

	$query="SELECT items.id as itemId,items.sn,count(items.id) as qty,
						owners.name as owner,
						items.location,items.id_owners 
					FROM items LEFT JOIN parts
						ON items.id_parts=parts.id
						LEFT JOIN owners ON 
						items.id_owners=owners.id 
					WHERE items.id_places='$ajax_id_places'
					AND (parts.pn_manufacturer='$ajax_pn'
						OR parts.pn_supplier='$ajax_pn')
					GROUP BY sn,items.location,items.id_owners";
	$result=runSQL($query);

	if(mysqli_num_rows($result))
	{//item present at supplier's
		$i=0;
?>

		<table class="itemsSubtable">
			<tr>
				<td>
					<input type="hidden" 
						name="cellPartNumber_<?=$index?>" 
						id="cellPartNumber_<?=$index?>" 
						value="<?=$ajax_pn?>">
				</td>
				<td class="fieldTitle">from location</td>
				<td class="fieldTitle">owner</td>
				<td class="fieldTitle">S/N</td>
				<td class="fieldTitle">qty</td>
				<td class="fieldTitle">to location</td>
				<td class="fieldTitle">
					<span name="repair_header" style="display:none">to repair</span>
				</td>
				<td class="fieldTitle">
					<span name="fotografia_header" style="display:none">fotografia</span>
				</td>
			</tr>
<?
		require_once("fotografia.php");
		$children_automatically_selected="";
		while($row=mysqli_fetch_assoc($result))
		{
			$checked_condition=($_POST["ajax_selectedSn"]==$row["itemId"]);
			$checked=($checked_condition?" checked='checked'":"");
			$repair_visible=((strlen(trim($row["sn"]))>0)
				&&($checked_condition)
				&&(((int)$places_type)>2)?"":" style='display:none'");
			$children=get_children_items($row["itemId"]);
			if($checked_condition)
			{
				$children_automatically_selected=$children;
				$sn_automatically_selected=$row["sn"];
			}
			?>
				<tr>
					<td>
						<input type="checkbox" itemId="<?=$row["itemId"]?>"
							name="sn_<?=$index?>_<?=$row["sn"]?>§<?=$row["id_owners"]?>§<?=$row["location"]?>" 
							onclick="canAddElement(this,'<?=$children?>')"
							<?=$checked?>>
					</td>
					<td>
						<?=(strlen(trim($row["location"]))?$row["location"]:"---")?>
					</td>
					<td>
						<?=(strlen($row["owner"])?$row["owner"]:"---")?>
					</td>
					<td>
						<?=(strlen($row["sn"])?$row["sn"]:"no serial")?>
					</td>
					<td>
		<?
			if(!strlen($row["sn"]))
			{?>
						<input type="text" class="threeDigitInput"
							name="qty_<?=$index?>§<?=$row["id_owners"]?>§<?=$row["location"]?>"
							value="<?=$row["qty"]?>"
							onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"
							onchange="qtyChanged(this,<?=$row["qty"]?>)">
			<?}?>

					</td>
					<td>
						<input type="text" class="longTextInput"
							name="location_<?=$index?>_<?=$row["sn"]?>§<?=$row["id_owners"]?>§<?=$row["location"]?>">
					</td>
					<td>
<?			if(strlen($row["sn"]))
			{?>
						<input type="checkbox" <?=$repair_visible?>
							name="repair_<?=$index?>_<?=$row["sn"]?>§<?=$row["id_owners"]?>§<?=$row["location"]?>">
			<?}?>
					</td>
					<td id="fotografia_cell_<?=$index?>_<?=$i?>">
						<?
							buildFotografiaCombo($id_places_to,
								$ajax_pn,
								$row["sn"],
								$index,
								$row["id_owners"],
								$row["location"]);
						?>
					</td>
				</tr>
		<?
			$i++;
		}
		
?>
		</table>
<?
	}
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	if($showNewItem)
		echo new_item_table($index);

	if(strlen($children_automatically_selected))
	{?>
		<script language="JavaScript" type="text/javascript">
			sender={checked:true,name:"sn_<?=$index?>_<?=$sn_automatically_selected?>"};
			canAddElement(sender,'<?=$children_automatically_selected?>');
		</script>
	<?}
	die();

}

function new_item_table($index,$n=0)
{
	global $myhost,$myuser,$mypass;

	$simulator_id=$_SESSION["simulator_id"];

	$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass));
	((bool)mysqli_query($conn , "USE " . $mydb));

	$owners=array();
	$query = "SELECT * FROM owners 
			WHERE id_simulators='$simulator_id'
			ORDER BY name";

	$result=runSQL($query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$owners[$row["id"]]=$row["name"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);

	$out='
	<table>
		<tr>
			<td style="vertical-align:middle">
				<input type="checkbox" 
					name="newItem_'.$index.'_'.$n.'" 
					id="newItem_'.$index.'_'.$n.'" 
					onclick="getNewItemTable(this);canAddElement(this)"
					>
			</td>
			<td>
				<fieldset>
				<legend>new items properties</legend>
				<table>
					<tr>
						<td class="fieldLabel">
							S/N
						</td>
						<td>
							<input	type="text"
									id="ni_'.$index.'_snInput_'.$n.'"
									name="ni_'.$index.'_snInput_'.$n.'"
									class="longTextInput"
									onkeyup="
									if(jQuery.trim(this.value)!=\'\') 
									{
										$(\'#ni_'.$index.'_quantityInput_'.$n.'\').val(\'1\');
										$(\'#ni_'.$index.'_quantityInput_'.$n.'\').attr(\'readonly\',\'readonly\');
										$(\'#ni_'.$index.'_quantityCheck_'.$n.'\').text(\'Quantity forced to 1 when item is serialized.\');
									} 
									else 
									{
										$(\'#ni_'.$index.'_quantityInput_'.$n.'\').attr(\'readonly\',\'\');
										$(\'#ni_'.$index.'_quantityCheck_'.$n.'\').text(\'\');
									}
									"/>
						</td>
						<td	id="ni_'.$index.'_snCheck_'.$n.'" class="fieldCheck">
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">
							Quantity
						</td>
						<td>
							<input	type="text"
									id="ni_'.$index.'_quantityInput_'.$n.'"
									name="ni_'.$index.'_quantityInput_'.$n.'"
									class="threeDigitInput"
									value=\'1\'
									onkeyup="
										this.value=this.value.replace(/[^0-9]/g,\'\');
										if(jQuery.trim($(\'#ni_'.$index.'_quantityInput_'.$n.'\').val())!=\'1\') 
										{
											$(\'#ni_'.$index.'_snInput_'.$n.'\').val(\'\');
										}"
									onchange="
										if(this.value==0)
											if(this.value=1);"
									/>
						</td>
						<td	id="ni_'.$index.'_quantityCheck_'.$n.'" class="fieldCheck">
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">
							Location / note
						</td>
						<td>
							<input	type="text"
									id="ni_'.$index.'_locationInput_'.$n.'" 
									name="ni_'.$index.'_locationInput_'.$n.'"
									/>
						</td>
						<td	id="ni_'.$index.'_locationCheck_'.$n.'" class="fieldCheck">
						</td>
					</tr>
					<tr>
						<td class="fieldLabel">
							Owner
						</td>
						<td>
							<select	id="ni_'.$index.'_ownerSelect_'.$n.'" 
								name="ni_'.$index.'_ownerSelect'.$n.'"
								class="longTextInput">
							<!-- default option -->
							<option value="0">Select owner...</option>';

							foreach($owners as $id=>$owner)
							{
								$out.='
									<option value="'.$id.'">
										'.$owner.'
									</option>';
							}
	$out.='
							</select>
						</td>
						<td	id="ni_'.$index.'_ownerCheck_'.$n.'" class="fieldCheck">
						</td>
					</tr>
				</table>
				</fieldset>

			</td>
		</tr>
	</table>';
	return $out;
}

function getPnFromSn()
{
	// ajax_sn è in realtà itemId
	$id_simulators=$_SESSION["simulator_id"];
	$query="SELECT parts.id,parts.pn_supplier,description
						FROM items LEFT JOIN parts
						ON items.id_parts=parts.id
					WHERE items.id='".$_POST["ajax_sn"]."'";
	$result=runSQL($query);
	$row=mysqli_fetch_assoc($result);
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
//	die($row["id"].",".$row["pn_supplier"]);
	$out=array("id_parts"=>$row["id"],
		"pn_supplier"=>$row["pn_supplier"],"description"=>$row["description"]);
	die(json_encode($out));
}



/*
DROP TRIGGER IF EXISTS `movements_items_insert`//

CREATE TRIGGER movements_items_insert BEFORE INSERT ON movements_items
FOR EACH ROW 
BEGIN
    IF NEW.id_fotografia = '0' THEN
        SET NEW.id_fotografia = NULL;
    END IF;
END;

DROP TRIGGER IF EXISTS `movements_items_update`//

CREATE TRIGGER movements_items_update BEFORE UPDATE ON movements_items
FOR EACH ROW 
BEGIN
    IF NEW.id_fotografia = '0' THEN
        SET NEW.id_fotografia = NULL;
    END IF;
END;

*/
?>
