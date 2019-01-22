<?php
include "server/calorie_data.php";
?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<style>
table				{ border-collapse: collapse; }
body				{ font-family: sans-serif; }

tbody				{ font-size: large; }
tbody th			{ font-weight: normal; text-align: right; }
tbody input			{ font-size: inherit; border: none; border-bottom: thin solid #999; padding: 0.1em 0.2em; width: 4em; line-height: 1.8; }
tbody th, tbody input,
tbody td			{ text-align: right; }

.Sun > *			{ padding-top: 1em; }
.Today				{ border-bottom: thick solid red; }
tbody tr			{ color: gray; }
.Today ~ tr			{ color: black; }
</style>
<script src="https://d3js.org/d3-array.v1.min.js"></script>
<script src="https://d3js.org/d3-scale.v2.min.js"></script>
<script src="https://d3js.org/d3-color.v1.min.js"></script>
<script src="https://d3js.org/d3-interpolate.v1.min.js"></script>
<script>
window.onload = bootstrap;
function bootstrap() {
	append_running_total();
	colorize_inputs();
	watch_changes();
}

var colorize_inputs = function() {
	const range = ["green", "white", "red"];

	let domain = [BMR/2 * 1, BMR/2 * 2, BMR/2 * 3];
	const daily_interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	const inputs = document.querySelectorAll("tbody input");
	for (const input of inputs) {
		if (input.value)
			input.style.backgroundColor = daily_interpolator(input.value);
	}

	const POUND = 3500;
	domain = [-1 * POUND, 0, 1 * POUND];
	const cumulative_interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	const outputs = document.querySelectorAll("tbody output");
	for (const output of outputs) {
		output.parentNode.style.backgroundColor = cumulative_interpolator(output.textContent * 1);
	}
}

var append_running_total = function() {
	const rows = document.querySelectorAll("tbody tr");
	let running_total = 0;
	for (const row of rows) {
		let intake = row.querySelector("input").value * 1;
		if (intake === 0)
			intake = BMR;
		running_total += intake - BMR;
		row.cells[2].querySelector("output").textContent = running_total;
		row.cells[3].querySelector("output").textContent = (running_total / 3500).toFixed(1);
	}
}

function watch_changes() {
	const inputs = document.querySelectorAll("tbody input");
	for (const input of inputs) {
		input.onchange = append_running_total;
	}
}
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
