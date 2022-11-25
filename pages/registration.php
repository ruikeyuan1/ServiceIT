<!-- <?php
session_start();
include "connect.php";
include_once "utils.php";




?> -->
<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta http-equiv = "X-UA-Compatible" content="IE=edge">
    <meta name = "viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href="Stylesheet.css" type="text/css">
    <title>Service IT</title>
</head>
<body>
    <div class = "header">
        <div class = "logo">Service IT</div>
        <button1 name = "admin" onclick="document.location='adminLog.php'">admin</button1>
    </div>
    <div class = "container" id = "container"> 
        <h1>Welcome to Service IT</h1>
        <h2>Register</h2>
        <form> 
            <input type = "text" name = "Username" placeholder = "Enter a username"/>
            <input type = "text" name = "Email" placeholder = "Enter an email"/>
            <input type = "password" name = "Password" placeholder = "Enter a password"/>
            <button name = "Sign_Up">Sign Up</button>
            <p>Alredy have an account? <a href = "logInPage.php">SignIn</a></p>
        </form>
    </div>
</body>
</html>