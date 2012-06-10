<? include "Script/functions.php"; ?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<style>
body				{ font-family: sans-serif; }
h1					{ font-family: Trebuchet MS, Verdana, sans-serif; font-weight: normal; }
h2					{ font-size: medium; }

h3					{ margin: 1ex 0; font-weight: normal; font-size: 1.5em; display: inline; }
.Month, .Month td, .Chart, #Table	{ border-style: hidden; }
.Month th			{ border: 1px solid black; border: 1px solid black; text-align: center; }
.Month span			{ font-size: xx-large; }
.First				{ float: left; }
.Last				{ float: right; }

table				{ border-collapse: collapse; }
fieldset			{ display: inline-block; padding: 1ex; border: medium solid green; -ms-border-radius: 1ex; }
fieldset *			{ color: green; }
legend				{ font-weight: bold; }
form				{ display: inline; }
th:first-child		{ padding-right: 10px; text-align: right; letter-spacing: -1px; width: <?= $leftmargin - 10 ?>px; }

input[type=text]	{ width: 42px; margin: 0; font-size: 100%; text-align: right; }
#Table				{ max-height: 10em; overflow: scroll; }
#Table td			{ width: <?= $blocksize ?>px; padding: 0.8ex 0; text-align: center; font-size: 18px; }
#Chart				{ height: <?= $blocksize * $verticalblocks + 10 + 10 ?>px; }
.Chart				{ padding: 0; }
#Table, #Chart		{ width: <?= ($range["days"] + 1) * $blocksize + $leftmargin ?>px; }
.Date				{ text-align: center; border-style: hidden; }
th:first-child		{ -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff',endColorstr='#eaeaea',gradientType=1)"; background: -webkit-linear-gradient(left, #FFFFFF 0%, #EAEAEA 100%); border-right: 1px solid #CACACA; }
.Date th, .Month th	{ font-weight: normal; }
.NewWeek			{ border-left: 1px solid #CACACA; width: <?= $blocksize - 1 ?>px ! important; }
.Date .NewWeek		{ border-color: black; }
.Actual th			{ color: <?= $actualColor ?>; }
.Measured th		{ color: <?= $measuredColor ?>; }

tr					{ border-bottom: 1px solid #CACACA; }
</style>
</head>
<body>
<h1>fitCast</h1>
<h2>Forecasting your fitness with more precision than a jeweler's scale.</h2>
<nav><a href="faq.html">Questions</a></nav>

<? if (!isset($_SESSION["id"])) {
	echo "Not <a href='login.php'>logged in</a>. Can't fetch metabolism information.";
} else {
	$metabolism = get_metabolism($_SESSION["id"]);
	if ($metabolism === FALSE) {
		echo "User profile incomplete. Cannot calculate weight. <a href='profile.php'>Enter information.</a>";
	}
}

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$db_data = fetch_calories($_SESSION["id"]);
	$food = $db_data["food"];
	$exercise = $db_data["exercise"];
	$measured = $db_data["measured"];
	$first_measured = $db_data["first_measured"];
	$net = calculate_net($range["start"], $food, $exercise);

	if ($first_measured) {
		$daily = calculate_daily_changes($net, $measured, $metabolism, $first_measured);
		$actual = $daily["actual"];
		$change = $daily["change"];
	}
}
?>
<script>
var metabolism = <?= json_encode($metabolism) ?>;
var startday = <?= $range["start"]->format("j"); ?>;
var days = <?= $range["days"] ?>, blocksize = <?= $blocksize ?>, leftmargin = <?= $leftmargin ?>, verticalblocks = <?= $verticalblocks ?>;
var actualColor = "<?= $actualColor ?>", measuredColor = "<?= $measuredColor ?>";
<? output_json_table($range, $metabolism, $actual, $measured); ?>
</script>
<script src="Script/interactivity.js"></script>
<form method="post" action="Script/storevalues.php">
<table id="Table" cellpadding="0" cellspacing="0" border="0">
<tbody>

<? draw_months_row($range); ?>

<tr class="Date">
 <td></td>
<? for ($day = 0; $day <= $range["days"]; $day++) { ?>
 <th<?= new_week($day, $range["start"]) ?>><?= add_days($range["start"], $day)->format("D<\b\\r>jS") ?></th>
<? } ?>
</tr>

<tr class="Food" id="Food">
 <th>Food</th>
<? for ($day = 0; $day <= $range["days"]; $day++) {
$YMD = add_days($range["start"], $day)->format("Y-m-d"); ?>
 <td><input name="food:<?= $YMD ?>" type="text" size="4" value="<?= $food[$YMD] ?>"></td>
<? } ?>
</tr>

<tr class="Exercise" id="Exercise">
 <th>Exercise</th>
<? for ($day = 0; $day <= $range["days"]; $day++) {
$YMD = add_days($range["start"], $day)->format("Y-m-d"); ?>
 <td><input name="exercise:<?= $YMD ?>" type="text" size="4" value="<?= $exercise[$YMD] ?>"></td>
<? } ?>
</tr>

<tr class="Net" id="Net">
 <th>Net</th>
<? for ($day = 0; $day <= $range["days"]; $day++) {
$YMD = add_days($range["start"], $day)->format("Y-m-d"); ?>
 <td><?= round($net[$YMD]) ?></td>
<? } ?>
</tr>

<tr>
 <td colspan="<?= $range["days"] + 2 ?>" class="Chart">
  <div id="Chart"></div>
 </td>
</tr>

<tr class="Change" id="Change">
 <th>Change</th>
<? for ($day = 0; $day <= $range["days"]; $day++) {
$YMD = add_days($range["start"], $day)->format("Y-m-d"); ?>
 <td><?= sprintf("%.2f", round($change[$YMD], 2)) ?></td>
<? } ?>
</tr>

<tr class="Actual" id="Actual">
 <th>Actual</th>
<? for ($day = 0; $day <= $range["days"]; $day++) {
$YMD = add_days($range["start"], $day)->format("Y-m-d"); ?>
 <td noround="<?= $actual[$YMD] ?>"><?= sprintf("%.1f", round($actual[$YMD], 1)) ?></td>
<? } ?>
</tr>

<tr class="Measured" id="Measured">
 <th>Measured</th>
<? for ($day = 0; $day <= $range["days"]; $day++) {
$YMD = add_days($range["start"], $day)->format("Y-m-d"); ?>
 <td><input name="measured:<?= $YMD ?>" type="text" size="4" value="<?= $measured[$YMD] ?>"></td>
<? } ?>
</tr>

</tbody>
</table>
<input type="submit">
</form>

</body>
</html>
