<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<!--    <link rel = "stylesheet" href="style/stylesheet.css" type="text/css">-->
    <link rel = "stylesheet" href="stylesheet.css" type="text/css">
    <title>Admin Panel</title>
</head>
<body class="userContractPage">
<div class="userContractMain">
    <?php
        loadUserContractActionForm();
    ?>
</div>
</body>
</html>
<?php
function loadUserContractActionForm(){
    require_once('getContractFile.php');
    $contractId = null;
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['contractId'])) {
            if ($_GET['contractId'] == $_SESSION ['userContractId']) {
                $contractId = filter_input(INPUT_GET, 'contractId');
                $dirName = "upload";
                echo "<div class='userContractAction'>";
                echo "<div class='userContractContent'>";
                echo '<div class="userContractDescription">
                        <h1>Contract: </h1>                    
                        <h3>Contract ID: '.$contractId.'</h3>
                    </div>
            
                  <div class="userContractActionForm">
                    <h3>Service IT</h3>
                    <p><a href="userProfile.php">Back to profile</a></p>
                    <p><a href='.$dirName."/".getContractFileName($contractId).'>Download Contract</a></p>
                  </div>';

                echo "</div>";
                echo '<div class="userContractView">
                            <iframe src='.$dirName."/".getContractFileName($contractId).' width="100%" height="500px"></iframe>
                      </div>';
                echo "</div>";
            }else{
                echo "Contract ID input invalid: ".$contractId;
            }
        }
    }
}
?>

