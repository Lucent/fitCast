<?php
include "server/calorie_data.php";
?>
<!doctype html>
<html>
<head>
<title>fitCast - Predict your weight using calorie counting</title>
<style>
body				{ font-family: sans-serif; }

tbody th			{ font-weight: normal; text-align: right; }
tbody input			{ font-size: inherit; }
tbody th, tbody input		{ padding: 0.2em; margin: 0.2em; }

.Sun > *			{ padding-top: 1em; }
</style>
</head>
<body>
<h1>fitCast</h1>
<h2>Forecasting your fitness with more precision than a jeweler's scale.</h2>
<nav>
 <a href="faq.html">Questions</a>
 <a href="//foodpicker.fitcast.com/">Food picker</a>
</nav>

<?php if (!isset($_SESSION["id"])) {
	echo "Not <a href='login.php'>logged in</a>. Can't fetch metabolism information.";
} else {
//	$metabolism = get_metabolism($_SESSION["id"]);
//	if ($metabolism === FALSE) {
		echo "User profile incomplete. Cannot calculate weight. <a href='profile.php'>Enter information.</a>";
//	}
}

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
//	$db_data = fetch_calories($_SESSION["id"]);
//	$first_measured = $db_data["first_measured"];
?>
<?php
	draw_table_chart();
}
?>

</body>
</html>
