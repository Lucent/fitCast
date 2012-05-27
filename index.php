<? include "Script/calculations.php"; ?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<style>
body				{ font-family: sans-serif; }
h1					{ font-family: Trebuchet MS, Verdana, sans-serif; font-weight: normal; }
h2					{ font-size: medium; }
table				{ border-collapse: collapse; }
fieldset			{ display: inline-block; padding: 1ex; border: medium solid green; -ms-border-radius: 1ex; }
fieldset *			{ color: green; }
legend				{ font-weight: bold; }
form				{ display: inline; }
th					{ padding-right: 10px; text-align: right; letter-spacing: -1px; width: 70px; }
td					{ padding: 1ex 0; }
input[type=text]	{ width: 50px; font-size: medium; margin: 0; border: none; padding: 0; }
#Table				{ max-height: 10em; overflow: scroll; }
#Chart				{ height: 220px; width: 780px; }
.NewWeek			{ border-top: thin solid black; }

tr					{ border-bottom: thin solid #CCC; }
</style>
<script>var days = <?= $days ?>;</script>
<script src="Script/interactivity.js"></script>
</head>
<body>
<nav style="float: right;"><a href="faq.html">Questions</a> <a href="/?weight=200&age=29&sex=m&feet=6&inches=0&lifestyle=1.2&day-20=3000&exercise-20=&day-19=6000&exercise-19=&day-18=2000&exercise-18=&day-17=1800&exercise-17=&day-16=2000&exercise-16=&day-15=1700&exercise-15=&day-14=3000&exercise-14=&day-13=1500&exercise-13=&day-12=2500&exercise-12=&day-11=1900&exercise-11=&day-10=1700&exercise-10=&day-9=1600&exercise-9=&day-8=5&exercise-8=1200&day-7=1900&exercise-7=&day-6=5&exercise-6=2000&day-5=5&exercise-5=2000&day-4=5&exercise-4=2000&day-3=5&exercise-3=4000&day-2=5&exercise-2=3000&day-1=5&exercise-1=4000">Load sample data</a></nav>
<h1>fitCast</h1>
<h2>Forecasting your fitness with more precision than a jeweler's scale.</h2>

<fieldset>
<legend>Log in</legend>
<form name="login" action="login.php" method="post">
Username: <input type="text" name="username"><br>
Password: <input type="password" name="password"><br>
<input type="submit" value="Login">
</form>
</fieldset>

<form method="get">

<fieldset>
<legend>Measurements</legend>
Weight: <input name="weight" type='text' value="<?= $_GET["weight"] ?>"> lbs<br>
Age: <input name="age" type='text' value="<?= $_GET["age"] ?>"> years<br>
Sex: <input type="radio" name="sex" value="m" id="male" <?= ($_GET["sex"] == "m" ? "checked" : "") ?>><label for="male">male</label> <input type="radio" name="sex" value="f" id="female" <?= ($_GET["sex"] == "f" ? "checked" : "") ?>><label for="female">female</label><br>
Height: <select name="feet"><? for ($x = 4; $x <= 6; $x++) { ?><option value=<?= $x ?> <?= ($_GET["feet"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> ft <select name="inches"><? for ($x = 0; $x < 12; $x++) { ?><option value=<?= $x ?> <?= ($_GET["inches"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> in<br>
</fieldset>

<fieldset>
<legend>Lifestyle (BMR=<?= round($bmr) ?>)</legend>
<input type="radio" name="lifestyle" value="1.2" id="sedentary" checked><label for="sedentary">Sedentary: <?= round($bmr * $_GET["lifestyle"]) ?> cal/day</label><br>
</fieldset>

<br><br>
<div id="Chart"></div>

<table id="Table" cellpadding="0" cellspacing="0" border="0">
<thead>
<!--
 <th colspan="2">Exercise <span>(cal)</span></th>
 <th></th>
 <th>Net <span>(cal)</span></th>
 <th>Today <span>(lbs)</span></th>
 <th>Change <span>(lbs)</span></th>
 <th>Weight <span>(lbs)</span></th>
-->
</thead>
<tbody>
<!-- <tr class="<?= $x % 2 ? "even" : "odd" ?> <?= date("D", strtotime($x . " day")) == "Sun" ? "NewWeek" : "" ?>"> -->

<tr class="Day">
<th>Date</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td><?= date("D", strtotime($x . " day")) ?><br><?= date("jS", strtotime($x . " day")) ?></td>
<? } ?>
</tr>

<tr class="Food">
<th>Food</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td><input name="day<?= $x ?>" type="text" size="4" value="<?= $day[$x] ?>"></td>
<? } ?>
</tr>

<tr class="Exercise">
<th>Exercise</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td><input name="exercise<?= $x ?>" type="text" size="4" value="<?= $exercise[$x] ?>"></td>
<? } ?>
</tr>

<tr class="Net">
<th>Net</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td id="net<?= $x ?>"><?= $net[$x] ?></td>
<? } ?>
</tr>

<tr class="Today">
<th>Today</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td><?= sprintf("%.2f", round($loss[$x] / 3500, 2)) ?></td>
<? } ?>
</tr>

<tr class="Change">
<th>Change</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td><?= sprintf("%.1f", round($cumulative[$x] / 3500, 1)) ?></td>
<? } ?>
</tr>

<tr class="Weight">
<th>Weight</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
	<td><?= sprintf("%.1f", round($weight + $cumulative[$x] / 3500, 1)) ?></td>
<? } ?>
</tr>

</tbody>
</table>
<? output_json_table($days, $weight, $cumulative); ?>
<input type="submit">
</form>

</body>
</html>
