<?
function expenditure($sex, $weight, $height, $age) {
	if ($sex == "m")
		return 66 + 6.23 * $weight + 12.7 * $height - 6.76 * $age;
	elseif ($sex == "f")
		return 655 + 4.35 * $weight + 4.7 * $height - 4.7 * $age;
}

$days = 14;
$sex = $_GET["sex"];
$weight = $_GET["weight"];
$height = $_GET["feet"] * 12 + $_GET["inches"];
$age = $_GET["age"];
$lifestyle = $_GET["lifestyle"];

$bmr = expenditure($sex, $weight, $height, $age);
$loss = array();
$day = array();
$cumulative = array();
$net = array();
$exercise = array();
$measured = array();

for ($x = -$days; $x <= 0; $x++) {
	$day[$x] = $_GET["day" . $x];
	$exercise[$x] = $_GET["exercise" . $x];
	$net[$x] = $day[$x] - $exercise[$x];
	$measured[$x] = $_GET["measured" . $x];

	if ($day[$x] == "") {
		$loss[$x] = 0;
	} else {
		$loss[$x] = $day[$x] - expenditure($sex, $weight + $cumulative[$x] / 3500, $height, $age) * $lifestyle - $exercise[$x];
	}
	$cumulative[$x] += $loss[$x];
}

function output_json_table($days, $weight, $cumulative, $measured) {
	$table = array();
	$table[] = array("Date", "Actual", "Measured");
	for ($x = -$days; $x < 0; $x++) {
		$table[] = array((int) date("j", strtotime($x." day")) + 0.5, $weight + $cumulative[$x] / 3500, $measured[$x] == "" ? null : (float) $measured[$x]);
	}
	echo "var data = ", json_encode($table), ";";
}

function new_week($x) {
	if (date("w", strtotime($x . " day")) == 0)
		return ' class="NewWeek"';
	else
		return '';
}

?>
