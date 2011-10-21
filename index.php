<!doctype html>
<html>
<head>
<style>span	{ display: inline-block; width: 8em; }</style>
</head>
<body>
<form method="get">
RMR: <input name="rmr" type='text' size='4' value="<?= $_GET["rmr"] ?>"><br>
Start: <input name="start" type='text' size='4' value="<?= $_GET["start"] ?>"><br>
<?
$cumulative = 0;
for ($x = -20; $x < 0; $x++) { ?>
	<span><?= date("D M j", strtotime($x . " day")) ?></span>
	<input name="day<?= $x ?>" type="text" size="4" value="<?= $_GET["day".$x] ?>">
	<?
	if ($_GET["day".$x] == "")
		$loss = 0;
	else
		$loss = $_GET["rmr"] - $_GET["day".$x];
	$cumulative += $loss;
	?>
	<span>Today's: <?= round($loss / 3500, 2) ?></span>
	<span>Cumulative: <?= round($cumulative / 3500, 2) ?></span>
	<span>Predicted: <?= round($_GET["start"] - $cumulative / 3500, 2) ?></span>
	<br>
<? } ?>

<input type="submit">
</form>
</body>
</html>
