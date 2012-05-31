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
if (!isset($_GET["start"])) {
	$date_end = new DateTime();
} else {
	$date_end = new DateTime($_GET["end"]);
}
$days = date_diff($date_start, $date_end)->format("%a");

$food = array();
$exercise = array();
$measured = array();

$metabolism = get_metabolism($_SESSION["id"]);
if ($metabolism === FALSE) { ?>
	User profile incomplete. Cannot calculate weight. <a href="profile.php">Enter information.</a>
<? } else {
	$bmr = expenditure($metabolism["sex"], $metabolism["startweight"], $metabolism["height"], $metabolism["age"]);
}

$loss = array();
$net = array();
$measured = array();
$months = array();
$cumulative = array();

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$query = "SELECT date, food, exercise, net, measured FROM calories WHERE id=" . $_SESSION['id'] . " AND date >= '" . $date_start->format("Y-m-d") . "' AND date <= '" . $date_end->format("Y-m-d"). "'";
	$result = mysqli_query($conn, $query);
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$food[$row["date"]] = $row["food"];
		$exercise[$row["date"]] = $row["exercise"];
		$measured[$row["date"]] = $row["measured"];
	}
	mysqli_close($conn);
}

for ($day = 0; $day <= $days; $day++) {
	// find distinct months
	$today = add_days($date_start, $day);
	$months[$today->format("F Y")]++;

	$YMD = $today->format("Y-m-d");

	$net[$YMD] = $food[$YMD] - $exercise[$YMD];

	if ($food[$YMD] == "") {
		$loss[$YMD] = 0;
	} else {
		$loss[$YMD] = $net[$YMD] - expenditure($metabolism["sex"], $metabolism["startweight"] + $cumulative[$YMD] / 3500, $metabolism["height"], $metabolism["age"]) * $metabolism["lifestyle"];
	}
	$cumulative[$YMD] += $cumulative[sub_days($today, 1)->format("Y-m-d")] + $loss[$YMD];
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

function expenditure($sex, $weight, $height, $age) {
	switch ($sex) {
		case "male":
			return 66 + 6.23 * $weight + 12.7 * $height - 6.76 * $age;
		case "female":
			return 655 + 4.35 * $weight + 4.7 * $height - 4.7 * $age;
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

function register($user, $pass, $email) {
	if ($user == "" || $pass == "") return FALSE;
	$hash = hash("sha256", $password);
	$salt = createSalt();
	$hash = hash("sha256", $salt . $hash);

	$conn = database_connect();
	$user = mysqli_real_escape_string($user);
	$query = "INSERT INTO users (username, password, salt) VALUES ('$username', '$hash', '$salt');";
	mysqli_query($conn, $query);
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

function output_json_table($date_start, $days, $metabolism, $cumulative, $measured) {
	$date_start_int = $date_start->format("j");
	$table = array();
	$table[] = array("Date", "Actual", "Measured");
	for ($day = 0; $day <= $days; $day++) {
		$YMD = add_days($date_start, $day)->format("Y-m-d");

		$table[] = array(
			$date_start_int + $day + 0.5,
			$metabolism["startweight"] + $cumulative[$YMD] / 3500,
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
