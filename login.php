<?php
include "server/session.php";
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
		header("Location: /");
		echo "Login accepted, {$userdata['username']}.<br><a href='/'>Continue to fitCast.</a>";
		exit();
	}
?>
<!doctype html>
<html>
<head>
<title>fitCast - Log in or Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root		{ color-scheme: light dark; }
body		{ font-family: sans-serif; color: CanvasText; background-color: Canvas; }
fieldset	{ padding: 1em; border: thin solid CanvasText; display: inline-block; }
legend		{ font-size: x-large; }
label		{ text-transform: uppercase; }
input[type=text],input[type=password],input[type=email]	{ min-width: 15em; display: block; margin-bottom: 1em; }
</style>
</head>
<body>
<?php
	if ($userdata === "NOUSER") {
		echo "That username doesn't exist. Did you mistype it, <a href='forgotusername.php'>forget your username</a>, or do you want to create an account?";
		draw_login_register("Register", $_POST["username"], $_POST["password"], array("Register"));
	} elseif ($userdata === "BADPASS") {
		echo "The username {$_POST['username']} is registered, but you entered the incorrect password.";
		draw_login_register("Retry Log in", $_POST["username"], "", array("Log in"));
	}
} else {
	draw_login_register("Log in or Register", "", "", array("Log in", "Register"));
}
?>
</body>
</html>
