<?php include "login.php";

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$id = $_SESSION["id"];

	$submission = [];
	foreach ($_POST as $date => $intake) {
		if ($intake !== "") {
			$date = mysqli_real_escape_string($conn, $date);
			$intake = mysqli_real_escape_string($conn, $intake);
			$submission[] = "($id, STR_TO_DATE('$date', '%Y-%m-%d'), $intake)";
		}
	}
	$values = implode(", ", $submission);

	$query = "REPLACE INTO calories (id, date, intake) VALUES {$values};";
	mysqli_query($conn, $query);
	echo $conn->error;

	mysqli_close($conn);
}
?>
