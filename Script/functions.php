<?
session_start();
function expenditure($sex, $weight, $height, $age) {
	if ($sex == "m")
		return 66 + 6.23 * $weight + 12.7 * $height - 6.76 * $age;
	elseif ($sex == "f")
		return 655 + 4.35 * $weight + 4.7 * $height - 4.7 * $age;
}

$date_start = new DateTime($_GET["start"]);
$date_start_int = $date_start->format("j");
$date_end = new DateTime($_GET["end"]);
$days = date_diff($date_start, $date_end)->format("%a");

$food = array();
$exercise = array();
$measured = array();

include "Script/database.php";
if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$query = "SELECT date, food, exercise, net FROM calories WHERE id=" . $_SESSION['userid'] . " AND date >= '" . $date_start->format("Y-m-d") . "' AND date <= '" . $date_end->format("Y-m-d"). "'";
	$result = mysqli_query($conn, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		if (array_key_exists("food", $row))
			$food[$row["date"]] = $row["food"];
	}
	echo $conn->error;
}

$blocksize = 60; $leftmargin = 90; $verticalblocks = 4;
$actualColor = "#3366CC";
$measuredColor = "#DC3912";

$sex = $_GET["sex"];
$weight = $_GET["weight"];
$height = $_GET["feet"] * 12 + $_GET["inches"];
$age = $_GET["age"];
$lifestyle = $_GET["lifestyle"];

$bmr = expenditure($sex, $weight, $height, $age);
$loss = array();
$net = array();
$months = array();

for ($day = 0; $day <= $days; $day++) {
	// find distinct months
	$temp = clone $date_start;
	$today = $temp->add(new DateInterval("P".$day."D"));
	$months[$today->format("F Y")]++;

	$net[$today->format("Y-m-d")] = $food[$today->format("Y-m-d")] - $exercise[$today->format("Y-m-d")];

	if ($food[$today->format("Y-m-d")] == "") {
		$loss[$day] = 0;
	} else {
		$loss[$day] = $food[$today->format("Y-m-d")] - expenditure($sex, $weight + $cumulative[$day] / 3500, $height, $age) * $lifestyle - $exercise[$day];
	}
	$cumulative[$day] += $cumulative[$day - 1] + $loss[$day];
}

function output_json_table($date_start_int, $days, $weight, $cumulative, $measured) {
	$table = array();
	$table[] = array("Date", "Actual", "Measured");
	for ($day = 0; $day <= $days; $day++) {
		$table[] = array(
			$date_start_int + $day + 0.5,
			$weight + $cumulative[$day] / 3500,
			$measured[$day] == "" ? null : (float) $measured[$day]
		);
	}
	echo "var data = ", json_encode($table), ";";
}

function new_week($x, $start) {
	$temp = clone $start;
	if ($temp->add(new DateInterval("P".$x."D"))->format("w") == 0)
		return ' class="NewWeek"';
	else
		return '';
}

?>
