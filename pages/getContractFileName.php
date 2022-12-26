<?php
function getContractFileName($contractId) : string{
    //create an array for storing the result to be returned
    $fileNameFetched = "";
    //load the php file for connecting database
   require 'connect.php';
    //Create the query
    $query = " SELECT `file_path` FROM `contract` WHERE `id` = ?";

    //Prepare query as a statement
    if($statement = mysqli_prepare($conn, $query))
    {
        //Fill in  ? parameters
        mysqli_stmt_bind_param($statement, 'i', $contractId);

        //Execute statement and check success
        if(!mysqli_stmt_execute($statement))
        {
            die(mysqli_error($conn));
        }

        //Bind result to variables when fetching...
        mysqli_stmt_bind_result($statement, $fileName);
        //And buffer the result for checking the number of rows
        mysqli_stmt_store_result($statement);

        //Check if the number of rows fetched is correct
        if(mysqli_stmt_num_rows($statement) == 1)
        {
            while (mysqli_stmt_fetch($statement))
            {
                //assign the result(fileName) to a new variable
                $fileNameFetched = $fileName;
            }
        }else{
            echo "error fetching the data.The data should be one row.";
        }

        //Close the statement and free memory
        mysqli_stmt_close($statement);
    }else{
        die(mysqli_error($conn));
    }

    //Close the connection
    mysqli_close($conn);
    //check if the variable is null, if so an empty string will be assigned.
    //The empty string will be checked when displaying the contract file in contract pages
    if($fileNameFetched == null){
        return " ";
    }
    //return the filename fetched
    return $fileNameFetched;
}