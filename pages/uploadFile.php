<?php
session_start();
$adminID = 1;
$_SESSION ['adminId'] = $adminID;

define ('SITE_ROOT',realpath('upload/'));

function fileUpload($contractId){
    if ($_FILES["uploadedFile"]["size"] < 1000000)
    {
        $acceptedFileTypes = ["application/pdf"];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);

        $uploadedFileType = finfo_file($fileInfo, $_FILES["uploadedFile"]["tmp_name"]);

        if(in_array($uploadedFileType, $acceptedFileTypes))
        {
            if ($_FILES["uploadedFile"]["error"] > 0)
            {
                echo "Error: " . $_FILES["uploadedFile"]["error"] . "<br />";
            }else{
                echo "<br />Upload: " . $_FILES["uploadedFile"]["name"] . "<br />";
                echo "Type: " . $uploadedFileType . "<br />";
                echo "Size: " . ($_FILES["uploadedFile"]["size"] / 1024) . " Kb<br />";

                if (file_exists( SITE_ROOT."/".$_FILES["uploadedFile"]["name"])){
                    echo $_FILES["uploadedFile"]["name"] . "<p>"." already exists. ". "<p/>";
                }else{
                    $file = $_FILES["uploadedFile"]["name"];
                    $info = pathinfo($file);
                    $fileName =  basename($file,'.'.$info['extension']);
                    echo "<p>"."FileName: " . $fileName . "<p/>";

                    if(strlen($fileName) > 50 or ctype_lower($fileName)){
                        echo "The original file name must not exceed 50 characters and must contain at least 1 uppercase.";
                    }else{
                        //If the file does not exist, transfer the file from the temporary folder to the upload folder using the original upload name
                        if(move_uploaded_file($_FILES["uploadedFile"]["tmp_name"], SITE_ROOT."/".$_FILES["uploadedFile"]["name"])){
                            echo "<p>"."Stored in folder"."</p>";
                                deleteStoredFile($contractId);
                                updateContractIntoDatabase($contractId,$_FILES["uploadedFile"]["name"]);
                        }else {
                            echo "Something went wrong while uploading.";
                        }
                    }
                }
                echo '<p><a href="adminPanel.php">Go back to AdminPanel</a></p>';
            }
        }else{
            echo "Invalid file type. Must be pdf.";
        }
    }else{
        echo "Invalid file size. Must be less than 1000KB.";
    }
}

function deleteStoredFile($contractId){
    if ($conn = mysqli_connect("localhost", "root", "", "serviceIT")) {
        // Step #3: Create the query

        $query = "SELECT `file_path` FROM `contract` WHERE `id` = ?";

        //Prepare query as a statement
        if ($statement = mysqli_prepare($conn, $query)) {
            //Fill in ? parameters!

            mysqli_stmt_bind_param($statement, 'i', $contractId);

            //Execute statement and check success
            if (!mysqli_stmt_execute($statement)) {
                echo "Error executing query";
                die(mysqli_error($conn));
            }
            mysqli_stmt_bind_result($statement, $fileToBeDeleted);

            mysqli_stmt_store_result($statement);
            echo "<br><br>--------------<br><br>";

            if (mysqli_stmt_num_rows($statement) == 1) {
                while (mysqli_stmt_fetch($statement))
                {
                    if (file_exists(SITE_ROOT."/".$fileToBeDeleted))
                    {
                        unlink(SITE_ROOT."/".$fileToBeDeleted);
                        echo "<p>".$fileToBeDeleted." deleted from database</p>";
                    }else{
                        echo "File does not exist in the folder";
                    }
                }
            }else{
                "error:row < 1 or row >1";
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

function updateContractIntoDatabase($contractId,$filePath){
    if(!$_SESSION ['adminId'] == null) {
        if (!empty($_SESSION ['adminId'])) {
            echo "<p>ContractId: ".$contractId."</p>";
            echo "<p>File: ".$filePath."</p>";
            echo "<p>Operated by the admin--AdminId: ".$_SESSION ['adminId']."</p>";
            // Create connection
            //Selecting the database (assuming it has already been created)
            //Open a connection to MySQL...
            if ($conn = mysqli_connect("localhost", "root", "", "serviceIT")) {
                // Step #3: Create the query

                $query = "UPDATE `contract` 
                            SET `file_path` = ?
                            WHERE contract.id = ?";

                //Prepare query as a statement
                if ($statement = mysqli_prepare($conn, $query)) {
                    //Fill in ? parameters!
                    mysqli_stmt_bind_param($statement, 'si', $filePath,$contractId);
                    //Execute statement and check success
                    if (!mysqli_stmt_execute($statement)) {
                        echo "Error executing query";
                        die(mysqli_error($conn));
                    }
                    echo "<br><br>--------------<br><br>";
                    echo "<p>".$filePath." stored in database</p>";
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

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['upload'])) {
        if (!empty($_POST['contractID'])) {
            if (!empty($_FILES["uploadedFile"]["tmp_name"])) {
                if (in_array($_POST['contractID'], $_SESSION['acceptedContractId'])) {
                    $contractId = filter_input(INPUT_POST, 'contractID');
                    fileUpload($contractId);
                } else {
                    echo "contractID is invalid.You can only upload an contract based the contract you selected";
                }
            } else {
                echo "You cannot upload an empty file(fileName) or upload nothing.";
            }
        } else {
            echo "ContractId input is empty.Please select an contract to be updated.";
        }
    }
}
