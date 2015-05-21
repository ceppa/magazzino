<?php
/* connect to database */
require_once('mysql.php');
require_once('functions.php');
$isLogged = false;
if (isset($_SESSION['key']))
	$random_string = $_SESSION['key'];
else
    // a new visitor.
    $random_string = $_SESSION['key'] = make_key();




if(!isset($_POST['func']))
	$_POST['func']='';
if(!isset($_REQUEST['func']))
	$_REQUEST['func']='';

if($_POST['func']=='doLogin')
{
	if(isset($_POST['userNameInput']))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $mydb))
			or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query=	"SELECT users.*"
			.	",users_types.id AS user_type"
			.	",simulators.id AS simulator_id"
			.	" FROM users"
			.	" LEFT JOIN users_types"
			.	" ON users.id_users_types = users_types.id"
			.	" LEFT JOIN simulators"
			.	" ON users.id_simulators = simulators.id"
			.	" WHERE username=\"".$_POST["userNameInput"]."\""
			.	" AND users.active=1";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query);

		if(mysqli_num_rows($result))
		{
			$row=mysqli_fetch_assoc($result);
			$combined_hash = md5($random_string.$row['password']);
			if (($_POST['passwordInput'] == $combined_hash))
			{
				if($row['expired']==1)
				{
					$message = 'your password has expired<br>please choose a new one';
					$expired=true;
					$isLogged = false;
					$_SESSION['user_id']=$row['id'];
				}
				else
				{
					$_SESSION['user_id']=$row["id"];
					$_SESSION['tspm_user_id']=$row["id_user_tspm"];
					$_SESSION['simulator_id']=$row["simulator_id"];
					$_SESSION['user_type']=$row["user_type"];
					$_SESSION['name']=ucfirst($row["name"])." ".ucfirst($row["surname"]);
					$_SESSION['username']=$row["username"];
					$_SESSION['session_password'] = $combined_hash;
					$_SESSION['password']=$row['password'];
					$_SESSION['simulator_id']=$row['simulator_id'];
					$_SESSION['rp']=$row['rp'];
					$isLogged = true;
				}
			}
			else
			{
				$isLogged = false;
				$message = 'incorrect password for user '.$_POST["userNameInput"];
			}
			((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		}
		else
		{
			$message = 'unknown user '.$_POST["userNameInput"];
		}
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	}
}
elseif($_POST['func']=='doChangePass')
{
	if(isset($_POST['passwordInput']))
	{
		$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
			or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		((bool)mysqli_query($conn, "USE " . $mydb))
			or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query=	"UPDATE users SET 
					password=\"".md5($_POST["passwordInput"])."\",
					expired=0
				WHERE id=\"".$_SESSION["user_id"]."\"";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
		$query=	"SELECT users.*"
			.	",users_types.id AS user_type"
			.	",simulators.id AS simulator_id"
			.	" FROM users"
			.	" LEFT JOIN users_types"
			.	" ON users.id_users_types = users_types.id"
			.	" LEFT JOIN simulators"
			.	" ON users.id_simulators = simulators.id"
			.	" WHERE users.id=\"".$_SESSION["user_id"]."\""
			.	" AND users.active=1";
		$result=mysqli_query($GLOBALS["___mysqli_ston"], $query)
			or die("$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

		if(mysqli_num_rows($result))
		{
			$row=mysqli_fetch_assoc($result);
			$combined_hash = md5($random_string.$row['password']);
			$_SESSION['simulator_id']=$row["simulator_id"];
			$_SESSION['tspm_user_id']=$row["id_user_tspm"];
			$_SESSION['user_type']=$row["user_type"];
			$_SESSION['name']=ucfirst($row["name"])." ".ucfirst($row["surname"]);
			$_SESSION['username']=$row["username"];
			$_SESSION['session_password'] = $combined_hash;
			$_SESSION['password']=$row['password'];
			$_SESSION['simulator_id']=$row['simulator_id'];
			$_SESSION['rp']=$row['rp'];
			$isLogged = true;
		}
		((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);
		((is_null($___mysqli_res = mysqli_close($conn))) ? false : $___mysqli_res);
	}
}
elseif($_REQUEST['func']=='doLogout')
{
	unset($_SESSION["key"]);
	$isLogged = false;
	session_destroy();
	header("location: ".$_SERVER["PHP_SELF"]."?rnd=".time());
}
else
{
	$isLogged = false;
	$combined_hash = md5($random_string.$_SESSION["password"]);
	if (@$_SESSION['session_password'] == $combined_hash)
        	$isLogged = true;
}

?>
