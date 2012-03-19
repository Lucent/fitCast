<!doctype html>
<html>
<head>
<style>
body				{ font-family: sans-serif; }
h1					{ margin-bottom: 0; }
h2					{ font-size: medium; }
table, fieldset		{ display: inline-block; }
table				{ border-collapse: collapse; }
fieldset			{ padding: 1ex; border: medium solid green; -ms-border-radius: 1ex; }
fieldset *			{ color: green; }
legend				{ font-weight: bold; }
form				{ display: inline; }
input[type=text]	{ width: 5ex; font-size: medium; }
#PredictedWeight	{ float: right; }
.odd				{ background-color: #EEE; }
.NewWeek			{ border-top: thin solid black; }
</style>
<script src="Script/weightcast.js"></script>
<? /*
Generated from:
'http://www.google.com/jsapi?autoload=' + encodeURIComponent(JSON.stringify({
    "modules": [{
        "name": "visualization",
        "version": "1",
        "packages": ["corechart"],
        "callback": "drawChart"
    }]
})) */ ?>
<script src="http://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22visualization%22%2C%22version%22%3A%221%22%2C%22packages%22%3A%5B%22corechart%22%5D%2C%22callback%22%3A%22drawChart%22%7D%5D%7D"></script>
</head>
<body>
<h1>WeightCast</h1>
<h2>Forecasting your weight with more precision than a jeweler's scale.</h2>
<form method="get">

<fieldset>
<legend>Measurements</legend>
Weight: <input name="weight" type='text' value="<?= $_GET["weight"] ?>"> lbs<br>
Age: <input name="age" type='text' value="<?= $_GET["age"] ?>"> years<br>
Sex: <input type="radio" name="sex" value="m" id="male" <?= ($_GET["sex"] == "m" ? "checked" : "") ?>><label for="male">male</label> <input type="radio" name="sex" value="f" id="female" <?= ($_GET["sex"] == "f" ? "checked" : "") ?>><label for="female">female</label><br>
Height: <select name="feet"><? for ($x = 4; $x <= 6; $x++) { ?><option value=<?= $x ?> <?= ($_GET["feet"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> ft <select name="inches"><? for ($x = 0; $x < 12; $x++) { ?><option value=<?= $x ?> <?= ($_GET["inches"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> in<br>
</fieldset>

<?
function expenditure($sex, $weight, $height, $age) {
	if ($sex == "m")
		$bmr = 66 + 6.23 * $weight + 12.7 * $height - 6.76 * $age;
	elseif ($sex == "f")
		$bmr = 655 + 4.35 * $weight + 4.7 * $height - 4.7 * $age;
	return $bmr * 1.2;
}
?>

<fieldset>
<legend>Lifestyle (BMR=<?= round($bmr) ?>)</legend>
<input type="radio" name="lifestyle" value="sedentary" id="sedentary" checked><label for="sedentary">Sedentary: <?= round(expenditure($_GET["sex"], $_GET["weight"], $_GET["feet"] * 12 + $_GET["inches"], $_GET["age"])) ?> cal/day</label><br>
</fieldset>

<br>

<table id="Table" cellspacing=0 cellpadding=5>
<thead>
<tr>
 <th colspan="2">Date</th>
 <th>Intake<br>(cal)</th>
 <th></th>
 <th>Exercise<br>(cal)</th>
 <th></th>
 <th>Net<br>(cal)</th>
 <th>Today<br>(lbs)</th>
 <th>Cumulative<br>(lbs)</th>
 <th>Predicted<br>(lbs)</th>
</tr>
</thead>
<tbody>
<?
$cumulative = 0;
for ($x = -20; $x < 0; $x++) { ?>
<tr class="<?= $x % 2 ? "even" : "odd" ?> <?= date("D", strtotime($x . " day")) == "Sun" ? "NewWeek" : "" ?>">
	<td align="right"><?= date("D", strtotime($x . " day")) ?></td>
	<td><?= date("M j", strtotime($x . " day")) ?></td>
	<td><input name="day<?= $x ?>" type="text" size="4" value="<?= $_GET["day".$x] ?>"></td>
	<td>-</td>
	<td><input name="exercise<?= $x ?>" type="text" size="4" value="<?= $_GET["exercise".$x] ?>"></td>
	<td>=</td>
	<td align="right" id="net<?= $x ?>"><?= $_GET["day".$x] - $_GET["exercise".$x] ?></td>
<?
	if ($_GET["day".$x] == "")
		$loss = 0;
	else
		$loss = $_GET["day".$x] - expenditure($_GET["sex"], $_GET["weight"] + $cumulative / 3500, $_GET["feet"] * 12 + $_GET["inches"], $_GET["age"]) - $_GET["exercise".$x];
	$cumulative += $loss;
	?>
	<td align="right"><?= sprintf("%.2f", round($loss / 3500, 2)) ?></td>
	<td align="right"><?= sprintf("%.1f", round($cumulative / 3500, 1)) ?></td>
	<td><?= round($_GET["weight"] + $cumulative / 3500, 1) ?></td>
	<script>data.push([new Date(<?= date("Y", strtotime($x." day")) ?>, <?= date("n", strtotime($x." day")) - 1 ?>, <?= date("j", strtotime($x." day")) ?>), <?= $_GET["weight"] + $cumulative / 3500 ?>]);</script>
</tr>
<? } ?>
</tbody>
</table>

<div id="PredictedWeight"></div>
<br>
<input type="submit">
</form>

</body>
</html>
