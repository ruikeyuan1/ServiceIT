<?php
//initialise variables
$hostName = "localhost";
$userName = "root";
$password = "";
$database = "service";
// Open a connection to MySQL...
// Create connection
// Selecting the database (assuming it has already been created)
$conn = mysqli_connect($hostName, $userName, $password, $database);

//Test the connection & Terminates execution and return the error message if connection fails
if(!$conn)
{
    die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
}


