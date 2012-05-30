<?
function validateUser($id, $username) {
	session_regenerate_id();
	$_SESSION["valid"] = 1;
	$_SESSION["username"] = $username;
	$_SESSION["userid"] = $id;
}

session_start();
$username = $_POST["username"];
$password = $_POST["password"];

include "Script/database.php";

$username = mysqli_real_escape_string($conn, $username);
$query = "SELECT id, password, salt FROM users WHERE username = '$username';";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) < 1) {
	echo "No such user";
	die();
}
$userData = mysqli_fetch_array($result, MYSQL_ASSOC);
$hash = hash("sha256", $userData["salt"] . hash("sha256", $password));
if ($hash != $userData["password"]) {
	echo "Bad password";
	die();
}
validateUser($userData["id"], $username);
echo "Succesfully logged in.";
session_start();

?>
