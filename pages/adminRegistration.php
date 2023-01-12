<?php
require_once "connect.php";

$username = $password = $confirm_password = $name = "";
$username_err = $password_err = $confirm_password_err = $name_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{

        $sql = "SELECT id FROM administrator WHERE username = ?";
        
        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("s", $param_username);
            
            $param_username = trim($_POST["username"]);
            
            if($stmt->execute()){

                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }
    
    //Validate name 
    if(empty(trim($_POST["userName"]))){
        $name_err = "Please enter a name.";
    } elseif(!preg_match('/^[a-zA-Z]+$/', trim($_POST["userName"]))){
        $name_err = "Username can only contain letters";
    } else{
        $name = trim($_POST["userName"]);
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) <= 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }


    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($name_err)){
        
        $sql = "INSERT INTO administrator (`username`, `password`, `name`) VALUES (?, ?, ?)";

        if($stmt = $conn->prepare($sql)){

            $stmt->bind_param("sss", $param_username, $param_password, $param_name);
            
            $param_username = $username;
             // password hash
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_email = $email;
            $param_name = $name;

            if($stmt->execute()){
                header("location: adminLog.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            $stmt->close();
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset = "UTF-8">
    <meta http-equiv = "X-UA-Compatible" content="IE=edge">
    <meta name = "viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href="stylesheet.css" type="text/css">
    <title>Service IT</title>
</head>
<body>
    <div class = "header">
        <div class = "logo">Service IT</div>
        <button1 name = "admin" onclick="document.location='logInPage.php'">client</button1>
    </div>
    <div class = "container" id = "container"> 
        <div class = "loginTitle">
            <h1 class = "tagTitle">Welcome to Service IT</h1>
            <h2 class = "tagTitle">Register</h2>
        </div>
        <div class = "loginForm">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
                <input type = "text" name = "username" placeholder = "Enter a username" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                <input type = "text" name = "userName" placeholder = "Enter a name" <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                <span class="invalid-feedback"><?php echo $name_err; ?></span>
                <input type = "password" name = "password" placeholder = "Enter a password" <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
                <input type = "password" name = "confirm_password" placeholder = "Confirm the password" <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                <button name = "Sign_Up">Sign Up</button>
                <p>Alredy have an account? <a href = "logInPage.php">SignIn</a></p>
            </form>
        </div>
</body>
</html>