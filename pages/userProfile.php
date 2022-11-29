<?php
session_start();
require_once('dropDownBox.php');
$selectedNameType = "ticket";
$serviceNameArray = array(
    "ticket" ,
    "newRequest"
);

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['profileDropDown'])) {
        if (isset($_POST['selectedNameType'])) {
            if (in_array($_POST['selectedNameType'], $serviceNameArray)) {
                $selectedNameType = filter_input(INPUT_POST, 'selectedNameType');
            } else {
                echo "wrong Input";
            }
        }
    }
}

$userID = 1;
$_SESSION ['userId'] = $userID;

function checkUserLoginStatus(){
    if(isset($_GET['page'])){
        //If GET is present -> include that page
        unset($_SESSION ['userId']);
        header("Location: userLogout.php");
    }
    else{
        //No GET present -> Check if admin is logged in via SESSION
        if(!isset($_SESSION['userId'])){
            header("Location: userLogout.php");
        }
    }
}

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
                loadUserInfo();
            ?>
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method='post'>
                <select name="selectedNameType" id="Service-type">
                    <?php
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
            loadUserProfileTable($_SESSION ['userId'],$selectedNameType);
        ?>
    </div>
</div>
</body>
</html>
<?php
function loadUserInfo(){
    if ($conn = mysqli_connect("localhost", "root", "", "serviceIT")) {
        // Create the query

        $query = "SELECT user.name, user.email, contract.id 
        FROM `contract` , user 
        WHERE contract.user_id = user.id And user_id = ?";

        //Prepare query as a statement
        if ($statement = mysqli_prepare($conn, $query)) {
            //Fill in ? parameters!

            mysqli_stmt_bind_param($statement, 'i', $_SESSION ['userId']);

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
            }else{
                "error:row < 1 or row >1";
            }
            //Close the statement and free memory
            mysqli_stmt_close($statement);

        } else {
            die(mysqli_error($conn));
        }
        //  Close the connection!
        mysqli_close($conn);

    } else {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }
}

function loadUserProfileTable($userId,$selectedFilterType)
{
    // Open a connection to MySQL...
    // Docker users need to use the service name (ex: mysql)
    $conn = mysqli_connect("localhost", "root", "");

    // And test the connection
    if (!$conn) {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }

    //Selecting the database (assuming it has already been created)
    if (mysqli_select_db($conn, "ServiceIT")) {

        //Create the query
        $query = "";
        if($selectedFilterType == "ticket"){
            $query = "SELECT service_ticket.id,service_ticket.service_type, service_ticket.status
                        FROM service_ticket
                        JOIN user ON user.id = service_ticket.user_id AND user.id = ?;";
        }elseif ($selectedFilterType == "newRequest"){
            $query = "SELECT service_request.id,service_request.service_type, service_request.status
                        FROM service_request
                        JOIN user ON user.id = service_request.user_id AND user.id = ?;";
        }

        // Prepare query as a statement
        if ($statement = mysqli_prepare($conn, $query)) {
            //Fill in ? parameter
             mysqli_stmt_bind_param($statement, 'i', $userId);

            // Execute statement and check success
            if (!mysqli_stmt_execute($statement)) {
                die(mysqli_error($conn));
            }

            // Bind result to variables when fetching...
            mysqli_stmt_bind_result($statement, $serviceId, $serviceType, $serviceStatus);
            // Step #6.2: And buffer the result if and only if you want to check the number of rows
            mysqli_stmt_store_result($statement);

            //Create heading

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
    } else {
        die(mysqli_error($conn));
    }

    // Close the connection!
    mysqli_close($conn);
}
?>





