<?php

function getContractFileName($contractId) : string{
    $filePathFetched = "";
    $conn = mysqli_connect("localhost", "root", "");

    if(!$conn)
    {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }

    if(mysqli_select_db($conn, "serviceIT"))
    {
        $query = " SELECT `file_path` FROM `contract` WHERE `id` = ?";

        if($statement = mysqli_prepare($conn, $query))
        {
            mysqli_stmt_bind_param($statement, 'i', $contractId);

            if(!mysqli_stmt_execute($statement))
            {
                die(mysqli_error($conn));
            }

            mysqli_stmt_bind_result($statement, $filePath);

            mysqli_stmt_store_result($statement);

            if(mysqli_stmt_num_rows($statement) == 1)
            {
                while (mysqli_stmt_fetch($statement))
                {
                    $filePathFetched = $filePath;
                }

            }else{
                echo "No files found";
            }

            mysqli_stmt_close($statement);
        }else{
            die(mysqli_error($conn));
        }
    }else{
        die(mysqli_error($conn));
    }

    mysqli_close($conn);
    return $filePathFetched;
}