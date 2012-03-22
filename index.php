<!doctype html>
<html>
<head>
<style>
body				{ font-family: sans-serif; }
h1					{ font-family: Trebuchet MS, Verdana, sans-serif; font-weight: normal; }
h2					{ font-size: medium; }
fieldset			{ display: inline-block; }
table				{ border-collapse: collapse; }
fieldset			{ padding: 1ex; border: medium solid green; -ms-border-radius: 1ex; }
fieldset *			{ color: green; }
legend				{ font-weight: bold; }
form				{ display: inline; }
th					{ padding: 1ex 0.5ex; letter-spacing: -1px; }
th span				{ display: block; font-weight: normal; }
input[type=text]	{ width: 5ex; font-size: medium; }
#PredictedWeight	{ float: right; }
#Table				{ max-height: 10em; overflow: scroll; }
.odd				{ ackground-color: #EEE; }
.NewWeek			{ border-top: thin solid black; }

tr					{ border-bottom: thin solid #CCC; }
.Day				{ line-height: 2.5; padding: 0 0.8ex; text-align: right; }
.Date				{ background: -webkit-linear-gradient(left, #FFF 0%, #EEE 75%, #DDD 100%); padding-right: 1em; }
.Food, .Exercise	{ padding: 0 1ex; }
.Minus, .Equals		{ font-weight: bold; }
.Net				{ text-align: right; padding-right: 1em; }
.Today				{ text-align: right; padding: 0 1ex; }
.Change				{ text-align: right; padding: 0 1ex; }
.Weight				{ text-align: right; padding: 0 1ex; }


</style>
<script src="Script/weightcast.js"></script>
</head>
<body>
<nav style="float: right;"><a href="faq.html">Questions</a> <a href="/?weight=200&age=29&sex=m&feet=6&inches=0&lifestyle=1.2&day-20=3000&exercise-20=&day-19=6000&exercise-19=&day-18=2000&exercise-18=&day-17=1800&exercise-17=&day-16=2000&exercise-16=&day-15=1700&exercise-15=&day-14=3000&exercise-14=&day-13=1500&exercise-13=&day-12=2500&exercise-12=&day-11=1900&exercise-11=&day-10=1700&exercise-10=&day-9=1600&exercise-9=&day-8=5&exercise-8=1200&day-7=1900&exercise-7=&day-6=5&exercise-6=2000&day-5=5&exercise-5=2000&day-4=5&exercise-4=2000&day-3=5&exercise-3=4000&day-2=5&exercise-2=3000&day-1=5&exercise-1=4000">Load sample data</a></nav>
<h1>WeightCast</h1>
<h2>Forecasting your weight with more precision than a jeweler's scale.</h2>

<form name="login" action="login.php" method="post">
Username: <input type="text" name="username"><br>
Password: <input type="password" name="password"><br>
<input type="submit" value="Login">
</form>

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
		return 66 + 6.23 * $weight + 12.7 * $height - 6.76 * $age;
	elseif ($sex == "f")
		return 655 + 4.35 * $weight + 4.7 * $height - 4.7 * $age;
}
$bmr = expenditure($_GET["sex"], $_GET["weight"], $_GET["feet"] * 12 + $_GET["inches"], $_GET["age"]);
?>

<fieldset>
<legend>Lifestyle (BMR=<?= round($bmr) ?>)</legend>
<input type="radio" name="lifestyle" value="1.2" id="sedentary" checked><label for="sedentary">Sedentary: <?= round($bmr * $_GET["lifestyle"]) ?> cal/day</label><br>
</fieldset>

<br>
<div id="PredictedWeight"></div>

<table id="Table" cellpadding="0" cellspacing="0" border="0">
<thead>
<tr>
 <th colspan="2">Date</th>
 <th>Food <span>(cal)</span></th>
 <th colspan="2">Exercise <span>(cal)</span></th>
 <th></th>
 <th>Net <span>(cal)</span></th>
 <th>Today <span>(lbs)</span></th>
 <th>Change <span>(lbs)</span></th>
 <th>Weight <span>(lbs)</span></th>
</tr>
</thead>
<tbody>
<?
$cumulative = 0;
for ($x = -20; $x < 0; $x++) { ?>
<tr class="<?= $x % 2 ? "even" : "odd" ?> <?= date("D", strtotime($x . " day")) == "Sun" ? "NewWeek" : "" ?>">
	<td class="Day"><?= date("D", strtotime($x . " day")) ?></td>
	<td class="Date"><?= date("M j", strtotime($x . " day")) ?></td>
	<td class="Food"><input name="day<?= $x ?>" type="text" size="4" value="<?= $_GET["day".$x] ?>"></td>
	<td class="Minus">-</td>
	<td class="Exercise"><input name="exercise<?= $x ?>" type="text" size="4" value="<?= $_GET["exercise".$x] ?>"></td>
	<td class="Equals">=</td>
	<td class="Net" id="net<?= $x ?>"><?= $_GET["day".$x] - $_GET["exercise".$x] ?></td>
<?
	if ($_GET["day".$x] == "")
		$loss = 0;
	else
		$loss = $_GET["day".$x] - expenditure($_GET["sex"], $_GET["weight"] + $cumulative / 3500, $_GET["feet"] * 12 + $_GET["inches"], $_GET["age"]) * $_GET["lifestyle"] - $_GET["exercise".$x];
	$cumulative += $loss;
	?>
	<td class="Today"><?= sprintf("%.2f", round($loss / 3500, 2)) ?></td>
	<td class="Change"><?= sprintf("%.1f", round($cumulative / 3500, 1)) ?></td>
	<td class="Weight"><?= round($_GET["weight"] + $cumulative / 3500, 1) ?></td>
	<script>data.push([new Date(<?= date("Y", strtotime($x." day")) ?>, <?= date("n", strtotime($x." day")) - 1 ?>, <?= date("j", strtotime($x." day")) ?>), <?= $_GET["weight"] + $cumulative / 3500 ?>]);</script>
</tr>
<? } ?>
</tbody>
</table>

<input type="submit">
</form>

</body>
</html>
