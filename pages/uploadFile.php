<?php
session_start();
$adminID = 1;
$_SESSION ['adminId'] = $adminID;

//define a root path(path the file to be saved) variable, so
//it is easier to maintain the code(when changing the path to save the file)
define ('SITE_ROOT',realpath('upload/'));

function fileUpload($contractId){
    if ($_FILES["uploadedFile"]["size"] < 1000000)
    {
        //declare a array that contains the accepted file types to check if the uploaded file is a pdf
        $acceptedFileTypes = ["application/pdf"];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        //assign the file type of the uploaded file to a variable for upcoming type check
        $uploadedFileType = finfo_file($fileInfo, $_FILES["uploadedFile"]["tmp_name"]);
        //check if the file type of the uploaded file is correct
        if(in_array($uploadedFileType, $acceptedFileTypes))
        {
            //If there are errors regarding uploaded file
            if ($_FILES["uploadedFile"]["error"] > 0)
            {
                //echo the error
                echo "Error: " . $_FILES["uploadedFile"]["error"] . "<br />";
            }else{
                //echo the name of the uploaded file
                echo "<br />Upload: " . $_FILES["uploadedFile"]["name"] . "<br />";
                //echo the file type of the uploaded file(contract)
                echo "Type: " . $uploadedFileType . "<br />";
                //echo the size of the uploaded file(contract)
                echo "Size: " . ($_FILES["uploadedFile"]["size"] / 1024) . " Kb<br />";
                //check if there is already a file having the same name exists.
                if (file_exists( SITE_ROOT."/".$_FILES["uploadedFile"]["name"])){
                    //echo that there is a same existing contract
                    echo $_FILES["uploadedFile"]["name"] . "<p>"." already exists. ". "<p/>";
                }else{
                    //assign the file name to a variable
                    $file = $_FILES["uploadedFile"]["name"];
                    //get the info of the file and assign it to an array variable.(an array saving different attributes
                    //of the file name)
                    $info = pathinfo($file);
                    $fileName = basename($file,'.'.$info['extension']);
                    //echo the file name of the file uploaded w
                    echo "<p>"."FileName: " . $fileName . "<p/>";

                    //check if the length of the file name is less than 50 and has at least 1 uppercase
                    if(strlen($fileName) > 50 or ctype_lower($fileName)){
                        echo "The original file name must not exceed 50 characters and must contain at least 1 uppercase.";
                    }else{
                        //Transfer the file from the temporary folder to the upload folder using the original upload name
                        if(move_uploaded_file($_FILES["uploadedFile"]["tmp_name"], SITE_ROOT."/".$_FILES["uploadedFile"]["name"])){
                            //echo that the file has been uploaded and successfully stored in the right path
                            echo "<p>"."Stored in folder"."</p>";
                                //call the function to delete the former contract.So the newest contract with the
                                //same contractId will replace the old one
                                deleteStoredFile($contractId);
                                //call the function to update the contract uploaded to the database
                                updateContractIntoDatabase($contractId,$_FILES["uploadedFile"]["name"]);
                        }else {
                            echo "Something went wrong while uploading(while moving file from temporary folder).";
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
    $conn = mysqli_connect("localhost", "root", "");

    if(!$conn)
    {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }

    if (mysqli_select_db($conn, "serviceIT")) {
        //Create the query

        $query = "SELECT `file_path` FROM `contract` WHERE `id` = ?";

        //Prepare query as a statement
        if ($statement = mysqli_prepare($conn, $query)) {
            //Fill in ? parameters

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

    } else {
        die(mysqli_error($conn));
    }
    //Close the connection!
    mysqli_close($conn);
}

function updateContractIntoDatabase($contractId,$fileName){
    //make sure the admin has logged in
    if(!$_SESSION ['adminId'] == null) {
        if (!empty($_SESSION ['adminId'])) {
            //echo the contractId of the contract uploaded
            echo "<p>ContractId: ".$contractId."</p>";
            //echo the file name of the contract uploaded
            echo "<p>File: ".$fileName."</p>";
            //echo the adminId of the admin taking action
            echo "<p>Operated by the admin--AdminId: ".$_SESSION ['adminId']."</p>";
            // Create connection
            //Selecting the database (assuming it has already been created)
            //Open a connection to MySQL...
            $conn = mysqli_connect("localhost", "root", "", "serviceIT");
            //return the error message and terminates the execution of the script if there are errors of connection
            if(!$conn)
            {
                die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
            }

            if (mysqli_select_db($conn, "serviceIT")) {
                //Create the query
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
            } else {
                die(mysqli_error($conn));
            }
            mysqli_close($conn);
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    //check if the post request with name 'upload' is set
    if(isset($_POST['upload'])) {
        //check if the posted contractID is not empty
        if (!empty($_POST['contractID'])) {
            //check if the file name posted is not empty
            if (!empty($_FILES["uploadedFile"]["tmp_name"])) {
                //check if the contractId posted is an existing contractId saved in session(the session contains the
                //contractId(contract) used selected in admin panel page)
                if (in_array($_POST['contractID'], array_keys($_SESSION['acceptedContractId']))) {
                    //check if the contractID posted is a correct type and assign it to a variable
                    $contractId = filter_input(INPUT_POST, 'contractID',FILTER_SANITIZE_NUMBER_INT);
                    //call the function to upload the file(contract) selected with an contractId to be assigned
                    //The contractId is the id for the contract to be uploaded.
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
