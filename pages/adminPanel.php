<?php
session_start();
$adminID = 1;
$_SESSION['adminId'] = $adminID;

function checkAdminLoginStatus(){
    if(isset($_GET['page'])){
        //If GET is present -> include that page
        unset($_SESSION ['adminId']);
        header("Location: adminLogout.php");
    }
    else{
        //No GET present -> Check if admin is logged in via SESSION
        if(!isset($_SESSION['adminId'])){
            header("Location: adminLogout.php");
        }
    }
}

checkAdminLoginStatus();

$serviceNameSelected = "ticket";
$serviceTypeSelected = "all";
$serviceHandlingSelected = "all";
$hideHandledService = false;
$serviceToBeUpdated = array();
$acceptedContractId = array();

require_once('dropDownBox.php');

$statusArray = array(
    "InProgress" ,
    "Done"
);

$serviceNameArray = array(
    "ticket" ,
    "newRequest"
);

$serviceTypeArray = array(
    "laptop_repair",
    "phone_repair",
    "all"
);

$serviceHandlingArray = array(
    "unHandled",
    "all"
);

require_once('getAdminArray.php');

$adminArray = getAdminArray();

function checkServiceHandling($serviceHandlingSelected) : bool{
    if($serviceHandlingSelected == "unHandled"){
        return true;
    }
    return false;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['selectedDropDown'])) {
        if(isset($_POST['serviceType'])) {
            if (in_array($_POST['serviceType'], $serviceTypeArray)) {
                $serviceType = filter_input(INPUT_POST, 'serviceType');
                $serviceTypeSelected = $serviceType;
            }else{
                echo "wrong Input";
            }
        }

        if(isset($_POST['serviceName'])) {
            if (in_array($_POST['serviceName'], $serviceNameArray)) {
                $serviceName = filter_input(INPUT_POST, 'serviceName');
                $serviceNameSelected = $serviceName;
            }else{
                echo "wrong Input";
            }
        }

        if(isset($_POST['serviceHandlingType'])) {
            if (in_array($_POST['serviceHandlingType'], $serviceHandlingArray)) {
                $serviceHandlingSelected = filter_input(INPUT_POST, 'serviceHandlingType');
            }else{
                echo "wrong handling Input";
            }
        }
    }

    if(isset($_POST['Save'])) {
        if(in_array($_POST['serviceTypeSaved'] , $serviceTypeArray)){
            if(in_array($_POST['serviceNameSaved'] , $serviceNameArray)){
                if(in_array($_POST['serviceHandlingSaved'] , $serviceHandlingArray)){
                    //These three inputs are assigned after immediately checking because the
                    //checks for the filter have been completed.The system needs to remember what has been selected.
                    $serviceNameSelected = filter_input(INPUT_POST, 'serviceNameSaved');
                    $serviceTypeSelected = filter_input(INPUT_POST, 'serviceTypeSaved');
                    $serviceHandlingSelected = filter_input(INPUT_POST, 'serviceHandlingSaved');

                    if(in_array( $_POST['serviceStatus'] , $statusArray)){
                        if(in_array($_POST['adminId'] , array_keys($adminArray))){
                            if(in_array($_POST['serviceId'] , $_SESSION['serviceToBeUpdated'])){
                                unset($_SESSION['serviceToBeUpdated']);
                                $serviceStatusSelected = filter_input(INPUT_POST, 'serviceStatus');
                                $serviceIdSelected = filter_input(INPUT_POST, 'serviceId');
                                $adminIdSelected = filter_input(INPUT_POST, 'adminId');
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

function updateService($serviceNameSelected, $serviceStatusSelected, $serviceIdSelected, $adminIdSelected){
    if ($conn = mysqli_connect("localhost", "root", "", "ServiceIT")) {
        //Prepare query as a statement

        $query = "UPDATE `service_ticket` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";

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
    } else {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }
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
            <h3>Service IT</h3>
            <h1>Admin Panel</h1>
            <a href='adminPanel.php?page=logout'>click here to log out</a>
        </div>
        <div>
            <form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method='post'>
                <select name="serviceName" id="Service-type">
                    <?php
                        dropDownBox($serviceNameArray, $serviceNameSelected);
                    ?>
                </select>

                <select name="serviceType" id="Service-name">
                    <?php
                        dropDownBox($serviceTypeArray, $serviceTypeSelected);
                    ?>
                </select>

                <select name="serviceHandlingType" id="Service-name">
                    <?php
                        dropDownBox($serviceHandlingArray, $serviceHandlingSelected);
                    ?>
                </select>

                <input type='submit' name='selectedDropDown' value='Confirm'>
            </form>
        </div>

        <div>
            <?php
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
    $checkBindPara = false;
    if(isset($_SESSION ['adminId'])) {
        if (!empty($_SESSION ['adminId'])) {
            echo "<h3>AdminId:" . $_SESSION ['adminId']."</h3>";
            // Open a connection to MySQL...
            // Create connection
            // Selecting the database (assuming it has already been created)
            if ($conn = mysqli_connect("localhost", "root", "", "serviceIT")) {
                //Create the query

                if ($serviceNameSelected == "newRequest") {
                    if($serviceTypeSelected == "all"){
                        $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.name, contract.id 
                        FROM service_request 
                        INNER JOIN user ON user.id = service_request.user_id INNER JOIN contract ON user.id = contract.user_id;";
                    }else{
                        $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.name, contract.id 
                        FROM service_request 
                        INNER JOIN user ON user.id = service_request.user_id INNER JOIN contract ON user.id = contract.user_id AND service_request.service_type = ?;";
                        $checkBindPara = true;
                    }
                }else{
                    if($serviceTypeSelected == "all"){
                        $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.name, contract.id 
                        FROM service_ticket 
                        INNER JOIN user ON user.id = service_ticket.user_id INNER JOIN contract ON user.id = contract.user_id";
                    }else{
                        $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.name, contract.id 
                        FROM service_ticket 
                        INNER JOIN user ON user.id = service_ticket.user_id INNER JOIN contract ON user.id = contract.user_id  AND service_ticket.service_type = ?;";
                        $checkBindPara = true;
                    }
                }

                //Prepare query as a statement
                if ($statement = mysqli_prepare($conn, $query)) {
                    //Fill in ? parameters!
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

                    //Create heading
                    if($serviceNameSelected == "ticket"){
                        echo "<h2>List of tickets</h2>";
                    }else{
                        echo "<h2>List of new requests</h2>";
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
                                $serviceToBeUpdated[] += $service_id;
                                $acceptedContractId[$contract_id] = $user_name;
                                echo "<form action=" . htmlentities($_SERVER['PHP_SELF']) . " method='post'>";
                                echo "<tr>";

                                // Create cells
                                echo "<td>" . $service_id . "</td>";
                                echo "<td>" . $service_type . "</td>";

                                echo '<td><select name="serviceStatus">';
                                dropDownBox($statusArray, $ticket_status);
                                echo '</select></td>';

                                echo '<td><select name="adminId">';

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
                                echo "<td><input type='submit' name='Save' value='Save'></td>";
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
            } else {
                die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
            }
        }
    }
}
?>

