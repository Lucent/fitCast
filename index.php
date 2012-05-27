<? include "Script/calculations.php"; ?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<style>
body				{ font-family: sans-serif; }
h1					{ font-family: Trebuchet MS, Verdana, sans-serif; font-weight: normal; }
h2					{ font-size: medium; }
h3					{ margin: 1ex 0 1ex 80px; width: 700px; text-align: center; display: block; }
h3 span				{ margin: 0 1em; font-size: x-large; }
table				{ border-collapse: collapse; }
fieldset			{ display: inline-block; padding: 1ex; border: medium solid green; -ms-border-radius: 1ex; }
fieldset *			{ color: green; }
legend				{ font-weight: bold; }
form				{ display: inline; }
th					{ padding-right: 10px; text-align: right; letter-spacing: -1px; width: 70px; }

input[type=text]	{ width: 35px; margin: 0; font-size: 100%; }
#Table				{ max-height: 10em; overflow: scroll; }
#Table td			{ width: 50px; padding: 0.8ex 0; text-align: center; font-size: 15px; }
#Table .Selected	{ border: 1px solid green; width: 48px; }
#Chart				{ height: 220px; width: 780px; }
.Date				{ text-align: center; }
.NewWeek			{ border-left: thin solid #CCC; width: 49px ! important; }

tr					{ border-bottom: thin solid #CCC; }
</style>
<script>
var days = <?= $days ?>;
<? output_json_table($days, $weight, $cumulative, $measured); ?>
</script>
<script src="Script/interactivity.js"></script>
</head>
<body>
<nav style="float: right;"><a href="faq.html">Questions</a> <a href="/?weight=200&age=29&sex=m&feet=6&inches=0&lifestyle=1.2&day-14=3000&day-13=1500&day-12=2500&day-11=1900&day-10=1700&day-9=1600&day-8=5&day-7=1900&day-6=5&day-5=5&day-4=5&day-3=5&day-2=5&day-1=5&exercise-14=&exercise-13=&exercise-12=&exercise-11=&exercise-10=&exercise-9=&exercise-8=1200&exercise-7=&exercise-6=2000&exercise-5=2000&exercise-4=2000&exercise-3=4000&exercise-2=3000&exercise-1=4000&measured-14=&measured-13=199&measured-12=&measured-11=&measured-10=&measured-9=&measured-8=198&measured-7=&measured-6=&measured-5=&measured-4=&measured-3=197&measured-2=&measured-1=">Load sample data</a></nav>
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

<h3><span>⇦</span> <?= date("F Y") ?> <span>⇨</span></h2>

<div id="Chart"></div>

<table id="Table" cellpadding="0" cellspacing="0" border="0">
<tbody>

<tr class="Date">
 <th>Date</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><?= date("D", strtotime($x . " day")) ?><br><?= date("jS", strtotime($x . " day")) ?></td>
<? } ?>
</tr>

<tr class="Food">
 <th>Food</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><input name="day<?= $x ?>" type="text" size="4" value="<?= $day[$x] ?>"></td>
<? } ?>
</tr>

<tr class="Exercise">
 <th>Exercise</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><input name="exercise<?= $x ?>" type="text" size="4" value="<?= $exercise[$x] ?>"></td>
<? } ?>
</tr>

<tr class="Net">
 <th>Net</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?> id="net<?= $x ?>"><?= $net[$x] ?></td>
<? } ?>
</tr>

<tr class="Today">
 <th>Today</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><?= sprintf("%.2f", round($loss[$x] / 3500, 2)) ?></td>
<? } ?>
</tr>

<tr class="Change">
 <th>Change</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><?= sprintf("%.1f", round($cumulative[$x] / 3500, 1)) ?></td>
<? } ?>
</tr>

<tr class="Actual">
 <th>Actual</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><?= sprintf("%.1f", round($weight + $cumulative[$x] / 3500, 1)) ?></td>
<? } ?>
</tr>

<tr class="Measured">
 <th>Measured</th>
<? for ($x = -$days; $x < 0; $x++) { ?>
 <td<?= new_week($x) ?>><input name="measured<?= $x ?>" type="text" size="4" value="<?= $measured[$x] ?>"></td>
<? } ?>
</tr>


</tbody>
</table>
<input type="submit">
</form>

</body>
</html>
