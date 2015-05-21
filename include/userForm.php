<?php
/*include databse data*/
include_once("mysql.php");

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

/*prepare simulators query*/
$query = "SELECT * FROM simulators";

/*execute simulators query*/
$simulators = mysqli_query($GLOBALS["___mysqli_ston"], $query)
	or die("Query error<br>".((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)));
?>

<form	id="userForm"
		name="userForm"
		action="include/new_user.php"
		method="post">
	<table>
		<tr>
			<td class="userFormLabel">
				Username
			</td>
			<td>
				<input	type="text"
						id="userNameInput"
						name="userNameInput"/>
			</td>
			<td	id="userNameCheck" class="userFormCheckCell">
			</td>
		</tr>
		<tr>
			<td class="userFormLabel">
				Name
			</td>
			<td>
				<input	type="text"
						id="nameInput"
						name="nameInput"/>
			</td>
			<td	id="nameCheck" class="userFormCheckCell">
			</td>
		</tr>
		<tr>
			<td class="userFormLabel">
				Surname
			</td>
			<td>
				<input	type="text"
						id="surnameInput"
						name="surnameInput"/>
			</td>
			<td	id="surnameCheck" class="userFormCheckCell">
			</td>
		</tr>
		<tr>
			<td class="userFormLabel">
				Email
			</td>
			<td>
				<input	type="text"
						id="emailInput"
						name="emailInput"/>
			</td>
			<td	id="emailCheck" class="userFormCheckCell">
			</td>
		</tr>
		<tr>
			<td class="userFormLabel">
				User level
			</td>
			<td>
				<select	id="userTypeSelect"
						name="userTypeSelect">
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
			<td	id="userTypeCheck" class="userFormCheckCell">
			</td>
		<tr>
			<td class="userFormLabel">
				Simulator
			</td>
			<td>
				<select	id="simulatorSelect"
						name="simulatorSelect">
					<?php
					while($simulator = 
							mysqli_fetch_assoc($simulators))
					{
						?>
						<option value="<?php echo $simulator['id']?>">
							<?php echo $simulator['name']?>
						</option>
						<?php
					}
					?>
				</select>
			</td>
			<td	id="simulatorCheck" class="userFormCheckCell">
			</td>
		</tr>
		<tr>
			<td colspan="3">
				<input	type="submit"
						id="userFormSubmit"
						value="SUBMIT"/>
				<input	type="reset"
						id="userFormReset"
						value="RESET"/>
			</td>
		</tr>
	</table>
</form>

<?php
/*free mysql results*/
((mysqli_free_result($users_types) || (is_object($users_types) && (get_class($users_types) == "mysqli_result"))) ? true : false);
((mysqli_free_result($simulators) || (is_object($simulators) && (get_class($simulators) == "mysqli_result"))) ? true : false);
?>