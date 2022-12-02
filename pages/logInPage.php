<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: homePage.php");
    exit;
}

require_once "connect.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
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
        // Prepare 
        $sql = "SELECT `id`, `username`, `password` FROM user WHERE username = ?";
        
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $stmt->store_result();
                
                    $stmt->bind_result($id, $username, $hashed_password);
                    if($stmt->fetch()){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    // Close connection
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
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">  -->
    <title>Service IT</title>
</head>
<body>
    <div class = "header">
        <div class = "logo">Service IT</div>
        <button1 name = "admin" onclick="document.location='adminLog.php'">admin</button1>
    </div>
    <div class = "container" id = "container"> 
        <h1>Welcome to Service IT</h1>
        <h2>Log in</h2>
            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type = "text" name = "username" placeholder = "Enter a username" <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
            
            <input type="password" name="password" placeholder = "Enter a password" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
            <span class="invalid-feedback"><?php echo $password_err; ?></span>

            <p>Forgot your password?</p>
            <button name = "Sign_In">Sign In</button>
            <p>Don't have an account? <a href = "#">SignUp</a></p>
        </form>
    </div>
</body>
</html>
