<?php include "functions.php";
header("Content-type: application/json");

$conn = database_connect();
$id = mysqli_real_escape_string($conn, $_GET["id"]);
$query = "SELECT * FROM `food_usda` WHERE `id`=$id;";
$result = mysqli_query($conn, $query);

$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$row["id"] = (int) $row["id"];

echo json_encode($row);
?>
