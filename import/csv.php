<?php include "../Script/functions.php";

if (!empty($_POST)) {
	$conn = database_connect();
	$manufacturer = mysqli_real_escape_string($conn, $_POST["manufacturer"]);
	$table = mysqli_real_escape_string($conn, "food_" . $_POST["table"]);
	$long = mysqli_real_escape_string($conn, $_POST["csv"]);
	$suffix1 = mysqli_real_escape_string($conn, $_POST["suffix1"]);
	$suffix2 = mysqli_real_escape_string($conn, $_POST["suffix2"]);
	$suffix3 = mysqli_real_escape_string($conn, $_POST["suffix3"]);
	$final = array();

	$rows = explode('\r\n', $long);
	foreach ($rows as $line) {
		$fields = explode(", ", $line);
		if (count($fields) == 1)
			$currentproduct = $fields[0];
		else {
			if ($fields[0] !== "")
				$final[$currentproduct][] = $fields;
		}
	}

	foreach ($final as $linename => $productline) {
		foreach ($productline as $product) {
			$product = str_replace("<1", 0.5, $product);
			if ($product[1] !== "") {
				$query = "REPLACE INTO `$table` (`manufacturer`, `long`, `kcal`, `fat`, `carb`, `fiber`, `protein`) VALUES ('$manufacturer', '$linename, $product[0], $suffix1, $suffix2, $suffix3', $product[1], $product[2], $product[3], $product[4], $product[5]);";
				echo $query, "\n";
				mysqli_query($conn, $query);
				echo $conn->error;
			}
		}
	}

//	print_r($final);
	mysqli_close($conn);
}
?>
<form method="post">
<fieldset>
<legend>Import</legend>
<select name="table"><option value="fast">fast</select><br>
Manufacturer: <input name="manufacturer" type="text" value="<?= $_POST["manufacturer"] ?>"><br>
Suffix 1: <select name="suffix1">
<option value="Tall (12 oz)">Tall (12 oz)
<option value="Grande (16 oz)">Grande (16 oz)
<option value="Venti (20 oz)">Venti (20 oz)
<option value="Venti Iced (24 oz)">Venti Iced (24 oz)
</select><br>
Suffix 2: <select name="suffix2">
<option value="Nonfat milk">Nonfat milk
<option value="Whole milk">Whole milk
<option value="2% milk">2% milk
<option value="Soy (USA)">Soy (USA)
<option value="Soy (Canada)">Soy (Canada)
</select><br>
Suffix 3: <select name="suffix3">
<option value="No whipped cream">No whipped cream
<option value="Whipped cream">Whipped cream
</select><br>
<textarea name="csv" rows=50 cols=80><?= $_POST["csv"] ?></textarea>
</fieldset>

<input type="submit">
</form>
