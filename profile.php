<? include "Script/functions.php"; ?>
<?
if (!empty($_POST) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$id = $_SESSION["id"];
	$age = mysqli_real_escape_string($conn, $_POST["age"]);
	$sex = mysqli_real_escape_string($conn, $_POST["sex"]);
	$height = mysqli_real_escape_string($conn, $_POST["feet"] * 12 + $_POST["inches"]);

	$query = "INSERT INTO metabolism (id, age, sex, height) VALUES ($id, '$age', '$sex', '$height');";
	mysqli_query($conn, $query);
//	echo $conn->error;
	mysqli_close($conn);
}
?>
<form method="post">
<fieldset>
<legend>Measurements</legend>
Age: <input name="age" type='text' value="<?= $_GET["age"] ?>"> years<br>
Sex: <input type="radio" name="sex" value="male" id="male" <?= ($_GET["sex"] == "m" ? "checked" : "") ?>><label for="male">male</label> <input type="radio" name="sex" value="female" id="female" <?= ($_GET["sex"] == "f" ? "checked" : "") ?>><label for="female">female</label><br>
Height: <select name="feet"><? for ($x = 4; $x <= 6; $x++) { ?><option value=<?= $x ?> <?= ($_GET["feet"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> ft <select name="inches"><? for ($x = 0; $x < 12; $x++) { ?><option value=<?= $x ?> <?= ($_GET["inches"] == $x ? "selected" : "") ?>><?= $x ?></option><? } ?></select> in<br>
</fieldset>

<fieldset>
<legend>Lifestyle (BMR=<?= round($bmr) ?>)</legend>
<input type="radio" name="lifestyle" value="1.2" id="sedentary" checked><label for="sedentary">Sedentary: <?= round($bmr * $_GET["lifestyle"]) ?> cal/day</label><br>
</fieldset>

<input type="submit">
</form>
