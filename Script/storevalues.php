<?
session_start();

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	include "database.php";
	print_r($_POST);
	$userid = mysqli_real_escape_string($conn, $_SESSION["userid"]);

	foreach ($_POST as $key => $val) {
		if ($val != "") {
			$pair = explode(":", $key);
			$date = date_create_from_format("Y-m-d", $pair[1]);
			$query = "REPLACE INTO calories (id, date, $pair[0]) VALUES ($userid, '" . $date->format("Y-m-d") . "', $val);";
			mysqli_query($conn, $query);
			echo $conn->error;
		}
	}
	mysqli_close($conn);
}
?>
