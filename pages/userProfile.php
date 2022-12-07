<?php
session_start();

//user id assigned for testing
$userID = 1;
$_SESSION ['userId'] = $userID;

//link the page that contains the display function for dropDown box
require_once('dropDownBox.php');
//set the default service name
$selectedNameType = "ticket";

//Initialise array for service name. The array is used to check the input service name and contains the options
//for dropDown box.
$serviceNameArray = array(
    "ticket" ,
    "newRequest"
);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //check if the post request for the dropDown box is set.
    if (isset($_POST['profileDropDown'])) {
        //check if the serviceName is set
        if (isset($_POST['selectedNameType'])) {
            //check if the serviceName is one of the service name in the system(check if it exists)
            if (in_array($_POST['selectedNameType'], $serviceNameArray)) {
                //filter and assign the service name to a variable
                $selectedNameType = filter_input(INPUT_POST, 'selectedNameType',FILTER_SANITIZE_SPECIAL_CHARS);
            } else {
                echo "service name input does not exist";
            }
        }
    }
}


//This function checks whether the user has logged in when accessing this page.
function checkUserLoginStatus(){
    if (isset($_GET['page'])){
        //include that page If GET is present
        unset($_SESSION ['userId']);
        //direct back to home page
        header("Location: userLogout.php");
    } else {
        //Check if admin is logged in via SESSION if No GET is present
        if (!isset($_SESSION['userId'])){
            //direct back to home page
            header("Location: userLogout.php");
        }
    }
}

//call the function to check if the user has logged in while visiting profile page
//If not the page will be directed to home page or login page
checkUserLoginStatus();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>UserProfile</title>
    <link rel = "stylesheet" href="stylesheet.css" type="text/css">
</head>
<body class="userProfilePage">
<div class="userProfileMain">
    <div class="userProfileContent">
        <div class="userProfileDescription">
            <h1>Profile</h1>
            <?php
                //load the basic information to be displayed like client name,etc
                loadUserInfo();
            ?>
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method='post'>
                <select name="selectedNameType" id="Service-type">
                    <?php
                    //display the dropDown box for selecting service names like ticket and new request.
                    dropDownBox($serviceNameArray,$selectedNameType);
                    ?>
                </select>
                <input type='submit' name='profileDropDown' value='Confirm'>
            </form>
        </div>
        <div class="userProfileActionLinks">
            <h3>Service IT</h3>
            <p><a href='userProfile.php?page=logout'>click here to log out</a></p>
            <p><a href='userContract.php?contractId=<?php echo $_SESSION ['userContractId']?>'>View contract</a></p>
        </div>
    </div>
    <div class="userProfileTable">
        <?php
            //load the table in the profile page
            loadUserProfileTable($_SESSION ['userId'],$selectedNameType);
        ?>
    </div>
</div>
</body>
</html>
<?php
function loadUserInfo(){
    //load the php file for connecting database
    require 'databaseConnect.php';
    // Create the query
    $query = "SELECT user.name, user.email, contract.id 
    FROM `contract` , user 
    WHERE contract.user_id = user.id And user_id = ?";

    //Prepare query as a statement
    if ($statement = mysqli_prepare($conn, $query)) {
        //Fill in ? parameters!
        mysqli_stmt_bind_param($statement, 'i', $_SESSION['userId']);
        //Execute statement and check success
        if (!mysqli_stmt_execute($statement)) {
            die(mysqli_error($conn));
        }
        mysqli_stmt_bind_result($statement, $name, $email, $contractId);

        mysqli_stmt_store_result($statement);

        if (mysqli_stmt_num_rows($statement) == 1) {
            while (mysqli_stmt_fetch($statement))
            {
                echo "<p><h3>Your Name: <span>".$name."</span></h3></p>";
                echo "<p><h3>Your Email: <span>".$email."</span></h3></p>";
                echo "<p><h3>Your contract ID:<span>".$contractId."</span></h3></p>";
                $_SESSION ['userContractId'] = $contractId;
            }
        } else {
            "error:row < 1 or row >1";
        }
        //Close the statement and free memory
        mysqli_stmt_close($statement);

    } else {
        die(mysqli_error($conn));
    }
    //  Close the connection!
    mysqli_close($conn);

}

function loadUserProfileTable($userId,$selectedFilterType)
{
    //load the php file for connecting database
    require 'databaseConnect.php';

    //Create the query
    $query = "";
    if($selectedFilterType == "ticket"){
        $query = "SELECT service_ticket.id,service_ticket.service_type, service_ticket.status
                    FROM service_ticket
                    JOIN user ON user.id = service_ticket.user_id AND user.id = ?;";
    } elseif ($selectedFilterType == "newRequest"){
        $query = "SELECT service_request.id,service_request.service_type, service_request.status
                    FROM service_request
                    JOIN user ON user.id = service_request.user_id AND user.id = ?;";
    }

    // Prepare query as a statement
    if ($statement = mysqli_prepare($conn, $query)) {
        //Fill in ? parameter
         mysqli_stmt_bind_param($statement, 'i', $userId);

        //Execute statement and check success
        if (!mysqli_stmt_execute($statement)) {
            die(mysqli_error($conn));
        }

        //Bind result to variables when fetching...
        mysqli_stmt_bind_result($statement, $serviceId, $serviceType, $serviceStatus);
        //And buffer the result if and only if you want to check the number of rows
        mysqli_stmt_store_result($statement);

        //Check if there are results in the statement
        if (mysqli_stmt_num_rows($statement) > 0) {
             //Make table
            echo "<div class='userProfileTable'><table border='1'>";
            // Make table header
            echo "<th style='text-align: left;'>ServiceID</th><th>ServiceType</th><th>ServiceStatus</th>";

            //Fetch all rows of data from the result statement
            while (mysqli_stmt_fetch($statement)) {

                // Create row
                echo "<tr>";

                // Create cells
                echo "<td>" . $serviceId . "</td>";
                echo "<td>" . $serviceType . "</td>";
                echo "<td>" . $serviceStatus . "</td>";

                // Close row
                echo "</tr>";
            }
            // Close table
           echo "</table></div>";
        } else {
            echo "No admins found";
        }
        //  Close the statement and free memory
        mysqli_stmt_close($statement);
    } else {
        die(mysqli_error($conn));
    }

    // Close the connection!
    mysqli_close($conn);
}
?>





