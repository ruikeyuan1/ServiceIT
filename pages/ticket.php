<?php
session_start();
require_once "connect.php";

$user_id = $_SESSION["userId"];
$user_name = $_SESSION["username"];
$user_email = $_SESSION["userEmail"];
$description = "";
$description_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){    

    //Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter a description.";   
    } else{
        $description = trim($_POST["description"]);
    }

    
    if(empty($description_err)){
        
        $sql = "INSERT INTO service_ticket (`user_id`, `user_name`,`user_email`,`description`) VALUES (?,?,?)";

        if($stmt = $conn->prepare($sql)){

            if($stmt->bind_param("isss",$user_id,$user_name,$user_email,$param_description)) {

                $param_description = $description;
                
                if($stmt->execute()){
                    echo "Your form is submitted!";
                } else{
                    //echo "Something went wrong. Please try again later.";
                    echo "Error executing:" . $conn->error;
                }

                $stmt->close();
            } else{
            echo "Error binding:" . $conn->error;
        }
        } else{
            echo "Error peparing:" . $conn->error;
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
    <link rel="stylesheet" href="stylesheet.css">
    <title>TicketPage</title>
</head>

<body>
    <div class="navbar">
        <a class="home" href="home">Home</a>
        <a href="News Service">News Service</a>
        <a href="Ticket">Ticket</a>
        <a href="Profile">Profile</a>
        <a href="contactform">ContactUs</a>
    </div>  
<div class="header">
    <div class="logo">SERVICE IT</div>
</div>
    <div class="container">
   
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
        <h3>Order a ticket</h3>
            <label for="subject">Describe the problem</label>
            <textarea id="subject" name="description" placeholder=" " style="height:200px"></textarea>
            <input type="submit" value="Submit"> 

        </form> 
    </div>
    </body>    
</html> 