<?php
require_once "connect.php";

$name = $email = $description = "";
$name_err = $email_err = $description_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate name
    if(empty(trim($_POST["name"]))){
        $name_err = "Please enter a name.";
    }else{
      $name = trim($_POST["name"]);
    }
    
    //Validate email 
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else{
        $email = trim($_POST["email"]);
    }

    //Validate description
    if(empty(trim($_POST["description"]))){
      $description_err = "Please enter an email.";
    } else{
      $description = trim($_POST["description"]);
    }

    
    if(empty($name_err) && empty($email_err) && empty($description_err)){
        
        $sql = "INSERT INTO service_ticket (`user_name`, `usr_email`, `description`) VALUES (?, ?, ?)";

        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("ssss", $param_name, $param_email, $param_description);
            
            $param_name = $name;
            $param_email = $email;
            $param_description = $description;

            $stmt->close();
        }
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
      <form action="/action_page.php" menthod="post">
      <h3>Order a ticket</h3>

        <input type="text" name="name" placeholder="Enter your name"  <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
        <span class="invalid-feedback"><?php echo $name_err; ?></span>
        <input type="text" name="email" placeholder="Your your email" <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
            <span class="invalid-feedback"><?php echo $email_err; ?></span>
        <label for="subject">Describe the problem</label>
        <textarea id="subject" name="email" placeholder=" " style="height:200px" <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $description; ?>"></textarea>
        <span class="invalid-feedback"><?php echo $description_err; ?></span>
        <input type="submit" value="Submit">

      </form>
    </div>
  </body>    
</html>