<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel = "stylesheet" href="Stylesheet.css" type="text/css">
    <title>Admin Panel</title>
</head>
<body class="userContractPage">
<div class="userContractMain">
    <?php
        loadUserContractPageContent();
    ?>
</div>
</body>
</html>
<?php
function loadUserContractPageContent(){
    require_once('getContractFileName.php');
    $contractId = null;
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['contractId'])) {
            //check if the contractId got is the contractId in the session.The session is assigned in the userProfile
            //page when fetching data
            if ($_GET['contractId'] == $_SESSION ['userContractId']) {
                //filter the contractId and assign to a variable
                $contractId = filter_input(INPUT_GET, 'contractId',FILTER_SANITIZE_NUMBER_INT);
                //define the name of the directory for loading(displaying) the contract later on
                $dirName = "upload";
                echo "<div class='userContractAction'>";
                echo "<div class='userContractContent'>";
                echo '<div class="userContractDescription">
                        <h1>Contract: </h1>                    
                        <h3>Contract ID: '.$contractId.'</h3>
                    </div>
            
                  <div class="userContractActionForm">
                    <h3>Service IT</h3>
                    <p><a href="userProfile.php">Back to profile</a></p>';

                //check if there is a contract file assigned to the contractId sent
                if(getContractFileName($contractId) != null AND trim(getContractFileName($contractId)) != ''){
                    echo '<p><a href='.$dirName."/".getContractFileName($contractId).'>Download Contract</a></p>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="contractView">
                               <iframe src='.$dirName."/".getContractFileName($contractId).' width="100%" height="500px"></iframe>
                          </div>';
                    echo "</div>";
                }else{
                    echo 'no contract file attached, please wait for administrators to upload a contract for you';
                    echo '</div>';
                    echo '</div>';
                }
            }else{
                echo "Contract ID input invalid: ".$contractId;
            }
        }
    }
}
?>


