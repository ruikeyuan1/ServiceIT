<?php
session_start();

//admin id assigned for testing
//$adminID = 1;
//$_SESSION['adminId'] = $adminID;

//This function checks whether the admin is logged in.If not the page will be directed to home page
function checkAdminLoginStatus(){
    if(isset($_GET['page'])){
        //If GET is present -> include that page
        unset($_SESSION ['adminId']);
        $_SESSION["adminLoggedin"] = false;
        header("Location: adminLog.php");
    } else {
        //No GET present -> Check if admin is logged in via SESSION
        if(!isset($_SESSION['adminId'])){
            $_SESSION["adminLoggedin"] = false;
            header("Location: adminLog.php");
        }
    }
}

checkAdminLoginStatus();
//variable for checking/tracking the display option for the name of the service(like service ticket)
$serviceNameSelected = "ticket";
//variable for checking/tracking the display option for the type of the service(like phone_repair)
$serviceTypeSelected = "all";
//variable for checking/tracking the display option(only shows the handled services or all services)
$serviceHandlingSelected = "all";
//array for checking if the service selected(save button clicked) exists in the displayed table
$serviceToBeUpdated = array();
//array for checking contractId input
$acceptedContractId = array();

require_once('dropDownBox.php');

//Initialise array for service status. The array is used to check the input status and contains the display options
//for dropDown box.
$statusArray = array(
    "InProgress" ,
    "Done"
);

//Initialise array for service name. The array is used to check the input service name and contains the options
//for dropDown box.
$serviceNameArray = array(
    "ticket" ,
    "newRequest"
);

//Initialise array for service type. The array is used to check the input service type and contains the options
//for dropDown box.
$serviceTypeArray = array(
    "laptop_repair",
    "phone_repair",
    "software_service",
    "hosting_service",
    "all"
);

//Initialise array for service type. The array and contains the options
//for dropDown box.
$serviceHandlingArray = array(
    "unHandled",
    "all"
);

//require the page to get the existing admins and put the name and id into an associative array
//The data is from the database and the id and name is fetched from a while loop
require_once('getAdminArray.php');
$adminArray = getAdminArray();

//check if the service is handled or not while fetching the services data
function checkServiceHandling($serviceHandlingSelected) : bool{
    if($serviceHandlingSelected == "unHandled"){
        return true;
    }
    return false;
}


//check if it is a post request
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //check if the post request for the dropDown boxes is set.
    if(isset($_POST['selectedDropDown'])) {
        //check if the serviceType is set
        if(isset($_POST['serviceType'])) {
            //check if the service type selected exists
            if (in_array($_POST['serviceType'], $serviceTypeArray)) {
                //filter the serviceType and assign it to a variable
                $serviceTypeSelected = filter_input(INPUT_POST, 'serviceType',FILTER_SANITIZE_SPECIAL_CHARS);
            }else{
                echo "service type input is incorrect";
            }
        }
        //check if the serviceName is set
        if(isset($_POST['serviceName'])) {
            //check if the service name selected exists
            if (in_array($_POST['serviceName'], $serviceNameArray)) {
                //filter the serviceName and assign it to a variable
                $serviceNameSelected = filter_input(INPUT_POST, 'serviceName',FILTER_SANITIZE_SPECIAL_CHARS);
            }else{
                echo "service name input is incorrect";
            }
        }
        //check if the serviceHandlingType is set
        if(isset($_POST['serviceHandlingType'])) {
            //check if the serviceHandlingType selected exists
            if (in_array($_POST['serviceHandlingType'], $serviceHandlingArray)) {
                //filter the serviceHandlingType  and assign it to a variable
                $serviceHandlingSelected = filter_input(INPUT_POST, 'serviceHandlingType',FILTER_SANITIZE_SPECIAL_CHARS);
            }else{
                echo "service handling type input is incorrect";
            }
        }
    }

    if(isset($_POST['Save'])) {
        //check if the serviceTypeSaved input exists
        if(in_array($_POST['serviceTypeSaved'] , $serviceTypeArray)){
            //check if the serviceNameSaved input exists
            if(in_array($_POST['serviceNameSaved'] , $serviceNameArray)){
                //check if the serviceHandlingType input exists
                if(in_array($_POST['serviceHandlingSaved'] , $serviceHandlingArray)){
                    //These three inputs are assigned after immediately checking because the
                    //checks for the filter have been completed.The system needs to remember what has been selected.
                    $serviceNameSelected = filter_input(INPUT_POST, 'serviceNameSaved',FILTER_SANITIZE_SPECIAL_CHARS);
                    $serviceTypeSelected = filter_input(INPUT_POST, 'serviceTypeSaved',FILTER_SANITIZE_SPECIAL_CHARS);
                    $serviceHandlingSelected = filter_input(INPUT_POST, 'serviceHandlingSaved',FILTER_SANITIZE_SPECIAL_CHARS);

                    //check if the service status input exists
                    if(in_array( $_POST['serviceStatus'] , $statusArray)){
                        //check if the input service adminId is for an actual admin in the system
                        if(in_array($_POST['adminId'] , array_keys($adminArray))){
                            //check if the input service serviceId is from one of the services displayed
                            if(in_array($_POST['serviceId'] , $_SESSION['serviceToBeUpdated'])){
                                //unset the session as it is on longer useful
                                unset($_SESSION['serviceToBeUpdated']);
                                //filter and assign the inputs after all checks
                                $serviceStatusSelected = filter_input(INPUT_POST, 'serviceStatus',FILTER_SANITIZE_SPECIAL_CHARS);
                                $serviceIdSelected = filter_input(INPUT_POST, 'serviceId',FILTER_SANITIZE_NUMBER_INT);
                                $adminIdSelected = filter_input(INPUT_POST, 'adminId',FILTER_SANITIZE_NUMBER_INT);
                                //call the function so the data saved is updated in database
                                updateService($serviceNameSelected, $serviceStatusSelected,$serviceIdSelected,$adminIdSelected);
                            }else{
                                echo  "ServiceId cannot be empty and you can only select an displayed/existing service.";
                            }
                        }else{
                            echo  "AdminId/Name cannot be empty and you can only select an existing administrator.";
                        }
                    }else{
                        echo  "Status cannot be empty and you can only select an correct status.";
                    }
                }else{
                    echo  "Handling type cannot be empty and you can only select an correct handling type.";
                }
            }else{
                echo  "ServiceName(Like ticket) cannot be empty and you can only select an correct serviceName.";
            }
        }else{
            echo  "ServiceType(Like phone_repair) cannot be empty and you can only select an correct serviceType.";
        }
    }
}

function updateService($serviceNameSelected, $serviceStatusSelected, $serviceIdSelected, $adminIdSelected){;
    //load the php file for connecting database
    require 'connect.php';
    //Prepare query as a statement
    $query = "UPDATE `service_ticket` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";

    //check if the service name is newRequest.If so make the query for new requests
    if($serviceNameSelected == "newRequest"){
        $query = "UPDATE `service_request` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";
    }

    if ($statement = mysqli_prepare($conn, $query)) {
        //Fill in ? parameters!
        mysqli_stmt_bind_param($statement, 'isi',$adminIdSelected, $serviceStatusSelected, $serviceIdSelected);
        //Execute statement and check success
        if (!mysqli_stmt_execute($statement)) {
            echo "Error executing query";
            die(mysqli_error($conn));
        }
        //Close the statement and free memory
        mysqli_stmt_close($statement);
    } else {
        die(mysqli_error($conn));
    }

    // Close the connection!
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href="stylesheet.css" type="text/css">
    <title>Admin Panel</title>
</head>
<body class="adminPanelPage">
    <div class="adminPanelMain">
        <div class="header">
            <h3 class='tag'>Service IT</h3>
            <h1 class='tag'>Admin Panel</h1>
            <a href='adminPanel.php?page=logout'>click here to log out</a>
        </div>
        <div>
            <form class="adminPanelForm" action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method='post'>
                <select class="panelInput" name="serviceName" id="Service-type">
                    <?php
                        //display drop down options for service name like new request and ticket
                        dropDownBox($serviceNameArray, $serviceNameSelected);
                    ?>
                </select>

                <select class="panelInput" name="serviceType" id="Service-name">
                    <?php
                        //display drop down options for service type like phone_repair and laptop_repair
                        dropDownBox($serviceTypeArray, $serviceTypeSelected);
                    ?>
                </select>

                <select class="panelInput" name="serviceHandlingType" id="Service-name">
                    <?php
                        //display handling display options like handled services and all services
                        dropDownBox($serviceHandlingArray, $serviceHandlingSelected);
                    ?>
                </select>

                <input class="panelInput" type='submit' name='selectedDropDown' value='Confirm'>
            </form>
        </div>

        <div>
            <?php
                //load the admin panel table
                loadAdminPanelTable($serviceNameSelected,$serviceTypeSelected,$serviceToBeUpdated,$acceptedContractId,
               $statusArray,$adminArray,$serviceHandlingSelected);
            ?>
        </div>
    </div>
</body>
</html>

<?php
function loadAdminPanelTable($serviceNameSelected,$serviceTypeSelected,$serviceToBeUpdated,$acceptedContractId,
                        $statusArray,$adminArray,$serviceHandlingSelected){
    if(isset($_SESSION ['adminId'])) {
        if (!empty($_SESSION ['adminId'])) {
            echo "<h3 class='tag'>AdminId:" . $_SESSION ['adminId']."</h3>";
            //load the php file for connecting database
            require 'connect.php';

            //set the default variable(whether to bind parameter) to false
            $checkBindPara = false;
            //Create the query, and assign the fetching sql to $query based on the
            //selected service type and service name
            if ($serviceNameSelected == "newRequest") {
                if($serviceTypeSelected == "all"){
                    $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.username, contract.id 
                    FROM service_request 
                    INNER JOIN user ON user.id = service_request.user_id INNER JOIN contract ON user.id = contract.user_id;";
                }else{
                    $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.username, contract.id 
                    FROM service_request 
                    INNER JOIN user ON user.id = service_request.user_id INNER JOIN contract ON user.id = contract.user_id AND service_request.service_type = ?;";
                    $checkBindPara = true;
                }
            }else{
                if($serviceTypeSelected == "all"){
                    $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.username, contract.id 
                    FROM service_ticket 
                    INNER JOIN user ON user.id = service_ticket.user_id INNER JOIN contract ON user.id = contract.user_id";
                }else{
                    $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.username, contract.id 
                    FROM service_ticket 
                    INNER JOIN user ON user.id = service_ticket.user_id INNER JOIN contract ON user.id = contract.user_id  AND service_ticket.service_type = ?;";
                    $checkBindPara = true;
                }
            }

            //Prepare query as a statement
            if ($statement = mysqli_prepare($conn, $query)) {
                //Fill in ? parameters!
                //if the $query has parameters that need to be bind with variable
                if($checkBindPara == true) {
                    mysqli_stmt_bind_param($statement, 's', $serviceTypeSelected);
                }

                //Execute statement and check success
                if (!mysqli_stmt_execute($statement)) {
                    echo "Error executing query";
                    die(mysqli_error($conn));
                }

                //Bind result to variables when fetching...
                mysqli_stmt_bind_result($statement, $service_id, $admin_id, $service_type, $ticket_status, $user_name, $contract_id);
                //And buffer the result if and only if you want to check the number of rows
                mysqli_stmt_store_result($statement);

                //check the service name for displaying the title
                if($serviceNameSelected == "ticket"){
                    echo "<h2 class='tag'>List of tickets</h2>";
                }else{
                    echo "<h2 class='tag'>List of new requests</h2>";
                }

                //Check if there are results in the statement
                if (mysqli_stmt_num_rows($statement) > 0) {
                    // Make table
                    echo "<div class='adminTable'><table>";
                    // Make table header
                    echo "<th>ID</th><th>Type</th><th>Status</th><th>AdminName</th><th>UserName</th><th>ContractID</th><th>Save</th>";
                    //Fetch all rows of data from the result statement
                    while (mysqli_stmt_fetch($statement)) {
                        if(!($ticket_status == "Done" AND checkServiceHandling($serviceHandlingSelected))){
                            // Create row
                            //update array for saving all the service id displayed.Later on the array will be
                            // assigned to a session.
                            $serviceToBeUpdated[] += $service_id;
                            //update the associative array for saving displayed contractId and the user
                            // having the contract
                            $acceptedContractId[$contract_id] = $user_name;
                            echo "<form action=" . htmlentities($_SERVER['PHP_SELF']) . " method='post'>";
                            echo "<tr>";

                            // Create cells
                            echo "<td>" . $service_id . "</td>";
                            echo "<td>" . $service_type . "</td>";

                            echo '<td><select class="panelInput" name="serviceStatus">';
                            dropDownBox($statusArray, $ticket_status);
                            echo '</select></td>';

                            echo '<td><select class="panelInput" name="adminId">';
                            //echo the admins that can be assigned to
                            foreach ($adminArray as $adminId => $adminName) {
                                if ($admin_id == $adminId) {
                                    echo "<option value=$admin_id selected>$adminName</option>";
                                } else {
                                    echo "<option value=$adminId>$adminName</option>";
                                }
                            }

                            echo '</select></td>';

                            echo "<input type='hidden' name='serviceId' value='$service_id'>";
                            echo "<input type='hidden' name='serviceTypeSaved' value='$serviceTypeSelected'>";
                            echo "<input type='hidden' name='serviceNameSaved' value='$serviceNameSelected'>";
                            echo "<input type='hidden' name='serviceHandlingSaved' value='$serviceHandlingSelected'>";

                            $contract = '<a href="adminContract.php?contractId='.$contract_id.'">'.$contract_id.'</a>';

                            echo "<td>" . $user_name . "</td>";
                            echo "<td>" . $contract . "</td>";
                            echo "<td><input class='panelInput' type='submit' name='Save' value='Save'></td>";
                            // Close row
                            echo "</tr>";
                            echo "</form>";
                        }
                    }
                    // Close table

                    echo "</table></div>";

                    $_SESSION['serviceToBeUpdated'] = $serviceToBeUpdated;
                    $_SESSION['acceptedContractId'] = $acceptedContractId;
                } else {
                    echo "No tickets or requests found";
                }
                //Close the statement and free memory
                mysqli_stmt_close($statement);
            } else {
                die(mysqli_error($conn));
            }

            //Close the connection!
            mysqli_close($conn);
        }
    }
}
?>

