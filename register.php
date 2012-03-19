<form name="register" action="register.php" method="post">
Username: <input type="text" name="username" maxlength="30">
Password: <input type="password" name="password">
<input type="submit" value="Register">
</form>

<?
function createSalt() {
	$string = md5(uniqid(rand(), true));
	return substr($string, 0, 3);
}

$username = $_POST["username"];
$password = $_POST["password"];
if ($username == "" || $password == "")
	die();
$hash = hash("sha256", $password);
$salt = createSalt();
$hash = hash("sha256", $salt . $hash);

$dbhost = "localhost";
$dbname = "weightcast";
$dbuser = "weightcast";
$dbpass = "looseint";
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname, $conn);
$username = mysql_real_escape_string($username);
$query = "INSERT INTO users (username, password, salt) VALUES ('$username', '$hash', '$salt');";
mysql_query($query);
mysql_close();
