<?php

function getAdminArray() : array{
    //create an array for storing the result to be returned
    $adminArray = array();
    //load the php file for connecting database
    require 'databaseConnect.php';

    //Create the query(selecting all the existing admins in database)
    $query = "SELECT `id`, `name` FROM `administrator`";

    //Prepare query as a statement
    if($statement = mysqli_prepare($conn, $query))
    {
        //Execute statement and check success
        if(!mysqli_stmt_execute($statement))
        {
            die(mysqli_error($conn));
        }

        //Bind result to variables when fetching...
        mysqli_stmt_bind_result($statement, $id, $name);
        //And buffer the result for checking the number of rows
        mysqli_stmt_store_result($statement);

        //Check if the number of rows fetched is correct
        if(mysqli_stmt_num_rows($statement) > 0)
        {
            while (mysqli_stmt_fetch($statement))
            {
                //assign the result(adminId with the name of the admin) to an associative array
                $adminArray = array_merge($adminArray , [$id=>$name]) ;
            }
        }else{
            echo "No admins found";
        }

        //Close the statement and free memory
        mysqli_stmt_close($statement);
    }else{
        die(mysqli_error($conn));
    }


    //Close the connection
    mysqli_close($conn);
    return $adminArray;
}




