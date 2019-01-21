<?php
session_start();
include "db_password.php";

function database_connect() {
	global $dbpass;
	$dbhost = "localhost";
	$dbname = "fitcast";
	$dbuser = "fitcast";
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
	$conn->set_charset("utf8");
	mysqli_select_db($conn, $dbname);
	return $conn;
}

function set_session_vars($userdata) {
	session_regenerate_id(TRUE);
	$_SESSION["valid"] = 1;
	$_SESSION["username"] = $userdata["username"];
	$_SESSION["id"] = $userdata["id"];
}

function login($username, $password) {
	$conn = database_connect();
	$username = mysqli_real_escape_string($conn, $username);
	$query = "SELECT id, username, password FROM users WHERE username='$username' OR email='$username';";
	$result = mysqli_query($conn, $query);
	if (mysqli_num_rows($result) < 1) {
		return "NOUSER";
	}
	$userdata = mysqli_fetch_array($result, MYSQLI_ASSOC);
	if (!password_verify($password, $userdata["password"])) {
		return "BADPASS";
	}
	mysqli_close($conn);
	return $userdata;
}

function register($email, $password) {
	$username = $email;
	if ($email == "" || $password == "") return FALSE;
	$password_hash = password_hash($password, PASSWORD_BCRYPT);

	$conn = database_connect();
	$user = mysqli_real_escape_string($conn, $email);
	$query = "INSERT INTO users (username, email, password) VALUES ('$email', '$email', '$password_hash');";
	mysqli_query($conn, $query);
	echo $conn->error;
	mysqli_close($conn);
}

function draw_login_register($legend, $username, $password, $submittype) {
	echo "<form method='post'>\n";
	echo " <fieldset>\n";
	echo "  <legend>$legend</legend>\n";
	if ($legend == "Register") {
		echo "  <label for='Username'>Username</label> <input id='Username' type='text' name='username' value='$username'>\n";
		echo "  <label for='E-mail'>E-mail (optional)</label> <input id='E-mail' type='email' name='email' value=''>\n";
	} else {
		echo "  <label for='Username'>Username or E-mail</label> <input id='Username' type='text' name='username' value='$username'>\n";
	}
	echo "  <label for='Password'>Password</label> <input id='Password' type='password' name='password' value='$password'>\n";
	if ($legend == "Register") {
		echo "  <label for='Password2'>Re-type Password</label> <input id='Password2' type='password' name='password2' value=''>\n";
	}
	foreach ($submittype as $button)
		echo "  <input type='submit' name='Submit' value='$button'>\n";
	echo " </fieldset>\n";
	echo "</form>\n";
}

?>
