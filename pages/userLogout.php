<?php
session_start();
//echo "<p>this is user page.</p>";
//echo "<p>please login as a user.</p>";
//echo "<p><a href='userProfile.php'>back to home page</a></p>";
$_SESSION["userLoggedin"] = false;
header("Location: logInPage.php");


