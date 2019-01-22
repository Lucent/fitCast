<?php include "session.php";

if (isset($_SESSION["valid"]) && $_SESSION["valid"] === 1) {
	$conn = database_connect();
	$id = $_SESSION["id"];

	$bmr = mysqli_real_escape_string($conn, $_POST["bmr"]);
	$query = "UPDATE users SET bmr='$bmr' WHERE id='$id';";
	echo $query;
	mysqli_query($conn, $query);
	echo $conn->error;

	mysqli_close($conn);
}
?>
