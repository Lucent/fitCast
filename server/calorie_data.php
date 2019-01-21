<?php
include "login.php";

function output_json_table($range, $metabolism, $actual, $measured) {
	$date_start_int = $range["start"]->format("j");
	$table = array();
	$table[] = array("Date", "Actual", "Measured");
	for ($day = 0; $day <= $range["days"]; $day++) {
		$YMD = add_days($range["start"], $day)->format("Y-m-d");

		$table[] = array(
			$date_start_int + $day + 0.5,
			$actual[$YMD],
			isset($measured[$YMD]) ? (float) $measured[$YMD] : null
		);
	}
	echo "var data = ", json_encode($table), ";";
}

function fetch_calories($id) {
	$first_measured = FALSE;
	$conn = database_connect();
	$query = "SELECT date, food, exercise, measured FROM calories WHERE id=$id ORDER BY date;";
	$result = mysqli_query($conn, $query);

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$food[$row["date"]] = $row["food"];
		$exercise[$row["date"]] = $row["exercise"];
		if ($first_measured === FALSE && $row["measured"] != "")
			$first_measured = new DateTime($row["date"]);
		$measured[$row["date"]] = $row["measured"];
	}

	mysqli_close($conn);
	return array("food" => $food, "exercise" => $exercise, "measured" => $measured, "first_measured" => $first_measured);
}

function intake_get() {
	if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
		$conn = database_connect();
		$id = $_SESSION["id"];

		$query = "SELECT date, intake FROM calories WHERE id={$id} ORDER BY date;";
		$result = mysqli_query($conn, $query);

		$intake = [];
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
			$intake[$row["date"]] = $row["intake"];

		mysqli_close($conn);

		return $intake;
	}
}

function draw_table_chart() {
	$intake_array = intake_get();
	echo "<form method='post' action='server/intake_set.php'>\n";
	echo "<table id='Table' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tbody>\n";
	$date = new DateTime();
	for ($x = 0; $x < 30; $x++) {
		$date->sub(new DateInterval("P1D"));
		$date_string = $date->format("Y-m-d");
		$weekday = $date->format("D");
		if ($intake_array[$date_string])
			$intake = $intake_array[$date_string];
		else
			$intake = "";
		echo " <tr class='{$weekday}'><th>{$weekday} {$date_string}</th><td><input type='number' name='{$date_string}' min='0' max='10000' value='{$intake}'></td></tr>\n";
	}
	echo "</tbody>\n";
	echo "</table>\n";
	echo "<input type='submit'>\n";
	echo "</form>\n";
}

?>
