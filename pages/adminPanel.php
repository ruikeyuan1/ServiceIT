<?php
session_start();

$serviceNameSelected = "ticket";
$serviceTypeSelected = "all";
$serviceHandlingSelected = "all";
$hideHandledService = false;
$serviceToBeUpdated = array(0);
$acceptedContractId = array(0);

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


require('getAdminArray.php');

$adminArray = getAdminArray();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['selectedDropDown'])) {
        if(isset($_POST['serviceType'])) {
            if (in_array($_POST['serviceType'], $serviceTypeArray)) {
                $serviceType = filter_input(INPUT_POST, 'serviceType');
                $serviceTypeSelected = $serviceType;
                echo $serviceTypeSelected;
            }else{
                echo "wrong Input";
            }
        }

        if(isset($_POST['serviceName'])) {
            if (in_array($_POST['serviceName'], $serviceNameArray)) {
                $serviceName = filter_input(INPUT_POST, 'serviceName');
                $serviceNameSelected = $serviceName;
                echo $serviceNameSelected;
            }else{
                echo "wrong Input";
            }
        }

        if(isset($_POST['serviceHandlingType'])) {
            echo $_POST['serviceHandlingType'];
            if (in_array($_POST['serviceHandlingType'], $serviceHandlingArray)) {
                $serviceHandlingSelected = filter_input(INPUT_POST, 'serviceHandlingType');
                if($serviceHandlingSelected == "unHandled"){
                    $hideHandledService = true;
                }else{
                    $hideHandledService = false;
                }

            }else{
                echo "wrong handling Input";
            }
        }

    }


    if(isset($_POST['Save'])) {
        if(in_array($_POST['serviceTypeSaved'] , $serviceTypeArray)){
            if(in_array($_POST['serviceNameSaved'] , $serviceNameArray)){
                if(in_array($_POST['serviceHandlingSaved'] , $serviceHandlingArray)){
                    $serviceNameSelected = filter_input(INPUT_POST, 'serviceNameSaved');
                    $serviceTypeSelected = filter_input(INPUT_POST, 'serviceTypeSaved');
                    $serviceHandlingSelected = filter_input(INPUT_POST, 'serviceHandlingSaved');

                    if($serviceHandlingSelected == "unHandled"){
                        $hideHandledService = true;
                    }else{
                        $hideHandledService = false;
                    }

                    if(in_array( $_POST['serviceStatus'] , $statusArray)){
                        //echo  "The input: ".$_POST['serviceStatus']."is correct";
                        if(in_array($_POST['adminId'] , array_keys($adminArray))){
                            //echo  "The input: ".$_POST['adminId']."is correct";
                            if(in_array($_POST['serviceId'] , $_SESSION['serviceToBeUpdated'])){
                                $serviceStatusSelected = filter_input(INPUT_POST, 'serviceStatus');
                                $serviceIdSelected = filter_input(INPUT_POST, 'serviceId');
                                $adminIdSelected = filter_input(INPUT_POST, 'adminId');
                                updateService($serviceNameSelected, $serviceStatusSelected,$serviceIdSelected,$adminIdSelected);
                            }else{
                                echo  "Wrong service Id Input";
                            }
                        }else{
                            echo  "Wrong adminId Input";
                        }
                    }else{
                        echo  "Wrong status Input";
                    }
                }
            }else{
                echo  "Wrong service name";
            }
        }
    }
}


function updateService($serviceNameSelected, $serviceStatusSelected, $serviceIdSelected, $adminIdSelected){
    if ($conn = mysqli_connect("localhost", "root", "", "ServiceIT")) {
        //Prepare query as a statement

        $query = "";
        if($serviceNameSelected == "newRequest"){
            $query = "UPDATE `service_request` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";
        }elseif ($serviceNameSelected == "ticket") {
            $query = "UPDATE `service_ticket` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";
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
    <link rel = "stylesheet" href="style/stylesheet.css" type="text/css">
    <title>Admin Panel</title>
</head>
<body>

    <div class="adminPanelMain">
        <div class="header">
            <h3>Service IT</h3>
            <h1>Admin Panel</h1>
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
                loadAdminTable($serviceNameSelected,$serviceTypeSelected,$serviceToBeUpdated,$acceptedContractId,
                $hideHandledService,$statusArray,$adminArray,$serviceHandlingSelected);
            ?>
        </div>
    </div>
</body>
</html>

<?php


function loadAdminTable($serviceNameSelected,$serviceTypeSelected,$serviceToBeUpdated,$acceptedContractId,
                        $hideHandledService,$statusArray,$adminArray,$serviceHandlingSelected){
    $adminID = 1;
    $_SESSION ['adminId'] = $adminID;
    $checkBindPara = false;

    if(!$_SESSION ['adminId'] == null) {
        if (!empty($_SESSION ['adminId'])) {
            echo "<h3>AdminId:" . $_SESSION ['adminId']."</h3>";
            // Open a connection to MySQL...
            // Create connection
            // Selecting the database (assuming it has already been created)
            if ($conn = mysqli_connect("localhost", "root", "", "ServiceIT")) {
                // Step #3: Create the query
                $query = "";
                if ($serviceNameSelected == "newRequest") {
                    if($serviceTypeSelected == "all"){
                        $query = "SELECT service_request.id, service_request.admin_id, service_request.service_type, service_request.status, user.name, contract.id
                        FROM service_request
                        JOIN user ON user.id = service_request.user_id JOIN contract ON user.contract_id = contract.id;";
                    }else{
                        $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.name, contract.id
                        FROM service_request
                        JOIN user ON user.id = service_request.user_id JOIN contract ON user.contract_id = contract.id AND service_request.service_type = ?;";
                        $para = "";
                        $para = $serviceTypeSelected;
                        $checkBindPara = true;
                    }
                }else{
                    if($serviceTypeSelected == "all"){
                        $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.name, contract.id
                        FROM service_ticket
                        JOIN user ON user.id = service_ticket.user_id JOIN contract ON user.contract_id = contract.id;";
                    }else{
                        $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.name, contract.id
                        FROM service_ticket
                        JOIN user ON user.id = service_ticket.user_id JOIN contract ON user.contract_id = contract.id AND service_ticket.service_type = ?;";
                        $para = "";
                        $para = $serviceTypeSelected;
                        $checkBindPara = true;
                    }
                }

                //Prepare query as a statement
                if ($statement = mysqli_prepare($conn, $query)) {
                    // Step #4.2: Fill in the ? parameters!
                    if($checkBindPara == true) {
                        mysqli_stmt_bind_param($statement, 's', $para);
                    }

                    //Execute statement and check success
                    if (!mysqli_stmt_execute($statement)) {
                        echo "Error executing query";
                        die(mysqli_error($conn));
                    }

                    // Step #6.1: Bind result to variables when fetching...
                    mysqli_stmt_bind_result($statement, $service_id, $admin_id, $service_type, $ticket_status, $user_name, $contract_id);
                    // Step #6.2: And buffer the result if and only if you want to check the number of rows
                    mysqli_stmt_store_result($statement);

                    //Create heading
                    if($serviceNameSelected == "ticket"){
                        echo "<h2>List of tickets</h2>";
                    }else{
                        echo "<h2>List of new requests</h2>";
                    }


                    //Check if there are results in the statement
                    if (mysqli_stmt_num_rows($statement) > 0) {

                        //echo "Number of rows: " . mysqli_stmt_num_rows($statement);
                        // Make table

                        echo "<div class='adminTable'><table>";

                        // Make table header
                        echo "<th>ID</th><th>Type</th><th>Status</th><th>AdminName</th><th>UserName</th><th>ContractID</th><th>Save</th>";

                        //Fetch all rows of data from the result statement
                        while (mysqli_stmt_fetch($statement)) {
                            $serviceToBeUpdated[] += $service_id;
                            $acceptedContractId[] += $contract_id;

                            if(!($ticket_status == "Done" AND $hideHandledService == true)){
                                // Create row
                                echo "<form action=" . htmlentities($_SERVER['PHP_SELF']) . " method='post'>";
                                echo "<tr>";

                                // Create cells
                                echo "<td>" . $service_id . "</td>";
                                //echo "<td>" . $admin_id . "</td>";
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
                        echo "No tickets found";
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

