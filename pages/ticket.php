<?php
session_start();


$user_id = $_SESSION["userId"];
//print($_SESSION["userId"]);
$user_id = 1;
$user_name = $_SESSION["username"];
$admin_id = 0;
$status ="InProgress";
$description = "";
$description_err = "";
$service_type = "phone_repair";
//getSelectOptions();

// function getSelectOptions() : array {
//     require "connect.php";

$array = [];


require "connect.php";
$sql = "SELECT `service_type` FROM `service_request` WHERE `user_id` = ? ";

if($stmt = $conn->prepare($sql)){

    if($stmt->bind_param("i",$user_id)) {                      
        if($stmt->execute()){
            echo "Your form is submitted!";
        } else{
            //echo "Something went wrong. Please try again later.";
            echo "Error executing:" . $conn->error;
        }

        $stmt->store_result();
        //print_r($stmt);

        if($stmt->num_rows > 0) {
            
            $stmt -> bind_result($serviceType);
            while($stmt ->fetch()) {
                if(!in_array($serviceType,$array))
                {
                    array_push($array,$serviceType);
                }
            }

                //print($serviceType);
                // $array += [$serviceType];
               
        }

            $stmt->close();
    } else{
     
        echo "Error binding:" . $conn->error;
    }
} else{
    echo "Error peparing:" . $conn->error;
}

$conn->close();

//print_r($array);
//return $array;

//     }
// }
require "connect.php";
if($_SERVER["REQUEST_METHOD"] == "POST"){    

    $service_type = $_POST["options"];
    //Validate description
    if(empty(trim($_POST["description"]))){
        $description_err = "Please enter a description.";   
    } else{
        $description = trim($_POST["description"]);
    }

    $param_description = $description;
    
    if(empty($description_err)){
        
        $sql = "INSERT INTO service_ticket (`user_id`,`admin_id`,`status`,`description`,`service_type`) VALUES (?,?,?,?,?)";

        if($stmt = $conn->prepare($sql)){

            if($stmt->bind_param("iisss",$user_id,$admin_id,$status,$param_description, $service_type)) {            
                
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
   
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="selectEx" method="post"> 
        <h3>Order a ticket</h3>
         <select name="options" id="options">
        <?php

        $serviceTypeToBeChecked="";
            foreach ($array as $serviceType) {
                if ($serviceTypeToBeChecked == $serviceType) {
                    echo "<option value=$serviceTypeToBeChecked selected>$serviceTypeToBeChecked</option>";
                }
                else {
                    echo "<option value=$serviceType>$serviceType</option>";
                }
            }
        ?>    
         </select>
            <label for="subject">Describe the problem</label>
            <textarea id="subject" name="description" placeholder=" " style="height:200px"></textarea>
            <input type="submit" value="Submit"> 

        </form> 
    </div>
    </body>    
</html> 