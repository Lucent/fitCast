<? include "functions.php";

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$id = $_SESSION["id"];

	foreach ($_POST as $key => $val) {
		if ($val == "") $val = "NULL";
		$pair = explode(":", $key);
		$date = date_create_from_format("Y-m-d", $pair[1]);
		$query = "INSERT INTO calories (id, date, $pair[0]) VALUES ($id, '" . $date->format('Y-m-d') . "', $val) ON DUPLICATE KEY UPDATE $pair[0]=$val;";
		echo $query, "\n";
		mysqli_query($conn, $query);
		echo $conn->error;
	}
	mysqli_close($conn);
}
?>
