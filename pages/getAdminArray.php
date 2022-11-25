
<?php
function getAdminArray() : array{
    $adminArray = array();


    // Step #1: Open a connection to MySQL...
    // Docker users need to use the service name (ex: mysql)
    $conn = mysqli_connect("localhost", "root", "");

    // And test the connection
    if(!$conn)
    {
        die("There was an error connecting to the database. Error: " . mysqli_connect_errno());
    }

    // Step #2: Selecting the database (assuming it has already been created)
    if(mysqli_select_db($conn, "ServiceIT"))
    {

        // Step #3: Create the query
        $query = "SELECT `id`, `name` FROM `administrator`";

        // Step #4.1: Prepare query as a statement
        if($statement = mysqli_prepare($conn, $query))
        {
            // Step #4.2: Fill in the ? parameters!
           // mysqli_stmt_bind_param($statement, 's', $keyword);

            //Step #5: Execute statement and check success
            if(!mysqli_stmt_execute($statement))
            {
                die(mysqli_error($conn));
            }
            //echo"<br><br>--------------<br><br>";

            // Step #6.1: Bind result to variables when fetching...
            mysqli_stmt_bind_result($statement, $id, $name);
            // Step #6.2: And buffer the result if and only if you want to check the number of rows
            mysqli_stmt_store_result($statement);

            //Create heading
            //echo "<h2>List of artists</h2>";

            // Step #7: Check if there are results in the statement
            if(mysqli_stmt_num_rows($statement) > 0)
            {
//                echo "Number of rows: " . mysqli_stmt_num_rows($statement);
                // Make table
//                echo "<table border='1'>";
//                // Make table header
//                echo "<th style='text-align: left;'>ID</th><th>NAME</th>";

                // Step #8: Fetch all rows of data from the result statement
                while (mysqli_stmt_fetch($statement))
                {
                    //$adminArray[] += $id;
                    $adminArray = array_merge( $adminArray , [$id=>$name]) ;
//                    // Create row
//                    echo "<tr>";
//
//                    // Create cells
//                    echo "<td>" . $id . "</td>";
//                    echo "<td>" . $name . "</td>";
//
//                    // Close row
//                    echo "</tr>";
                }
                // Close table
//                echo "</table>";
            }else{
                echo "No admins found";
            }
            // Step #9: Close the statement and free memory
            mysqli_stmt_close($statement);
        }else{
            die(mysqli_error($conn));
        }
    }else{
        die(mysqli_error($conn));
    }

    // Step #10: Close the connection!
    mysqli_close($conn);
    return $adminArray;
}


?>

