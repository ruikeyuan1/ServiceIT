<?php
session_start();

$serviceNameSelected = "ticket";
$serviceTypeSelected = "all";
$serviceToBeUpdated = array(0);

$statusArray = array(
    "InProgress" ,
    "Done"
);

$serviceNameArray = array(
    "ticket" ,
    "newRequest"
);

$serviceTypeArray = array(
    "laptopRepair",
    "phoneRepair",
    "all"
);

//$adminArray = array();
require('getAdminArray.php');

$adminArray = getAdminArray();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['serviceType'])) {
        $serviceType = filter_input(INPUT_POST, 'serviceType');
        if (in_array($serviceType, $serviceTypeArray)) {
            $serviceTypeSelected = $serviceType;
            echo $serviceTypeSelected;
        }else{
            echo "wrong Input";
        }
    }

    if(isset($_POST['serviceName'])) {
        $serviceName = filter_input(INPUT_POST, 'serviceName');
        if (in_array($serviceName, $serviceNameArray)) {
            $serviceNameSelected = $serviceName;
            echo $serviceNameSelected;
        }else{
            echo "wrong Input";
        }
    }

    if(isset($_POST['Save'])) {
        $serviceNameSelected = filter_input(INPUT_POST, 'serviceNameSaved');
        $serviceTypeSelected = filter_input(INPUT_POST, 'serviceTypeSaved');
        $serviceStatusSelected = filter_input(INPUT_POST, 'serviceStatus');
        $serviceIdSelected = filter_input(INPUT_POST, 'serviceId');
        $adminIdSelected = filter_input(INPUT_POST, 'adminId');
        echo $serviceIdSelected;
        echo $serviceNameSelected;
        echo  $serviceStatusSelected;
        updateService($serviceNameSelected, $serviceStatusSelected,$serviceIdSelected,$adminIdSelected);
    }
}


function updateService($serviceNameSelected, $serviceStatusSelected, $serviceIdSelected, $adminIdSelected){
    if ($conn = mysqli_connect("localhost", "root", "", "ServiceIT")) {
        //Prepare query as a statement

        if($serviceNameSelected == "newRequest"){
            $query = "UPDATE `service_request` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";
        }elseif ($serviceNameSelected == "ticket") {
            $query = "UPDATE `service_ticket` SET `admin_id`= ? ,`status`= ? WHERE `id` = ?";
        }

        if ($statement = mysqli_prepare($conn, $query)) {
            print_r($query);
            //Fill in ? parameters!
            mysqli_stmt_bind_param($statement, 'isi',$adminIdSelected, $serviceStatusSelected, $serviceIdSelected);
            //Execute statement and check success
            if (mysqli_stmt_execute($statement)) {
                echo "Query executed";
            } else {
                echo "Error executing query";
                die(mysqli_error($conn));
            }
            echo "<br><br>--------------<br><br>";

            //Close the statement and free memory
            mysqli_stmt_close($statement);
        } else {
            die(mysqli_error($conn));
        }
        // Step #10: Close the connection!
        mysqli_close($conn);
    } else {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Example Database Connection</title>
</head>
<body>
<form action="<?php echo htmlentities($_SERVER['PHP_SELF'])?>" method='post'>
    <select name="serviceName" id="Service-type">
        <?php
            foreach($serviceNameArray as $serviceName) {
                if ($serviceNameSelected == $serviceName) {
                    echo "<option value=$serviceNameSelected selected>$serviceNameSelected</option>";
                }
                else {
                    echo "<option value=$serviceName>$serviceName</option>";
                }
            }
        ?>
    </select>

    <select name="serviceType" id="Service-name">
        <?php
            foreach ($serviceTypeArray as $serviceType) {
                if ($serviceTypeSelected == $serviceType) {
                    echo "<option value=$serviceTypeSelected selected>$serviceTypeSelected</option>";
                } else {
                    echo "<option value=$serviceType>$serviceType</option>";
                }
            }
        ?>
    </select>

    <input type='submit' name='selectedDropDown' value='Confirm'>
</form>



<?php

$adminID = 1;
$_SESSION ['adminId'] = $adminID;
$checkBindPara = false;
//$hideSolvedService = true;

if(!$_SESSION ['adminId'] == null) {
    if (!empty($_SESSION ['adminId'])) {
        echo "AdminId:" . $_SESSION ['adminId'];
        // Step #1: Open a connection to MySQL...
        // Create connection
        // Step #2: Selecting the database (assuming it has already been created)
        if ($conn = mysqli_connect("localhost", "root", "", "ServiceIT")) {
            // Step #3: Create the query
//            if($hideSolvedService == true){
//
//            }else{
//
//            }
            if ($serviceNameSelected == "newRequest" AND $serviceTypeSelected == "all") {
                $query = "SELECT service_request.id, service_request.admin_id, service_request.service_type, service_request.status, user.name, contract.id
                        FROM service_request
                        JOIN user ON user.id = service_request.user_id JOIN contract ON user.contract_id = contract.id;";
            }
            elseif ($serviceNameSelected == "ticket" AND $serviceTypeSelected == "all") {
                $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.name, contract.id
                        FROM service_ticket
                        JOIN user ON user.id = service_ticket.user_id JOIN contract ON user.contract_id = contract.id;";
            }
            elseif($serviceNameSelected == "newRequest" AND $serviceTypeSelected == "laptopRepair"){
                $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.name, contract.id
                        FROM service_request
                        JOIN user ON user.id = service_request.user_id JOIN contract ON user.contract_id = contract.id AND service_request.service_type = ?;";
                $para = "laptop_repair";
                $checkBindPara = true;
            }
            elseif($serviceNameSelected == "newRequest" AND $serviceTypeSelected == "phoneRepair"){
                $query = "SELECT service_request.id, service_request.admin_id,service_request.service_type, service_request.status, user.name, contract.id
                        FROM service_request
                        JOIN user ON user.id = service_request.user_id JOIN contract ON user.contract_id = contract.id AND service_request.service_type = ?;";
                $para = "phone_repair";
                $checkBindPara = true;
            }
            elseif($serviceNameSelected== "ticket" AND $serviceTypeSelected == "laptopRepair"){
                $query = "SELECT service_ticket.id, service_ticket.admin_id,service_ticket.service_type, service_ticket.status, user.name, contract.id
                        FROM service_ticket
                        JOIN user ON user.id = service_ticket.user_id JOIN contract ON user.contract_id = contract.id AND service_ticket.service_type = ?;";
                $para = "laptop_repair";
                $checkBindPara = true;
            }
            elseif($serviceNameSelected == "ticket" AND $serviceTypeSelected == "phoneRepair"){
                $query = "SELECT service_ticket.id, service_ticket.admin_id, service_ticket.service_type, service_ticket.status, user.name, contract.id
                        FROM service_ticket
                        JOIN user ON user.id = service_ticket.user_id JOIN contract ON user.contract_id = contract.id AND service_ticket.service_type = ?;";
                $para = "phone_repair";
                $checkBindPara = true;
            }

            // Step #4.1: Prepare query as a statement
            if ($statement = mysqli_prepare($conn, $query)) {
                // Step #4.2: Fill in the ? parameters!
                if($checkBindPara == true) {
                    mysqli_stmt_bind_param($statement, 's', $para);
                }

                // Step #5: Execute statement and check success
                if (mysqli_stmt_execute($statement)) {
                    echo "Query executed";
                } else {
                    echo "Error executing query";
                    die(mysqli_error($conn));
                }
                echo "<br><br>--------------<br><br>";

                // Step #6.1: Bind result to variables when fetching...
                mysqli_stmt_bind_result($statement, $service_id, $admin_id, $service_type, $ticket_status, $user_name, $contract_id);
                // Step #6.2: And buffer the result if and only if you want to check the number of rows
                mysqli_stmt_store_result($statement);

                //Create heading
                echo "<h2>List of tickets/new Requests</h2>";

                // Step #7: Check if there are results in the statement
                if (mysqli_stmt_num_rows($statement) > 0) {

                    echo "Number of rows: " . mysqli_stmt_num_rows($statement);
                    // Make table

                    echo "<table border='1'>";

                    // Make table header
                    echo "<th style='text-align: left;'>ID</th><th>Type</th><th>Status</th><th>AdminName</th><th>UserName</th><th>ContractID</th><th>Save</th>";

                    // Step #8: Fetch all rows of data from the result statement
                    while (mysqli_stmt_fetch($statement)) {
                        $serviceToBeUpdated[] += $service_id;
                        // Create row
                        echo "<form action=" . htmlentities($_SERVER['PHP_SELF']) . " method='post'>";
                        echo "<tr>";

                        // Create cells
                        echo "<td>" . $service_id . "</td>";
                        //echo "<td>" . $admin_id . "</td>";
                        echo "<td>" . $service_type . "</td>";

                        echo '<td><select name="serviceStatus">';

                        foreach ($statusArray as $statusName) {
                            if ($ticket_status == $statusName) {
                                echo "<option value=$ticket_status selected>$ticket_status</option>";
                            } else {
                                echo "<option value=$statusName>$statusName</option>";
                            }
                        }
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

                        $contract = '<a href="adminContract.php?contractId='.$contract_id.'">'.$contract_id.'</a>';

                        echo "<td>" . $user_name . "</td>";
                        echo "<td>" . $contract . "</td>";
                        echo "<td><input type='submit' name='Save' value='Save'></td>";
                        // Close row
                        echo "</tr>";
                        echo "</form>";
                    }

                    // Close table

                    echo "</table>";

                    } else {
                        echo "No tickets found";
                    }
                    // Step #9: Close the statement and free memory
                    mysqli_stmt_close($statement);
            } else {
                die(mysqli_error($conn));
            }

            //Step #10: Close the connection!
            mysqli_close($conn);
        } else {
            die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
        }
    }
}

?>


</body>
</html>
