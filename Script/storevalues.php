<? include "functions.php";

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$id = $_SESSION["id"];
	$submission = array();

	foreach ($_POST as $key => $val) {
		$pair = explode(":", $key);
		if (!isset($submission[$pair[1]]))
			$submission[$pair[1]] = array();
		$submission[$pair[1]][$pair[0]] = $val == "" ? "NULL" : $val;
	}

	$values = array();
	foreach ($submission as $date => $vals) {
		$date = date_create_from_format("Y-m-d", $date)->format("Y-m-d");
		$values[] = "($id, '$date', {$vals['Food']}, {$vals['Exercise']}, {$vals['Measured']})";
	}
	$query = "REPLACE INTO calories (id, date, food, exercise, measured) VALUES " . implode(", ", $values) . ";";
	echo $query;
	mysqli_query($conn, $query);
	echo $conn->error;

	mysqli_close($conn);
}
?>
