<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ServiceIT";

$conn = mysqli_connect($servername, $username, $password , $database );
// And test the connection
if (!$conn) {
    die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
}

?>

