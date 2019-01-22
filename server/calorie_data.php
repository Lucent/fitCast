<?php
include "session.php";

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

function draw_table_chart($start = -7, $end = 21) {
	$intake_array = intake_get();
	echo "<form method='post' action='server/intake_set.php'>\n";
	echo "<table id='Table'>\n";
	echo "<thead><tr><th>Date</th><th>Intake</th><th>Cumulative</th><th>Pounds</th></tr></thead>\n";
	echo "<tbody>\n";
	$now = new DateTime();
	for ($x = $start; $x < $end; $x++) {
		$date = clone $now;
		if ($x < 0) {
			$y = abs($x);
			$date->add(new DateInterval("P{$y}D"));
		} else
			$date->sub(new DateInterval("P{$x}D"));

		$date_string = $date->format("Y-m-d");
		$month_day = $date->format("M d");
		$weekday = $date->format("D");
		$today = $x == -1 ? "Today" : "";
		if (array_key_exists($date_string, $intake_array))
			$intake = $intake_array[$date_string];
		else
			$intake = "";
		echo " <tr class='{$weekday} {$today}' id='${date_string}'><th>{$weekday} {$month_day}</th><td><input type='number' name='{$date_string}' min='0' max='10000' value='{$intake}'></td><td><output></output></td><td><output></output></td></tr>\n";
	}
	echo "</tbody>\n";
	echo "</table>\n";
	echo "<input type='submit'>\n";
	echo "</form>\n";
}

?>
