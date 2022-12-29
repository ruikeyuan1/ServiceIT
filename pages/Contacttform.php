<?php
session_start();
require_once "connect.php";

$name = $email = $message = "";
$name_err = $email_err = $message_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    //Validate name 
    if(empty(trim($_POST["firstname"]))){
        $name_err = "Please enter a name.";
    } else{
        $name = trim($_POST["firstname"]);
    }

    //Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a valid email.";   
    } else{
        $email = trim($_POST["email"]);
    }

    //Validate message
    if(empty(trim($_POST["message"]))){
        $message_err = "Please enter a message.";   
    } else{
        $message = trim($_POST["message"]);
    }

    
    if(empty($name_err) && empty($email_err) && empty($message_err)){
        
        $sql = "INSERT INTO contact_form (`name`, `email`, `message`) VALUES (?, ?, ?)";

        if($stmt = $conn->prepare($sql)){

            if($stmt->bind_param("sss", $param_name, $param_email, $param_message)) {
            
                $param_name = $name;
                $param_email = $email;
                $param_message = $message;
                
                if($stmt->execute()){
                    echo "Your form is submitted! We will contact you soon.";
                } else{
                    echo "Error executing:" . $conn->error;
                }

                $stmt->close();
            } else{
            echo "Error binding:" . $conn->error;
        }
        } else{
            echo "Error preparing:" . $conn->error;
        }
    } else{
        echo "Please fill in all the fields before submission!";
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
    <title>ContactForm</title>
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

</head>
</body>

<h3>Contact Form</h3>

<div class="container">
  <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="fname">First Name</label>
    <input type="text" id="fname" name="firstname" placeholder=" ">

    <label for="email">Email</label>
    <input type="text" id="email" name="email" placeholder=" ">

    <label for="subject">Message</label>
    <textarea id="message" name="message" placeholder="Write something you would like to know.."></textarea>

    <input type="submit" value="Submit Form">
  </form>
</div>

</body>
</html>

