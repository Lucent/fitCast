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

tbody th			{ font-weight: normal; text-align: right; }
tbody input			{ font-size: inherit; }
tbody th, tbody input		{ padding: 0.2em; margin: 0.2em; }

.Sun > *			{ padding-top: 1em; }
.Today				{ border-bottom: thick solid red; }
tr					{ color: gray; }
.Today ~ tr			{ color: black; }
</style>
<script src="https://d3js.org/d3-array.v1.min.js"></script>
<script src="https://d3js.org/d3-scale.v2.min.js"></script>
<script src="https://d3js.org/d3-color.v1.min.js"></script>
<script src="https://d3js.org/d3-interpolate.v1.min.js"></script>
<script>
var colorize_inputs = function() {
	const inputs = document.querySelectorAll("tbody input");
	const range = ["blue", "white", "red"];
	const domain = [0, BMR, BMR * 2];
	const interpolator = d3.scaleLinear().domain(domain).range(range).interpolate(d3.interpolateLab);
	for (const input of inputs) {
		if (input.value)
			input.style.backgroundColor = interpolator(input.value);
	}

	append_running_total();
}

var append_running_total = function() {
	const rows = document.querySelectorAll("tbody tr");
	let running_total = 0;
	for (const row of rows) {
		let intake = row.querySelector("input").value * 1;
		if (intake === 0)
			intake = BMR;
		running_total += BMR - intake;
		row.insertCell(-1);
		const output = document.createElement("output");
		output.appendChild(document.createTextNode(running_total));
		row.appendChild(output);
	}
}

onload = colorize_inputs;
</script>
</head>
<body>
<h1>fitCast</h1>
<h2>Forecasting your fitness with more precision than a jeweler's scale.</h2>
<nav>
 <a href="faq.html">Questions</a>
 <a href="//foodpicker.fitcast.com/">Food picker</a>
</nav>

<?php
if (!isset($_SESSION["id"])) {
	echo "Not <a href='login.php'>logged in</a>. Can't save or restore data.";
} else {
	$bmr = $_SESSION["bmr"];
}
?>
<script>const BMR = <?= $_SESSION["bmr"] ?>;</script>
<form method="post" action="server/bmr_set.php">
 <input name="bmr" type="number" value="<?= $bmr ?>"><input type="submit">
</form>
<?php
if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
//	$db_data = fetch_calories($_SESSION["id"]);
//	$first_measured = $db_data["first_measured"];

	draw_table_chart();
} else
	echo "Not logged in";
?>

</body>
</html>
