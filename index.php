<?php
include "server/calorie_data.php";
?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
table				{ border-collapse: collapse; }
:root				{ color-scheme: light dark; }
body				{ font-family: sans-serif; color: CanvasText; background-color: Canvas; }

tbody				{ font-size: large; }
tbody th			{ font-weight: normal; text-align: right; }
tbody input			{ font-size: inherit; border: none; border-bottom: thin solid #999; padding: 0.1em 0.2em; width: 4em; line-height: 1.8; }
tbody th, tbody input,
tbody td			{ text-align: right; }

.Sun > *			{ padding-top: 1em; }
.Today				{ border-bottom: thick solid red; }
tbody tr			{ color: gray; }
.Today ~ tr			{ color: revert; }
#LineChart			{ width: 10em; }

.Black				{ color: black; }
.White				{ color: white; }
</style>
<script src="https://d3js.org/d3.v7.min.js"></script>
<script type="module" src="js/load.js">
</script>
</head>
<body>
<h1>fitCast</h1>
<h2>Forecast your weight with the precision of a jeweler's scale.</h2>
<?php
if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	if (array_key_exists("bmr", $_SESSION)) {
		$bmr = $_SESSION["bmr"];
		echo "<script>const BMR = {$_SESSION["bmr"]};</script>";
	}
} else {
	echo "<p>Please <a href='login.php'>log in or register</a> to save your data.</p>";
}
?>
<form method="post" action="server/bmr_set.php">
 <input name="bmr" type="number" value="<?= isset($bmr) ? $bmr : 2000 ?>">
 <input type="submit" value="Save BMR">
</form>

<div id="LineChart"></div>

<?php
if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1)
	draw_table_chart(-3, 200);
?>
<nav>
 <a href="faq.html">Questions</a>
 <a href="//foodpicker.fitcast.com/">Food picker</a>
</nav>

</body>
</html>
