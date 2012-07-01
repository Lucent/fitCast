<? include "functions.php";
header("Content-type: application/json");

$conn = database_connect();
$search = mysqli_real_escape_string($conn, $_GET["search"]);
$query = "SELECT `id`,`long` FROM `food_usda_desc` WHERE `long` LIKE '%$search%';";
$result = mysqli_query($conn, $query);

$row_set = array();
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	$row["id"] = (int) $row["id"];
	$row_set[] = $row;
}

echo json_encode($row_set);
?>
