<?
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

$blocksize = 50; $leftmargin = 90; $verticalblocks = 4;
$actualColor = "#3366CC";
$measuredColor = "#DC3912";

$sex = $_GET["sex"];
$weight = $_GET["weight"];
$height = $_GET["feet"] * 12 + $_GET["inches"];
$age = $_GET["age"];
$lifestyle = $_GET["lifestyle"];

$bmr = expenditure($sex, $weight, $height, $age);
$loss = array();
$food = array();
$cumulative = array();
$net = array();
$exercise = array();
$measured = array();

for ($day = 0; $day <= $days; $day++) {
	$food[$day] = $_GET["food" . $day];
	$exercise[$day] = $_GET["exercise" . $day];
	$net[$day] = $food[$day] - $exercise[$day];
	$measured[$day] = $_GET["measured" . $day];

	if ($food[$day] == "") {
		$loss[$day] = 0;
	} else {
		$loss[$day] = $food[$day] - expenditure($sex, $weight + $cumulative[$day] / 3500, $height, $age) * $lifestyle - $exercise[$day];
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
