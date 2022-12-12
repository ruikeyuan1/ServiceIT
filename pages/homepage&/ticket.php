<<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>TicketPage</title>
</head>

<body>

    <div class="navbar">
  <a class="home" href="#home">Home</a>
  <a href="#News Service">News Service</a>
  <a href="#Ticket">Ticket</a>
  <a href="#Profile">Profile</a>
</div>  

<div class="header">
    <h1>SERVICE IT</h1>
</div>

<div class="container">
  <form action="/action_page.php" menthod="post">
  <h3>Order a ticket</h3>
  
    <input type="text" id="fname" name="firstname" placeholder="Enter your name and lastname">
    
    <input type="text" id="lname" name="lastname" placeholder="Your your email">

    <label for="subject">Describe the problem</label>
    <textarea id="subject" name="subject" placeholder=" " style="height:200px"></textarea>

    <input type="submit" value="Submit">
  </form>
</div>

<?php
 require_once "connect.php";

// get the post records
$txtMessage = $_POST['description'];

// database insert SQL code
$sql = "INSERT INTO `service_ticket (`description`) VALUES ('$txtMessage')";

// insert in database 
$rs = mysqli_query($con, $sql);

if($rs)
{
  echo "your ticket has successfully submitted";
}

?>

</body>    
</html>