<?php
session_start();
//echo "<p>this is adminLogout page.</p>";
//echo "<p>please login as an admin.</p>";
//echo "<p><a href='adminLog.php'>back to home page</a></p>";
$_SESSION["adminLoggedin"] = false;
header("Location: adminLog.php");