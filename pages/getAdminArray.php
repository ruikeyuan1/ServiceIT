<?php

function getAdminArray() : array{
    $adminArray = array();

    $conn = mysqli_connect("localhost", "root", "");

    if(!$conn)
    {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }

    if(mysqli_select_db($conn, "serviceIT"))
    {
        $query = "SELECT `id`, `name` FROM `administrator`";

        if($statement = mysqli_prepare($conn, $query))
        {
            if(!mysqli_stmt_execute($statement))
            {
                die(mysqli_error($conn));
            }

            mysqli_stmt_bind_result($statement, $id, $name);
            mysqli_stmt_store_result($statement);

            if(mysqli_stmt_num_rows($statement) > 0)
            {
                while (mysqli_stmt_fetch($statement))
                {
                    $adminArray = array_merge( $adminArray , [$id=>$name]) ;
                }
            }else{
                echo "No admins found";
            }

            mysqli_stmt_close($statement);
        }else{
            die(mysqli_error($conn));
        }
    }else{
        die(mysqli_error($conn));
    }

    mysqli_close($conn);
    return $adminArray;
}




