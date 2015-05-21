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
    $conn	=	($GLOBALS["___mysqli_ston"] = mysqli_connect(	$myhost, 
								$myuser, 
								$mypass));

	((bool)mysqli_query(
						$conn , "USE " . $mydb));
                        
	/* table page number */
	$page = $_POST['page'];
	if(!$page)
	{
		$page = 1;
	}
	/* records per page */
	$rp = $_POST['rp'];
	/* sort field */
	$sortname = @ifnull($_POST['sortname']);
	$sortorder = @ifnull($_POST['sortorder']);

	if (!$sortname) $sortname = 'username';
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
	
	$where=" WHERE users.id_simulators='".$_SESSION["simulator_id"]."' ";

	if($_POST['query']!='')
		$where.= " AND ".$_POST['qtype']." LIKE '%".$_POST['query']."%' ";
	elseif(strlen(@ifnull($_POST["whereString"])))
		$where.=stripslashes($_POST["whereString"]);

	$sql = "SELECT users.id FROM users 
				$where";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("$sql<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$total = mysqli_num_rows($result);

	$sql =	"SELECT	users.id AS userid,
					users.username AS username,
					users.name AS name,
					users.surname AS surname,
					users.email AS email,
					users.active AS active,
					users.expired AS expired,
					users_types.name AS usertype
			FROM users 
				LEFT JOIN users_types 
				ON users_types.id = users.id_users_types 
			$where $sort $limit";
		
	$logfile = fopen("log.log", "a+"); 
	fwrite($logfile,$sql."\r\n\r\n");

	$conn	=	($GLOBALS["___mysqli_ston"] = mysqli_connect(	$myhost, 
								$myuser, 
								$mypass));

	((bool)mysqli_query(
						$conn , "USE " . $mydb));

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
	$json .= "\"page\": $page,\n";
	$json .= "\"total\": $total,\n";
	$json .= "\"rows\": [";
	$rc = false;
	while ($row = mysqli_fetch_array($result)) 
	{
		if ($rc) $json .= ",";
		$json .= "\n{";
		$json .= "\"id\":\"".$row['userid']."\",";
		$json .= "\"cell\":[\"".$row['username']."\"";
		$json .= ",\"".addslashes($row['name'])."\"";
		$json .= ",\"".addslashes($row['surname'])."\"";
		$json .= ",\"".addslashes($row['email'])."\"";
		$json .= ",\"".addslashes($row['usertype'])."\"";
		if($row['active']==0)
		{
			$json .= ",\"<img src='img/stock_draw-circle-red.png'/>\"";
		}
		elseif($row['active']==1)
		{
			$json .= ",\"<img src='img/stock_draw-circle-green.png'/>\"";
		}
		if($row['expired']==0)
		{
			$json .= ",\"&nbsp;\"]";
		}
		elseif($row['expired']==1)
		{
			$json .= ",\"<img src='img/stock_draw-circle-red.png'/>\"]";
		}
		$json .= "}";
		$rc = true;
	}
	$json .= "]\n";
	$json .= "}";

	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);

	echo $json;
	die();
}


elseif($func=='newUserForm')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user types query*/
	$query = "SELECT * FROM users_types";

	/*execute user types query*/
	$users_types = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	$tspm_users=array(0=>"...select...");
	$tspm_conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($tspm_conn, "USE " . $mydb_tspm))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$query = "SELECT * FROM utenti WHERE attivo=1";
	$result=mysqli_query($tspm_conn, $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$tspm_users[$row["id"]]=$row["login"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($tspm_conn))) ? false : $___mysqli_res);

	?>

	<form	id="userForm"
			name="userForm"
			action="include/users.php"
			method="post">
		<input 	type="hidden"
				id="func"
				name="func"
				value="add"/>
        <fieldset>
		<legend>New User</legend>
		<table>
			<tr>
				<td class="fieldLabel">
					Username
				</td>
				<td>
					<input	type="text"
							id="userNameInput"
							name="userNameInput"
							class="longTextInput"/>
				</td>
				<td	id="userNameCheck" class="fieldCheck">
				</td>
			</tr>
<?
		if($_SESSION["simulator_id"]==2)
		{?>
			<tr>
				<td class="fieldLabel">
					TSPM User
				</td>
				<td>
					<select	id="tspmUserSelect"
							name="tspmUserSelect"
							class="longTextInput">
		<?
				foreach($tspm_users as $id=>$tspm_user)
				{
		?>
						<option value="<?=$id?>">
							<?=$tspm_user?>
						</option>
		<?
				}
		?>
				</td>
				<td	id="tspmUserCheck" class="fieldCheck">
				</td>
			</tr>
		<?}?>
			<tr>
				<td class="fieldLabel">
					Name
				</td>
				<td>
					<input	type="text"
							id="nameInput"
							name="nameInput"
                            class="longTextInput"/>
				</td>
				<td	id="nameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Surname
				</td>
				<td>
					<input	type="text"
							id="surnameInput"
							name="surnameInput"
                            class="longTextInput"/>
				</td>
				<td	id="surnameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Email
				</td>
				<td>
					<input	type="text"
							id="emailInput"
							name="emailInput"
							class="longTextInput"/>
				</td>
				<td	id="emailCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					User level
				</td>
				<td>
					<select	id="userTypeSelect"
							name="userTypeSelect"
                            class="longTextInput">
					<?php
					while($user_type = mysqli_fetch_assoc($users_types))
					{
						?>
						<option value="<?php echo $user_type['id']?>">
							<?php echo $user_type['name']?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="userTypeCheck" class="fieldCheck">
				</td>
			</tr>
		</table>
        </fieldset>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($users_types) || (is_object($users_types) && (get_class($users_types) == "mysqli_result"))) ? true : false);
}

elseif($func=='editUserForm')
{
	$tspm_users=array(0=>"...select...");
	$tspm_conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($tspm_conn, "USE " . $mydb_tspm))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$query = "SELECT * FROM utenti WHERE attivo=1";
	$result=mysqli_query($tspm_conn, $query)
		or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	while($row=mysqli_fetch_assoc($result))
		$tspm_users[$row["id"]]=$row["login"];
	((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
	((is_null($___mysqli_res = mysqli_close($tspm_conn))) ? false : $___mysqli_res);

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user types query*/
	$query = "SELECT * FROM users_types";

	/*execute user types query*/
	$users_types = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user query*/
	$query =  "SELECT * FROM users";
	$query .= " WHERE id=\"".$_POST['userId']."\"";

	/*execute users query*/
	$users = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$user = mysqli_fetch_assoc($users);
	?>

	<form	id="userForm"
			name="userForm"
			action="include/users.php"
			method="post">
        <fieldset>
		<legend>Edit User Details</legend>
		<input 	type="hidden"
				id="func"
				name="func"
				value="edit"/>
		<input	type="hidden"
				id="userId"
				name="userId"
				value="<?php echo($_POST['userId']); ?>"/>
		<table>
			<tr>
				<td class="fieldLabel">
					Username
				</td>
				<td>
					<input	type="text"
							id="userNameInput"
							name="userNameInput"
							class="longTextInput"
							value="<?php echo($user['username']);?>"/>
				</td>
				<td	id="userNameCheck" class="fieldCheck">
				</td>
			</tr>
<?
		if($_SESSION["simulator_id"]==2)
		{?>
			<tr>
				<td class="fieldLabel">
					TSPM User
				</td>
				<td>
					<select	id="tspmUserSelect"
							name="tspmUserSelect"
							class="longTextInput">
		<?
				foreach($tspm_users as $id=>$tspm_user)
				{
		?>
						<option value="<?=$id?>"<?=($user['id_user_tspm']==$id?' selected="selected"':'')?>>
							<?=$tspm_user?>
						</option>
		<?
				}
		?>
				</td>
				<td	id="tspmUserCheck" class="fieldCheck">
				</td>
			</tr>
		<?}?>
			<tr>
				<td class="fieldLabel">
					Name
				</td>
				<td>
					<input	type="text"
							id="nameInput"
							name="nameInput"
                            class="longTextInput"
							value="<?php echo($user['name']);?>"/>
				</td>
				<td	id="nameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Surname
				</td>
				<td>
					<input	type="text"
							id="surnameInput"
							name="surnameInput"
                            class="longTextInput"
							value="<?php echo($user['surname']);?>"/>
				</td>
				<td	id="surnameCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					Email
				</td>
				<td>
					<input	type="text"
							id="emailInput"
							name="emailInput"
                            class="longTextInput"
							value="<?php echo($user['email']);?>"/>
				</td>
				<td	id="emailCheck" class="fieldCheck">
				</td>
			</tr>
			<tr>
				<td class="fieldLabel">
					User level
				</td>
				<td>
					<select	id="userTypeSelect"
							name="userTypeSelect"
                            class="longTextInput">
					<?php
					while($user_type = mysqli_fetch_assoc($users_types))
					{
						?>
						<option value="<?php echo $user_type['id']?>"
							<?php echo(($user['id_users_types']==$user_type['id'])?"selected":"");?>>
							<?php echo $user_type['name']?>
						</option>
						<?php
					}
					?>
					</select>
				</td>
				<td	id="userTypeCheck" class="fieldCheck">
				</td>
			</tr>
		</table>
	</form>

	<?php
	/*free mysql results*/
	((mysqli_free_result($users_types) || (is_object($users_types) && (get_class($users_types) == "mysqli_result"))) ? true : false);
	((mysqli_free_result($users) || (is_object($users) && (get_class($users) == "mysqli_result"))) ? true : false);
}


elseif($func=='add')
{
	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	$pass=substr(md5($_POST["userNameInput"]),0,8);
	$expired=1;
	$tspmUserSelect=(int)$_POST["tspmUserSelect"];

	/*prepare user insertion query*/
	$sql="INSERT INTO users(id_users_types,
							id_simulators,
							username,
							password,
							name,
							surname,
							email,
							expired,
							id_user_tspm)
				VALUES(	  \"".$_POST["userTypeSelect"]."\","
						."\"".$_SESSION["simulator_id"]."\","
						."\"".$_POST["userNameInput"]."\","
						."\"".md5($pass)."\","
						."\"".$_POST["nameInput"]."\","
						."\"".$_POST["surnameInput"]."\","
						."\"".$_POST["emailInput"]."\","
						."\"$expired\","
						."\"$tspmUserSelect\")";

	$logfile = fopen("log.log", "a+"); 
    fwrite($logfile,"\r\n----- users.php?func=add --------\r\n");
	fwrite($logfile,$sql."\r\n");
	fwrite($logfile,"pass: $pass"."\r\n");
	$result = mysqli_query($GLOBALS["___mysqli_ston"], $sql)
		or die("Query error\n".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	fwrite($logfile,((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false))."\r\n\r\n");
    fwrite($logfile,$sql."\r\n---------------------------------\r\n");
	fclose($logfile);

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo("0User ".$_POST["userNameInput"]." added successfully.");
		require_once("mail.php");

		$from = "System Administrator <noreply@hightecservice.biz>";
		$to = $_POST["nameInput"]." ".$_POST["surnameInput"]." <".$_POST["emailInput"].">";
		$subject = "registratione utente";

		$mailtext=file_get_contents("mailTemplateNewUser.html");
		$mailtext=str_replace("{username}",$_POST["userNameInput"],$mailtext);
		$mailtext=str_replace("{password}",$pass,$mailtext);
		$mailtext=str_replace("{name}",$_POST["nameInput"],$mailtext);
		$mailtext=str_replace("{surname}",$_POST["surnameInput"],$mailtext);
		emailHtml($from, $subject, $mailtext, $to);
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo("No insert executed.");
	}
	else
	{
		echo("User could not be added, mysql error.");
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
	$tspmUserSelect=(int)$_POST["tspmUserSelect"];

	/*prepare user deletion query*/
	$query	=	"UPDATE users";
	$query	.=	" SET username=\"".$_POST['userNameInput']."\"";
	$query	.=	",name=\"".$_POST['nameInput']."\"";
	$query	.=	",surname=\"".$_POST['surnameInput']."\"";
	$query	.=	",email=\"".$_POST['emailInput']."\"";
	$query	.=	",id_users_types=\"".$_POST['userTypeSelect']."\"";
	$query	.=	",id_simulators=\"".$_SESSION['simulator_id']."\"";
	$query	.=	",id_user_tspm=\"$tspmUserSelect\"";
	$query  .=	" WHERE id=\"".$_POST['userId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
		echo("0User updated successfully.");
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
		echo("Nothing to update.");
	else
		echo("User could not be updated, mysql error.");
	return;
}

elseif($func=='deactivate')
{
	if(!$_POST['userId'])
		die('No user id specified');

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE users";
	$query	.=	" SET active=\"0\"";
	$query  .=	" WHERE id=\"".$_POST['userId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0User successfully deactivated";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "User already inactive";
	}
	else
	{
		echo "User could not be deactivated, mysql error";
	}
	return;
}

elseif($func=='activate')
{
	if(!$_POST['userId'])
	{
		die('No user id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE users";
	$query	.=	" SET active=\"1\"";
	$query  .=	" WHERE id=\"".$_POST['userId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0User successfully activated";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "User already active";
	}
	else
	{
		echo "User could not be activated, mysql error";
	}
	return;
}

elseif($func=='expire')
{
	if(!$_POST['userId'])
	{
		die('No user id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"UPDATE users";
	$query	.=	" SET expired=\"1\"";
	$query  .=	" WHERE id=\"".$_POST['userId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0User successfully expired";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "User already expired";
	}
	else
	{
		echo "User could not be expired, mysql error";
	}
	return;
}


elseif($func=='delete')
{
	if(!$_POST['userId'])
	{
		die('No user id specified');
	}

	/*connect to database*/
	$conn = ($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
		or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
	((bool)mysqli_query($conn, "USE " . $mydb))
		or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	/*prepare user deletion query*/
	$query	=	"DELETE FROM users";
	$query  .=	" WHERE id=\"".$_POST['userId']."\"";

	$result = mysqli_query($GLOBALS["___mysqli_ston"], $query)
		or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

	if($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==1)
	{
		echo "0User successfully deleted";
	}
	elseif($result && mysqli_affected_rows($GLOBALS["___mysqli_ston"])==0)
	{
		echo "User already deleted";
	}
	else
	{
		echo "User could not be deleted, mysql error";
	}
	return;
}
?>
 
