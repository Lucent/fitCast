<?php include "session.php";

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$id = $_SESSION["id"];

	$weight = array_filter($_POST,
		fn($key) => str_starts_with($key, "Weight-") === true,
		ARRAY_FILTER_USE_KEY
	);
	$intake = array_filter($_POST,
		fn($key) => str_starts_with($key, "Intake-") === true,
		ARRAY_FILTER_USE_KEY
	);

	$intake_sql = array_to_sql($intake, "Intake-");
	$weight_sql = array_to_sql($weight, "Weight-");

	$query = "REPLACE INTO calories (id, date, intake) VALUES {$intake_sql};";
	mysqli_query($conn, $query);
	echo $conn->error;

	$query = "REPLACE INTO weight (id, date, weight) VALUES {$weight_sql};";
	mysqli_query($conn, $query);
	echo $conn->error;

	mysqli_close($conn);
}

function array_to_sql($array, $prefix) {
	global $id, $conn;
	$submission = [];
	foreach ($array as $date => $value) {
		$date = substr($date, strlen($prefix));
		$date = mysqli_real_escape_string($conn, $date);
		$value = $value === "" ? "NULL" : mysqli_real_escape_string($conn, $value);
		$submission[] = "($id, STR_TO_DATE('$date', '%Y-%m-%d'), $value)";
	}
	return implode(", ", $submission);
}
?>
