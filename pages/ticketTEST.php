<?php
session_start();
require_once "connect.php";

$name = $email = $description = "";
$name_err = $email_err = $description_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    //Validate name 
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter a name.";
    } else{
        $name = trim($_POST["name"]);
    }

    //Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a password.";   
    } else{
        $email = trim($_POST["email"]);
    }

    //Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter a description.";   
    } else{
        $description = trim($_POST["description"]);
    }

    
    if(empty($name_err) && empty($email_err) && empty($description_err)){
        
        $sql = "INSERT INTO service_ticket (`user_name`, `user_email`, `description`) VALUES (?, ?, ?)";

        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("sss", $param_name, $param_email, $param_description);
            
            $param_name = $name;
            $param_email = $email;
            $param_description = $description;
            
            if($stmt->execute()){
                echo "Executed!";
            } else{
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
    } else{
        echo "Error peparing:" . mysqli.error($conn);
    }
    } else{
        echo "Please fill in all the fileds before submission!";
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
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
    <div clas="logo">SERVICE IT</div>
</div>
    <div class="container">
   
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
        <h3>Order a ticket</h3>
            <input type="text" name="name" placeholder="Enter your name">
            <input type="text" name="email" placeholder="Your your email">
            <label for="subject">Describe the problem</label>
            <textarea id="subject" name="description" placeholder=" " style="height:200px"></textarea>
            <input type="submit" value="Submit"> 

        </form> 
    </div>
    </body>    
</html> 