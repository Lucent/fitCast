<!doctype html>
<html>
<head>
<style>input[type=text]	{ width: 5ex; }</style>
</head>
<body>
<form method="get">

<fieldset>
<legend>Measurements</legend>
Weight: <input name="weight" type='text' value="<?= $_GET["weight"] ?>"> lbs<br>
Age: <input name="age" type='text' value="<?= $_GET["age"] ?>"> years<br>
Sex: <input type="radio" name="sex" value="m" id="male" <?= ($_GET["sex"] == "m" ? "checked" : "") ?>><label for="male">male</label> <input type="radio" name="sex" value="f" id="female" <?= ($_GET["sex"] == "f" ? "checked" : "") ?>><label for="female">female</label><br>
Height: <select name="feet"><? for ($x = 4; $x <= 6; $x++) { ?><option value=<?= $x ?> <?= ($_GET["feet"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> ft <select name="inches"><? for ($x = 0; $x <= 12; $x++) { ?><option value=<?= $x ?> <?= ($_GET["inches"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> in<br>
</fieldset>

<?
if ($_GET["sex"] == "m") {
	$bmr = 66 + 6.23 * $_GET["weight"] + 12.7 * ($_GET["feet"] * 12 + $_GET["inches"]) - 6.76 * $_GET["age"];
} elseif ($_GET["sex"] == "f") {
	$bmr = 655 + 4.35 * $_GET["weight"] + 4.7 * ($_GET["feet"] * 12 + $_GET["inches"]) - 4.7 * $_GET["age"];
}
$exp = $bmr * 1.2;
?>

<fieldset>
<legend>Lifestyle (BMR=<?= round($bmr) ?>)</legend>
<input type="radio" name="lifestyle" value="sedentary" id="sedentary" checked><label for="sedentary">Sedentary: <?= round($exp) ?> cal/day</label><br>
</fieldset>

<table>
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
	<tr>
	<td align="right"><?= date("D", strtotime($x . " day")) ?></td>
	<td><?= date("M j", strtotime($x . " day")) ?></td>
	<td><input name="day<?= $x ?>" type="text" size="4" value="<?= $_GET["day".$x] ?>"></td>
	<td>-</td>
	<td><input name="exercise<?= $x ?>" type="text" size="4" value="<?= $_GET["exercise".$x] ?>"></td>
	<td>=</td>
	<td><input name="net<?= $x ?>" type="text" size="4" value="<?= $_GET["day".$x] - $_GET["exercise".$x] ?>"></td>
	<?
	if ($_GET["day".$x] == "")
		$loss = 0;
	else
		$loss = $_GET["day".$x] - $exp - $_GET["exercise".$x];
	$cumulative -= $loss;
	?>
	<td><?= round($loss / 3500, 2) ?></td>
	<td><?= round($cumulative / 3500, 2) ?></td>
	<td><?= round($_GET["weight"] - $cumulative / 3500, 1) ?></td>
	</tr>
<? } ?>
</tbody>
</table>

<input type="submit">
</form>
</body>
</html>
