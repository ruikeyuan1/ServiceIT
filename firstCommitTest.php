<?php
echo "Hello ServiceIT I am Branch Rick!";

$conn = mysqli_connect("localhost", "root", "")
or die("Could not connect to the database!");
	if ($conn) {
        testCreateTable($conn);
        echo "Connection OK";
	}

function testCreateTable($conn){
    if(isset($_POST["littlePost"])) {
        mysqli_select_db($conn, "ServiceIT");

        $sql = "CREATE TABLE student
			(
		    	studentId int not null primary key,
				surname varchar (25),
				firstname varchar (25)
			)";
        echo "jaja";
        $stmt = mysqli_prepare($conn, $sql)
        OR DIE("Preparation Error");
        mysqli_stmt_execute($stmt)
        OR DIE(mysqli_error($conn));

        mysqli_stmt_close($stmt);
        mysqli_close($conn);
    }
}


?>