
<?php
    session_start();
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
    <div class="contractMain">

        <?php
            loadAdminContractActionForm();
        ?>

    </div>
</body>
</html>

<?php

function loadAdminContractActionForm(){
    require_once('getContractFile.php');
    define ('SITE_ROOT',realpath(dirname(__DIR__,5)));
    $contractId = null;
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        if(isset($_GET['contractId'])) {
            if (in_array($_GET['contractId'], $_SESSION['acceptedContractId'])) {
                $contractId = filter_input(INPUT_GET, 'contractId');

                echo "<div class='contractAction'>";
                echo "<div class='contractContent'>";
                echo '<div class="contractDescription">     
                            <h1>Contract: </h1>                    
                            <h3>Contract ID: '.$contractId.'</h3>
                      </div>
                      <div class="contractActionForm">
                            <form action="uploadFile.php" method="post" enctype="multipart/form-data">
                                <label for="file">Filename:</label><input type="file" name="uploadedFile" id="file" />
                                <input type="hidden" name="contractID" value='.$contractId.'>
                                <p><input type="submit" name="upload" value="upload"/></p>
                            </form>
                              <p><a href="adminPanel.php">Back to adminPanel</a></p>
                                <p><a href='.SITE_ROOT.'ServiceIT'.getContractFileName($contractId).'>Download Contract</a></p>
                      </div>';
                echo "</div>";

                echo '<div class="contractView">
                            <iframe src='.SITE_ROOT.'ServiceIT'.getContractFileName($contractId).' width="100%" height="500px"></iframe>
                      </div>';
                echo "</div>";
            }else{
                echo "Contract ID input invalid: ".$contractId;
            }
        }
    }
}

?>
