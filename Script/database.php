<?
$dbhost = "localhost";
$dbname = "weightcast";
$dbuser = "weightcast";
$dbpass = "looseint";
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
mysqli_select_db($conn, $dbname);
?>
