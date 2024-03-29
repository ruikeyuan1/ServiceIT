<?php
session_start();
if(isset($_SESSION["userLoggedin"]) && $_SESSION["userLoggedin"] === true){
    header("location: home.php");
    exit;
}

require_once "connect.php";

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        
        $sql = "SELECT `id`, `username`, `password` FROM user WHERE username = ?";
        
        if($stmt = $conn->prepare($sql)){
            
            $stmt->bind_param("s", $param_username);

            $param_username = $username;

            if($stmt->execute()){

                $stmt->store_result();
                
                // Check if username exists, if yes then verify password
                if($stmt->num_rows == 1){                    
                        
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["userLoggedin"] = true;
                            $_SESSION["userId"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: home.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
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
        <button1 name = "admin" onclick="document.location='adminLog.php'">admin</button1>
    </div>
    <div class = "container" id = "container"> 
        <div class = "loginTitle">
            <h1 class = "tagTitle">Welcome to Service IT</h1>
            <h2 class = "tagTitle">Log in</h2>
        </div>
            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>
            <div class = "loginForm">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <input type = "text" name = "username" placeholder = "Enter a username" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span><?php echo $username_err; ?></span>
                    
                    <input type="password" name="password" placeholder = "Enter a password" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span><?php echo $password_err; ?></span>

                    <button name = "Sign_In">Sign In</button>
                    <p>Don't have an account? <a href = "registration.php">SignUp</a></p>
                </form>
            </div>
    </div>
</body>
</html>
