<?php
session_start();

// Layout options
$blocksize = 60; $leftmargin = 90; $verticalblocks = 4;
$actualColor = "#3366CC";
$measuredColor = "#DC3912";

function calculate_daily_changes($net, $measured, $metabolism, $first_measured) {
	$change = array();
	$actual = array();

	$actual[$first_measured->format("Y-m-d")] = (float) $measured[$first_measured->format("Y-m-d")];
	for ($day = add_days($first_measured, 1); $day <= new DateTime(array_pop(array_keys($net))); $day = add_days($day, 1)) {
		$today = $day->format("Y-m-d");
		$yesterday = sub_days($day, 1)->format("Y-m-d");
		$bmr = expenditure($metabolism["sex"], $actual[$yesterday], $metabolism["height"], $metabolism["age"], $metabolism["lifestyle"]);

		if ($net[$today] == 0)
			$change[$today] = 0;
		else
			$change[$today] = ($net[$today] - $bmr) / 3500;
		$actual[$today] = $actual[$yesterday] + $change[$today];
	}

	return array("actual" => $actual, "change" => $change);
}

function get_date_range($start, $end) {
	$range = array();
	if (!isset($start)) {
		$range["start"] = new DateTime();
		$range["start"]->sub(new DateInterval("P10D"));
	} else {
		$range["start"] = new DateTime($start);
	}
	if (!isset($end)) {
		$range["end"] = new DateTime();
	} else {
		$range["end"] = new DateTime($end);
	}
	$range["days"] = date_diff($range["start"], $range["end"])->format("%a");
	return $range;
}

function add_days($date, $days) {
	$temp = clone $date;
	$temp->add(new DateInterval("P".$days."D"));
	return $temp;
}
function sub_days($date, $days) {
	$temp = clone $date;
	$temp->sub(new DateInterval("P".$days."D"));
	return $temp;
}

function find_distinct_months($range) {
	$months = array();
	for ($day = 0; $day <= $range["days"]; $day++) {
		$month = add_days($range["start"], $day)->format("F Y");
		if (isset($months[$month]))
			$months[$month]++;
		else
			$months[$month] = 1;
	}
	return $months;
}

function calculate_net($start, $food, $exercise) {
	$net = array();
	for ($day = 0; $day <= count($food); $day++) {
		$YMD = add_days($start, $day)->format("Y-m-d");
		$net[$YMD] = $food[$YMD] - $exercise[$YMD];
	}
	return $net;
}

function expenditure($sex, $weight, $height, $age, $lifestyle) {
	switch ($sex) {
		case "male":
			return (66 + 6.23 * $weight + 12.7 * $height - 6.76 * $age) * $lifestyle;
		case "female":
			return (655 + 4.35 * $weight + 4.7 * $height - 4.7 * $age) * $lifestyle;
	}
}

function set_session_vars($userdata) {
	session_regenerate_id(TRUE);
	$_SESSION["valid"] = 1;
	$_SESSION["username"] = $userdata["username"];
	$_SESSION["id"] = $userdata["id"];
}

function login($username, $password) {
	$conn = database_connect();
	$username = mysqli_real_escape_string($conn, $username);
	$query = "SELECT id, username, password, salt FROM users WHERE username = '$username' OR email = '$username';";
	$result = mysqli_query($conn, $query);
	if (mysqli_num_rows($result) < 1) {
		return "NOUSER";
	}
	$userdata = mysqli_fetch_array($result, MYSQL_ASSOC);
	$hash = hash("sha256", $userdata["salt"] . hash("sha256", $password));
	if ($hash != $userdata["password"]) {
		return "BADPASS";
	}
	mysqli_close($conn);
	return $userdata;
}

function register($username, $password, $email = "") {
	if ($username == "" || $password == "") return FALSE;
	$hash = hash("sha256", $password);
	$salt = createSalt();
	$hash = hash("sha256", $salt . $hash);

	$conn = database_connect();
	$user = mysqli_real_escape_string($user);
	$query = "INSERT INTO users (username, password, salt) VALUES ('$username', '$hash', '$salt');";
	mysqli_query($conn, $query);
	echo $conn->error;
	mysqli_close($conn);
}

function get_metabolism($id) {
	$conn = database_connect();
	$query = "SELECT * FROM metabolism WHERE id = $id;";
	$result = mysqli_query($conn, $query);
	echo $conn->error;
	if (mysqli_num_rows($result) < 1) {
		return FALSE;
	}
	$userData = mysqli_fetch_array($result, MYSQL_ASSOC);
	mysqli_close($conn);
	return $userData;
}

function database_connect() {
	$dbhost = "localhost";
	$dbname = "weightcast";
	$dbuser = "weightcast";
	$dbpass = "looseint";
	$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
	$conn->set_charset("utf8");
	mysqli_select_db($conn, $dbname);
	return $conn;
}

function createSalt() {
	$string = md5(uniqid(rand(), true));
	return substr($string, 0, 3);
}

function output_json_table($range, $metabolism, $actual, $measured) {
	$date_start_int = $range["start"]->format("j");
	$table = array();
	$table[] = array("Date", "Actual", "Measured");
	for ($day = 0; $day <= $range["days"]; $day++) {
		$YMD = add_days($range["start"], $day)->format("Y-m-d");

		$table[] = array(
			$date_start_int + $day + 0.5,
			$actual[$YMD],
			isset($measured[$YMD]) ? (float) $measured[$YMD] : null
		);
	}
	echo "var data = ", json_encode($table), ";";
}

function fetch_calories($id) {
	$first_measured = FALSE;
	$conn = database_connect();
	$query = "SELECT date, food, exercise, measured FROM calories WHERE id=$id ORDER BY date;";
	$result = mysqli_query($conn, $query);

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$food[$row["date"]] = $row["food"];
		$exercise[$row["date"]] = $row["exercise"];
		if ($first_measured === FALSE && $row["measured"] != "")
			$first_measured = new DateTime($row["date"]);
		$measured[$row["date"]] = $row["measured"];
	}

	mysqli_close($conn);
	return array("food" => $food, "exercise" => $exercise, "measured" => $measured, "first_measured" => $first_measured);
}

function new_week($x, $start) {
	if (add_days($start, $x)->format("w") == 0)
		return ' class="NewWeek"';
	else
		return '';
}

function draw_login_register($legend, $username, $password, $submittype) {
	echo "<form method='post'>\n";
	echo " <fieldset>\n";
	echo "  <legend>$legend</legend>\n";
	if ($legend == "Register") {
		echo "  <label for='Username'>Username</label> <input id='Username' type='text' name='username' value='$username'>\n";
		echo "  <label for='E-mail'>E-mail (optional)</label> <input id='E-mail' type='email' name='email' value=''>\n";
	} else {
		echo "  <label for='Username'>Username or E-mail</label> <input id='Username' type='text' name='username' value='$username'>\n";
	}
	echo "  <label for='Password'>Password</label> <input id='Password' type='password' name='password' value='$password'>\n";
	if ($legend == "Register") {
		echo "  <label for='Password2'>Re-type Password</label> <input id='Password2' type='password' name='password2' value=''>\n";
	}
	foreach ($submittype as $button)
		echo "  <input type='submit' name='Submit' value='$button'>\n";
	echo " </fieldset>\n";
	echo "</form>\n";
}

function draw_months_row($range) {
	$months = find_distinct_months($range);
	echo "<tr class='Month'>\n";
	echo " <td></td>\n";
	foreach ($months as $month => $start) {
		echo " <th colspan='$start'>\n";
		if (array_shift(array_keys($months)) == $month)
			echo "  <span class='First'><a href='/?start=" . sub_days($range["start"], $range["days"])->format("Y-m-d") . "&end=" . sub_days($range["end"], $range["days"])->format("Y-m-d") . "'>⇦</a></span>\n";
		if ($start > 3)
			echo "  <h3>$month</h3>\n";
		if (array_pop(array_keys($months)) == $month)
			echo "  <span class='Last'><a href='/?start=" . add_days($range["start"], $range["days"])->format("Y-m-d") . "&end=" . add_days($range["end"], $range["days"])->format("Y-m-d") . "'>⇨</a></span>\n";
		echo " </th>\n";
	}
	echo "</tr>\n";
}

function draw_date_row($label, $range) {
	echo "<tr class='$label'>\n";
	echo " <td></td>\n";
	for ($day = 0; $day <= $range["days"]; $day++)
		echo " <th" . new_week($day, $range["start"]) . ">" . add_days($range["start"], $day)->format("D<\b\\r>j") . "</th>\n";
	echo "</tr>\n";
}

function draw_input_row($label, $vals, $range) {
	echo "<tr class='$label' id='$label'>\n";
	echo " <th>$label</th>\n";
	for ($day = 0; $day <= $range["days"]; $day++) {
		$YMD = add_days($range["start"], $day)->format("Y-m-d");
		echo " <td><input name='$label:$YMD' type='text' size='4' value='{$vals[$YMD]}'></td>\n";
	}
	echo "</tr>\n";
}

function draw_number_row($label, $vals, $range, $rounding, $pattern = "%d", $exact = FALSE) {
	echo "<tr class='$label' id='$label'>\n";
	echo " <th>$label</th>\n";
	for ($day = 0; $day <= $range["days"]; $day++) {
		$YMD = add_days($range["start"], $day)->format("Y-m-d");
		echo " <td" . ($exact ? " noround='{$vals[$YMD]}'" : "") . ">" . sprintf($pattern, round($vals[$YMD], $rounding)) . "</td>\n";
	}
	echo "</tr>\n";
}

function draw_table_chart($db_data, $net, $change, $actual, $range) {
	echo "<form method='post' action='Script/storevalues.php'>\n";
	echo "<table id='Table' cellpadding='0' cellspacing='0' border='0'>\n";
	echo "<tbody>\n";
	draw_months_row($range);
	draw_date_row("Date", $range);
	draw_input_row("Food", $db_data["food"], $range);
	draw_input_row("Exercise", $db_data["exercise"], $range);
	draw_number_row("Net", $net, $range, 0);
	echo "<tr><td colspan='" . ($range["days"] + 2) . "' class='Chart'><div id='Chart'></div></td></tr>\n";
	draw_number_row("Change", $change, $range, 2, "%.2f");
	draw_number_row("Actual", $actual, $range, 1, "%.1f", TRUE);
	draw_input_row("Measured", $db_data["measured"], $range);
	echo "</tbody>\n";
	echo "</table>\n";
	echo "<input type='submit'>\n";
	echo "</form>\n";
}

?>
