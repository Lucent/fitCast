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
		echo 'Login accepted, $userdata["username"]';
	} elseif ($userdata === "NOUSER") {
		echo "That username doesn't exist. Want to register it?";
		draw_login_register("Register", $_POST["username"], $_POST["password"], array("Register"));
	} elseif ($userdata === "BADPASS") {
		echo '$_POST["username"] exists. Did you forget your password?';
		draw_login_register("Retry Log in", $_POST["username"], "", array("Log in"));
	}
} else {
	draw_login_register("Log in or Register", "", "", array("Log in", "Register"));
}
?>
