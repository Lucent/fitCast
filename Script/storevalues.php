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

	foreach ($submission as $date => $vals) {
		$date = date_create_from_format("Y-m-d", $date)->format("Y-m-d");
		$query = "INSERT INTO calories (id, date, food, exercise, measured) VALUES ($id, '$date', {$vals['food']}, {$vals['exercise']}, {$vals['measured']}) ON DUPLICATE KEY UPDATE food={$vals['food']}, exercise={$vals['exercise']}, measured={$vals['measured']};";
		echo $query, "\n";
		mysqli_query($conn, $query);
		echo $conn->error;
	}

	mysqli_close($conn);
}
?>
