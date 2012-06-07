<?
session_start();

// Layout options
$blocksize = 60; $leftmargin = 90; $verticalblocks = 4;
$actualColor = "#3366CC";
$measuredColor = "#DC3912";

// Date range displayed
if (!isset($_GET["start"])) {
	$date_start = new DateTime();
	$date_start->sub(new DateInterval("P10D"));
} else {
	$date_start = new DateTime($_GET["start"]);
}
if (!isset($_GET["end"])) {
	$date_end = new DateTime();
} else {
	$date_end = new DateTime($_GET["end"]);
}
$days = date_diff($date_start, $date_end)->format("%a");

$food = array();
$exercise = array();
$measured = array();

if (!isset($_SESSION["id"])) { ?>
Not <a href="login.php">logged in</a>. Can't fetch metabolism information.
<?} else {
	$metabolism = get_metabolism($_SESSION["id"]);
	if ($metabolism === FALSE) { ?>
	User profile incomplete. Cannot calculate weight. <a href="profile.php">Enter information.</a>
<?	}
}

$change = array();
$measured = array();
$cumulative = array();
$first_measured = FALSE;

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$query = "SELECT date, food, exercise, net, measured FROM calories WHERE id=" . $_SESSION['id'] . " ORDER BY date";
	$result = mysqli_query($conn, $query);

	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$food[$row["date"]] = $row["food"];
		$exercise[$row["date"]] = $row["exercise"];
		if ($first_measured === FALSE && $row["measured"] != "")
			$first_measured = new DateTime($row["date"]);
		$measured[$row["date"]] = $row["measured"];
	}

	mysqli_close($conn);
}

$months = find_distinct_months($date_start, $days);

$net = calculate_net($date_start, $food, $exercise);

if ($first_measured) {
	$actual[$first_measured->format("Y-m-d")] = (float) $measured[$first_measured->format("Y-m-d")];
	for ($day = add_days($first_measured, 1); $day <= new DateTime(array_pop(array_keys($food))); $day = add_days($day, 1)) {
		$today = $day->format("Y-m-d");
		$yesterday = sub_days($day, 1)->format("Y-m-d");
		$bmr = expenditure($metabolism["sex"], $actual[$yesterday], $metabolism["height"], $metabolism["age"], $metabolism["lifestyle"]);

		if ($net[$today] == 0)
			$change[$today] = 0;
		else
			$change[$today] = ($net[$today] - $bmr) / 3500;
		$actual[$today] = $actual[$yesterday] + $change[$today];
	}
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

function find_distinct_months($date_start, $days) {
	$months = array();
	for ($day = 0; $day <= $days; $day++) {
		$month = add_days($date_start, $day)->format("F Y");
		$months[$month]++;
	}
	return $months;
}

function calculate_net($date_start, $food, $exercise) {
	$net = array();
	for ($day = 0; $day <= count($food); $day++) {
		$YMD = add_days($date_start, $day)->format("Y-m-d");
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
	$query = "SELECT id, username, password, salt FROM users WHERE username = '$username';";
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
	mysqli_select_db($conn, $dbname);
	return $conn;
}

function createSalt() {
	$string = md5(uniqid(rand(), true));
	return substr($string, 0, 3);
}

function output_json_table($date_start, $days, $metabolism, $actual, $measured) {
	$date_start_int = $date_start->format("j");
	$table = array();
	$table[] = array("Date", "Actual", "Measured");
	for ($day = 0; $day <= $days; $day++) {
		$YMD = add_days($date_start, $day)->format("Y-m-d");

		$table[] = array(
			$date_start_int + $day + 0.5,
			$actual[$YMD],
			isset($measured[$YMD]) ? (float) $measured[$YMD] : null
		);
	}
	echo "var data = ", json_encode($table), ";";
}

function new_week($x, $start) {
	if (add_days($start, $x)->format("w") == 0)
		return ' class="NewWeek"';
	else
		return '';
}

?>
