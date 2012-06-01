<?
include "Script/functions.php";

if (!empty($_POST)) {
	switch ($_POST["Submit"]) {
		case "Log in":
			$userdata = login($_POST["username"], $_POST["password"]);
			break;
		case "Register":
			$userdata = register($_POST["username"], $_POST["password"]);
	}
	if (is_array($userdata)) {
		set_session_vars($userdata);
	?>
Login accepted, <?= $userdata["username"] ?>.
	<? } elseif ($userdata === "NOUSER") { ?>
<form method="post">
<fieldset>
That username doesn't exist. Want to register it?
 <legend>Register</legend>
 Username: <input type="text" name="username" maxlength="30" value="<?= $_POST["username"] ?>"><br>
 Password: <input type="password" name="password" value="<?= $_POST["password"] ?>"><br>
 <input type="submit" name="Submit" value="Register">
</form>
	<? } elseif ($userdata === "BADPASS") { ?>
<form method="post">
<fieldset>
"<?= $_POST["username"] ?>" exists. Did you forget your password?
 <legend>Retry Log in</legend>
 Username: <input type="text" name="username" maxlength="30" value="<?= $_POST["username"] ?>"><br>
 Password: <input type="password" name="password"><br>
 <input type="submit" name="Submit" value="Log in">
</form>
	<? }
} else { ?>
<form method="post">
<fieldset>
 <legend>Log in or Register</legend>
 Username: <input type="text" name="username" maxlength="30"><br>
 Password: <input type="password" name="password"><br>
 <input type="submit" name="Submit" value="Log in">
 <input type="submit" name="Submit" value="Register">
</form>
<? } ?>
