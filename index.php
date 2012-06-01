<?  include "Script/functions.php"; ?>
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
#Table, #Chart		{ width: <?= ($days + 1) * $blocksize + $leftmargin ?>px; }
.Date				{ text-align: center; border-style: hidden; }
th:first-child		{ -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff',endColorstr='#eaeaea',gradientType=1)"; background: -webkit-linear-gradient(left, #FFFFFF 0%, #EAEAEA 100%); border-right: 1px solid #CACACA; }
.Date th, .Month th	{ font-weight: normal; }
.NewWeek			{ border-left: 1px solid #CACACA; width: <?= $blocksize - 1 ?>px ! important; }
.Date .NewWeek		{ border-color: black; }
.Actual th			{ color: <?= $actualColor ?>; }
.Measured th		{ color: <?= $measuredColor ?>; }

tr					{ border-bottom: 1px solid #CACACA; }
</style>
<script>
var startday = <?= $date_start->format("j"); ?>;
var days = <?= $days ?>, blocksize = <?= $blocksize ?>, leftmargin = <?= $leftmargin ?>, verticalblocks = <?= $verticalblocks ?>;
var actualColor = "<?= $actualColor ?>", measuredColor = "<?= $measuredColor ?>";
<? output_json_table($date_start, $days, $metabolism, $cumulative, $measured); ?>
</script>
<script src="Script/interactivity.js"></script>
</head>
<body>
<nav><a href="faq.html">Questions</a></nav>
<h1>fitCast</h1>
<h2>Forecasting your fitness with more precision than a jeweler's scale.</h2>

<form method="post" action="Script/storevalues.php">
<table id="Table" cellpadding="0" cellspacing="0" border="0">
<tbody>

 <tr class="Month">
  <td></td>
<? foreach ($months as $month => $start) { ?>
  <th colspan="<?= $start ?>">
<? if (array_shift(array_keys($months)) == $month) { ?>
   <span class="First"><a href="/?start=<?= sub_days($date_start, 10)->format("Y-m-d") ?>&end=<?= sub_days($date_end, 10)->format("Y-m-d") ?>">⇦</a></span>
<? }
if ($start > 3) { ?>
   <h3><?= $month ?></h3>
<? }
if (array_pop(array_keys($months)) == $month) { ?>
   <span class="Last"><a href="/?start=<?= add_days($date_start, 10)->format("Y-m-d") ?>&end=<?= add_days($date_end, 10)->format("Y-m-d") ?>">⇨</a></span>
<? } ?>
  </th>
<? } ?>
 </tr>

<tr class="Date">
 <td></td>
<? for ($day = 0; $day <= $days; $day++) { ?>
 <th<?= new_week($day, $date_start) ?>><?= add_days($date_start, $day)->format("D<\b\\r>jS") ?></th>
<? } ?>
</tr>

<tr class="Food">
 <th>Food</th>
<? for ($day = 0; $day <= $days; $day++) {
$YMD = add_days($date_start, $day)->format("Y-m-d"); ?>
 <td><input name="food:<?= $YMD ?>" type="text" size="4" value="<?= isset($food[$YMD]) ? $food[$YMD] : "" ?>"></td>
<? } ?>
</tr>

<tr class="Exercise">
 <th>Exercise</th>
<? for ($day = 0; $day <= $days; $day++) {
$YMD = add_days($date_start, $day)->format("Y-m-d"); ?>
 <td><input name="exercise:<?= $YMD ?>" type="text" size="4" value="<?= isset($exercise[$YMD]) ? $exercise[$YMD] : "" ?>"></td>
<? } ?>
</tr>

<tr class="Net">
 <th>Net</th>
<? for ($day = 0; $day <= $days; $day++) {
$YMD = add_days($date_start, $day)->format("Y-m-d"); ?>
 <td><?= round($net[$YMD]) ?></td>
<? } ?>
</tr>

<tr>
 <td colspan="<?= $days + 2 ?>" class="Chart">
  <div id="Chart"></div>
 </td>
</tr>

<tr class="Change">
 <th>Change</th>
<? for ($day = 0; $day <= $days; $day++) {
$YMD = add_days($date_start, $day)->format("Y-m-d"); ?>
 <td><?= sprintf("%.2f", round($loss[$YMD] / 3500, 2)) ?></td>
<? } ?>
</tr>

<tr class="Actual">
 <th>Actual</th>
<? for ($day = 0; $day <= $days; $day++) {
$YMD = add_days($date_start, $day)->format("Y-m-d"); ?>
 <td><?= sprintf("%.1f", round($metabolism["startweight"] + $cumulative[$YMD] / 3500, 1)) ?></td>
<? } ?>
</tr>

<tr class="Measured">
 <th>Measured</th>
<? for ($day = 0; $day <= $days; $day++) {
$YMD = add_days($date_start, $day)->format("Y-m-d"); ?>
 <td><input name="measured:<?= $YMD ?>" type="text" size="4" value="<?= isset($measured[$YMD]) ? $measured[$YMD] : "" ?>"></td>
<? } ?>
</tr>

</tbody>
</table>
<input type="submit">
</form>

</body>
</html>
