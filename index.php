<? include "Script/functions.php"; ?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<style>
body				{ font-family: sans-serif; }
h1					{ font-family: Trebuchet MS, Verdana, sans-serif; font-weight: normal; }
h2					{ font-size: medium; }

h3					{ margin: 1ex 0 0; }
.Month				{ margin-left: <?= $leftmargin ?>px; width: <?= ($days + 1) * $blocksize ?>px; border-style: hidden; }
.Month td			{ border: 1px solid black; border: 1px solid black; text-align: center; }
h3 span				{ font-size: xx-large; line-height: 0.7; }
.First				{ float: left; }
.Last				{ float: right; }

table				{ border-collapse: collapse; }
fieldset			{ display: inline-block; padding: 1ex; border: medium solid green; -ms-border-radius: 1ex; }
fieldset *			{ color: green; }
legend				{ font-weight: bold; }
form				{ display: inline; }
th:first-child		{ padding-right: 10px; text-align: right; letter-spacing: -1px; width: <?= $leftmargin - 10 ?>px; }

input[type=text]	{ width: 35px; margin: 0; font-size: 100%; }
#Table				{ max-height: 10em; overflow: scroll; }
#Table td			{ width: <?= $blocksize ?>px; padding: 0.8ex 0; text-align: center; font-size: 15px; }
#Table .Selected	{ border: 1px solid green; width: <?= $blocksize - 2 ?>px; }
#Chart				{ height: <?= $blocksize * $verticalblocks + 10 + 10 ?>px; }
#Table, #Chart		{ width: <?= ($days + 1) * $blocksize + $leftmargin ?>px; }
.Date				{ text-align: center; }
.Spacer th			{ padding-top: 1em; padding-bottom: 0.5ex; text-align: left; }
.NewWeek			{ border-left: 1px solid #CCC; width: <?= $blocksize - 1 ?>px ! important; }
.Date .NewWeek		{ border-color: black; }
.Actual th			{ color: <?= $actualColor ?>; }
.Measured th		{ color: <?= $measuredColor ?>; }

tr					{ border-bottom: thin solid #CCC; }
tr.Date				{ border-bottom: none; }
</style>
<script>
var startday = <?= $date_start_int ?>;
var days = <?= $days ?>, blocksize = <?= $blocksize ?>, leftmargin = <?= $leftmargin ?>, verticalblocks = <?= $verticalblocks ?>;
var actualColor = "<?= $actualColor ?>", measuredColor = "<?= $measuredColor ?>";
<? output_json_table($date_start_int, $days, $weight, $cumulative, $measured); ?>
</script>
<script src="Script/interactivity.js"></script>
</head>
<body>
<nav style="float: right;"><a href="faq.html">Questions</a> <a href="/?weight=200&age=29&sex=m&feet=6&inches=0&lifestyle=1.2&food0=2000&food1=1200&food2=1800&food3=1500&food4=1600&food5=2120&food6=1000&food7=1500&food8=3000&food9=3000&food10=2000&exercise0=&exercise1=&exercise2=&exercise3=&exercise4=&exercise5=&exercise6=&exercise7=&exercise8=&exercise9=&exercise10=&measured0=&measured1=&measured2=&measured3=&measured4=&measured5=&measured6=&measured7=&measured8=&measured9=&measured10=&start=2012-05-10&end=2012-05-20">Load sample data</a></nav>
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

<table class="Month" cellpadding="0" cellspacing="0" border="0">
 <tr>
<? foreach ($months as $month => $start) { ?>
  <td width="<?= $start * $blocksize ?>">
   <h3>
<? if (array_shift(array_keys($months)) == $month) { ?>
<span class="First">⇦</span>
<? } ?>
    <?= $month ?>
<? if (array_pop(array_keys($months)) == $month) { ?>
<span class="Last">⇨</span>
<? } ?>
   </h3>
  </td>
<? } ?>
 </tr>
</table>

<div id="Chart"></div>

<table id="Table" cellpadding="0" cellspacing="0" border="0">
<tbody>

<tr class="Date">
 <th></th>
<? for ($day = 0; $day <= $days; $day++) {
$date_start_ref = clone $date_start; ?>
 <td<?= new_week($day, $date_start) ?>><?= $date_start_ref->add(new DateInterval("P".$day."D"))->format("D<\b\\r>jS") ?></td>
<? } ?>
</tr>

<tr class="Spacer"><td></td><th colspan="14">Calories</th></tr>

<tr class="Food">
 <th>Food</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?>><input name="food<?= $day ?>" type="text" size="4" value="<?= $food[$day] ?>"></td>
<? } ?>
</tr>

<tr class="Exercise">
 <th>Exercise</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?>><input name="exercise<?= $day ?>" type="text" size="4" value="<?= $exercise[$day] ?>"></td>
<? } ?>
</tr>

<tr class="Net">
 <th>Net</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?> id="net<?= $day ?>"><?= $net[$day] ?></td>
<? } ?>
</tr>

<tr class="Spacer"><td></td><th colspan="14">Weight (lbs)</th></tr>

<tr class="Change">
 <th>Change</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?>><?= sprintf("%.2f", round($loss[$day] / 3500, 2)) ?></td>
<? } ?>
</tr>

<tr class="Cumulative">
 <th>Cumulative</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?>><?= sprintf("%.1f", round($cumulative[$day] / 3500, 1)) ?></td>
<? } ?>
</tr>

<tr class="Actual">
 <th>Actual</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?>><?= sprintf("%.1f", round($weight + $cumulative[$day] / 3500, 1)) ?></td>
<? } ?>
</tr>

<tr class="Measured">
 <th>Measured</th>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <td<?= new_week($day, $date_start) ?>><input name="measured<?= $day ?>" type="text" size="4" value="<?= $measured[$day] ?>"></td>
<? } ?>
</tr>

<input type="hidden" name="start" value="<?= $_GET["start"] ?>">
<input type="hidden" name="end" value="<?= $_GET["end"] ?>">

</tbody>
</table>
<input type="submit">
</form>

</body>
</html>
