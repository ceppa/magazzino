<?php
if(!isset($expired))
	$expired=0;

if(!isset($_POST["userNameInput"]))
	$_POST["userNameInput"]="";
if(!isset($message))
	$message="";

if($isLogged==false)
{
	$random_string = $_SESSION['key'] = make_key();
	if(!$expired)
	{
		$username=$_POST["userNameInput"];
		if(strstr($message,"incorrect"))
			$to_focus="passwordInput";
		else
			$to_focus="userNameInput";

?>
<div	class="loginContainer">
<div id="message_box">
	<?=$message?>
</div>
	<form	id="loginForm"
			name="loginForm"
			method="POST"
			action="index.php">
		<fieldset>
		<legend>Login</legend>
		<input type="hidden" name="func" value="doLogin" />
		<div	class="frame_body">
			Username <input	type="text" 
							name="userNameInput"
							id="userNameInput"
							onkeypress="checkEnter
										(	event,
											'passwordInput',
											'loginForm',
											'<?php echo($random_string)?>'
										);"
							value="<?=$_POST["userNameInput"]?>"/>
			<br/>
			<br/>
			Password <input	type="password"
							name="passwordInput"
							id="passwordInput"
							onkeypress="checkEnter
										(	event,
											'passwordInput',
											'loginForm',
											'<?php echo($random_string)?>'
										);"/>
			<div>
			<br/>
			<input	type="button"
					class="button"
					id="loginFormSubmit"
					value="SUBMIT"
					onclick="document.loginForm.passwordInput.value =
								hex_md5
								(
									'<?php echo($random_string);?>'+hex_md5
									(
										document.loginForm.passwordInput.value
									)
								);
								document.loginForm.submit();"/>
			</div>
		</div>
        </fieldset>
	</form>
	<script language="javascript" type="text/javascript">
		$('#<?=$to_focus?>').focus();
	</script>
</div>
	<?}
	else
	{?>
<div	class="loginContainer">
	<div id="message_box">
		password for user <?=$_POST["userNameInput"]?> is expired<br>
		please choose a new one
	</div>
	<form	id="passForm"
			name="passForm"
			method="POST"
			action="index.php"
			onsubmit="
				if($('#passwordInput').val()!=$('#password2Input').val())
				{
					
					$('#message_box').html('passwords do not match');
					return false;
				}
				else
				{
					return true;
				}">
		<fieldset>
		<legend>Change password</legend>
		<input type="hidden" name="func" value="doChangePass"/>
		<div	class="frame_body">
			New password <input type="password" 
							name="passwordInput"
							id="passwordInput"/>
			<br/>
			<br/>
			Repeat <input	type="password"
							name="password2Input"
							id="password2Input"/>
			<div>
			<br/>
			<input	type="submit"
					id="passwordFormSubmit"
					value="SUBMIT"/>
			</div>
		</div>
        </fieldset>
	</form>
	<script language="javascript" type="text/javascript">
		$('#passwordInput').focus();
	</script>
</div>


	<?}?>
<?php
}
?>
