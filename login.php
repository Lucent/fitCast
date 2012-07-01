<? include "Script/functions.php"; ?>
<!doctype html>
<html>
<head>
<title>fitCast - Log in or Register</title>
<style>
fieldset	{ padding: 1em; border: thin solid black; display: inline-block; }
legend		{ font-size: large; }
label		{ text-transform: uppercase; font-size: small; }
input[type=text],input[type=password],input[type=email]	{ min-width: 15em; display: block; margin-bottom: 1em; }
</style>
</head>
<body>
<?
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
		echo "Login accepted, {$userdata['username']}.<br><a href='/'>Continue to fitCast.</a>";
	} elseif ($userdata === "NOUSER") {
		echo "That username doesn't exist. Did you mistype it, <a href='forgotusername.php'>forget your username</a>, or do you want to create an account?";
		draw_login_register("Register", $_POST["username"], $_POST["password"], array("Register"));
	} elseif ($userdata === "BADPASS") {
		echo "The username {$_POST['username']} is registered, but you entered the incorrect password. Did you forget it?";
		draw_login_register("Retry Log in", $_POST["username"], "", array("Log in"));
	}
} else {
	draw_login_register("Log in or Register", "", "", array("Log in", "Register"));
}
?>
</body>
</html>
