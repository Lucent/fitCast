<?
function validateUser() {
	session_regenerate_id();
	$_SESSION["valid"] = 1;
	$_SESSION["userid"] = $username;
}

session_start();
$username = $_POST["username"];
$password = $_POST["password"];

$dbhost = "localhost";
$dbname = "weightcast";
$dbuser = "weightcast";
$dbpass = "looseint";
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname, $conn);

$username = mysql_real_escape_string($username);
$query = "SELECT password, salt FROM users WHERE username = '$username';";
$result = mysql_query($query);
if (mysql_num_rows($result) < 1) {
	echo "No such user";
	die();
}
$userData = mysql_fetch_array($result, MYSQL_ASSOC);
$hash = hash("sha256", $userData["salt"] . hash("sha256", $password));
if ($hash != $userData["password"]) {
	echo "Bad password";
	die();
}
validateUser();
echo "Succesfully logged in.";
session_start();

?>
