<?php
    session_start();
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
    <div class="contractMain">
        <?php
            loadAdminContractPageContent();
        ?>
    </div>
</body>
</html>
<?php
function loadAdminContractPageContent(){
    require_once('getContractFileName.php');
    $contractId = null;
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['contractId'])) {
            if (in_array($_GET['contractId'], array_keys($_SESSION['acceptedContractId']))) {
                $contractId = filter_input(INPUT_GET, 'contractId');
                $clientName = $_SESSION['acceptedContractId'][$contractId];
                $dirName = "upload";
                echo "<div class='contractAction'>";
                echo "<div class='contractContent'>";
                echo '<div class="contractDescription">     
                            <h1>Contract: </h1>                    
                            <h3>Contract ID: '.$contractId.'</h3>
                            <h3>Client Name: '.$clientName.'</h3>
                      </div>
                      <div class="contractActionForm">
                            <form action="uploadFile.php" method="post" enctype="multipart/form-data">
                                <label for="file">Filename:</label><input type="file" name="uploadedFile" id="file" />
                                <input type="hidden" name="contractID" value='.$contractId.'>                          
                                <p><input type="submit" name="upload" value="upload"/></p>
                            </form>
                             <p><a href="adminPanel.php">Back to adminPanel</a></p>
                             <p><a href='.$dirName."/".getContractFileName($contractId).'>Download Contract</a></p>
                      </div>';
                echo "</div>";

                echo '<div class="contractView">
                            <iframe src='.$dirName."/".getContractFileName($contractId).' width="100%" height="500px"></iframe>
                      </div>';
                echo "</div>";
            }else{
                echo "Contract ID input invalid: ".$contractId;
            }
        }else{
            echo "the contractId needs to be set and cannot be null";
        }
    }
}
?>
