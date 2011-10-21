<!doctype html>
<form method="get">
RMR: <input name="rmr" type='text' size='4' value="<?= $_GET["rmr"] ?>"><br>
<?
$cumulative = 0;
for ($x = -20; $x < 0; $x++) {
	echo date("D M j", strtotime($x . " day")), "\n"; ?>
	<input name="day<?= $x ?>" type="text" size="4" value="<?= $_GET["day".$x] ?>">
	<?
	$loss = $_GET["rmr"] - $_GET["day".$x];
	$cumulative += $loss;
	?>
	Today's: <?= round($loss / 3500, 2) ?>
	Cumulative: <?= round($cumulative / 3500, 2) ?>
	<br>
<? } ?>

<input type="submit">
</form>
