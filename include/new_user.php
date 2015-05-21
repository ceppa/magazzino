<?php

/*include databse data*/
include_once("mysql.php");

/*connect to database*/
$conn=($GLOBALS["___mysqli_ston"] = mysqli_connect($myhost, $myuser, $mypass))
	or die("Network connection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
((bool)mysqli_query($conn, "USE " . $mydb))
	or die("Database selection<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));

/*Initial password is first 8 characters of md5(username)*/
$pass=substr(md5($_POST["userNameInput"]),0,8);

/*prepare query*/
$query="INSERT INTO users(	id_users_types,
							id_simulators,
							username,
							password,
							name,
							surname,
							email,
							expired	)
				VALUES(	  \"".$_POST["userTypeSelect"]."\","
						."\"".$_POST["simulatorSelect"]."\","
						."\"".$_POST["userNameInput"]."\","
						."\"".md5($pass)."\","
						."\"".$_POST["nameInput"]."\","
						."\"".$_POST["surnameInput"]."\","
						."\"".$_POST["emailInput"]."\","
						."\"1\"	)";

/*insert new user into database and return success or error messages*/
if((!mysqli_query($GLOBALS["___mysqli_ston"], $query)))
{
	if(((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_errno($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_errno()) ? $___mysqli_res : false))!="1062")
	{ 
		echo "$query<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)); 
	}
	else
	{
		echo "User ".$_POST["userNameInput"]." already exists.";
	}
}
else
{
	echo "User ".$_POST["userNameInput"]." added successfully.";
}
?>