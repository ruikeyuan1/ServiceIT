<?php
// this page contains different functions that are called more than once in adminPanel.php, userProfile.php, userContract.php and
//admin contract.php

function getAdminArray() : array{
    //create an array for storing the result to be returned
    $adminArray = array();
    //load the php file for connecting database
    require 'connect.php';
    //Create the query(selecting all the existing admins in database)
    $query = "SELECT `id`, `name` FROM `administrator`";

    //Prepare query as a statement
    if ($statement = mysqli_prepare($conn, $query))
    {
        //Execute statement and check success
        if (!mysqli_stmt_execute($statement))
        {
            die(mysqli_error($conn));
        }

        //Bind result to variables when fetching...
        mysqli_stmt_bind_result($statement, $id, $name);
        //And buffer the result for checking the number of rows
        mysqli_stmt_store_result($statement);

        //Check if the number of rows fetched is correct
        if (mysqli_stmt_num_rows($statement) > 0)
        {
            while (mysqli_stmt_fetch($statement))
            {
                //assign the result(adminId with the name of the admin) to an associative array
                $adminArray = array_merge($adminArray , [$id=>$name]);
            }
        } else {
            echo "No admins found";
        }

        //Close the statement and free memory
        mysqli_stmt_close($statement);
    } else {
        die(mysqli_error($conn));
    }

    //Close the connection
    mysqli_close($conn);
    return $adminArray;
}

function getContractFileName($contractId) : string{
    //create an array for storing the result to be returned
    $fileNameFetched = "";
    //load the php file for connecting database
    require 'connect.php';
    //Create the query
    $query = " SELECT `file_path` FROM `contract` WHERE `id` = ?";

    //Prepare query as a statement
    if ($statement = mysqli_prepare($conn, $query))
    {
        //Fill in  ? parameters
        mysqli_stmt_bind_param($statement, 'i', $contractId);

        //Execute statement and check success
        if (!mysqli_stmt_execute($statement))
        {
            die(mysqli_error($conn));
        }

        //Bind result to variables when fetching...
        mysqli_stmt_bind_result($statement, $fileName);
        //And buffer the result for checking the number of rows
        mysqli_stmt_store_result($statement);

        //Check if the number of rows fetched is correct
        if (mysqli_stmt_num_rows($statement) == 1)
        {
            while (mysqli_stmt_fetch($statement))
            {
                //assign the result(fileName) to a new variable
                $fileNameFetched = $fileName;
            }
        } else {
            echo "error fetching the data.The data should be one row.";
        }

        //Close the statement and free memory
        mysqli_stmt_close($statement);
    } else {
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

//This is the dropDownBox function made for dropDown boxes in admin panel page and userprofile page
//The variable $valueToBeChecked is the value to be selected.The if-else statement checks if the $valueToBeChecked
//exists in the list of options to be displayed.
function dropDownBox($array,$valueToBeChecked){
    foreach ($array as $value) {
        if ($valueToBeChecked == $value) {
            echo "<option value=$valueToBeChecked selected>$valueToBeChecked</option>";
        }
        else {
            echo "<option value=$value>$value</option>";
        }
    }
}
