<?php
  session_start();

  if (!isset($_SESSION['userId'])) {
    header('Location: /loginPage.php');
    exit;
  }

  require_once "connect.php";

  function sqlValue($value) {
    global $conn;
    return mysqli_real_escape_string($conn, $_POST[$value]);
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		mail($_POST['emai'], "Service IT - Thanks for the Service Request!", "Thank you for the Service Request."); 
    $sql = "INSERT INTO `service_request` (`user_id`, `status`, `description`, `service_type`, `admin_id`) VALUES (".$_SESSION['userId'].", 'InProgress', '".sqlValue('text')."', '".sqlValue('type')."', 0)";
    $result = mysqli_query($conn, $sql);
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
  }

  function checkUserContract($id) {
    global $conn;

    $check_contract = mysqli_query($conn, "SELECT * FROM `contract` WHERE `user_id` = ".$id."");

    if (mysqli_num_rows($check_contract) > 0) {
      return true;
    } else {
      return false;
    }

    mysqli_close($conn);
  }
?>

<!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel = "stylesheet" href="stylesheet.css" type="text/css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  </head>

  <body class="by_root">
    <div id="wrapper4">
			<header>
				Service IT
			</header>

			<main>
				<div class="title">
					Request for a new service
				</div>

				<form class="items_wrapper" method="post">
					<div class="items_inner">
						<input class="input" type="text" name="name" required placeholder="Enter your name and lastname">
						<input class="input" type="email" name="email" required placeholder="Enter your Email">
					</div>

					<div class="items_inner">
						<div class="inner_title">
							Select avaiable options:
						</div>

						<div class="checkboxes">
							<label class="checkbox"><input type="radio" name="type" checked value="laptop repair">Laptop repair</label>
							<label class="checkbox"><input type="radio" name="type" value="phone repair">Phone repair</label>
							<label class="checkbox"><input type="radio" name="type" value="hardware / software problems">hardware / software problems</label>
						</div>	
					</div>

					<div class="items_inner">
						<div class="inner_title">
							Describe the request
						</div>

						<textarea class="textarea" name="text" required></textarea>
						<button class="button">Submit</button>
					</div>
				</form>
			</main>


			<footer>
				<div class="navigation">
					<a href="home.php" class="link active">Home</a>
					<a href="Ticket.php" class="link">New Service</a>
					<a href="Ticket.php" class="link">Ticket</a>
					<a href="userProfile.php" class="link">Profile</a>
          <a href="Contacttform.php" class="link">ContactUs</a>
				</div>

				<div class="copyright">
					Copyright (c) 2022 Service IT. All rights are reserved
				</div>
			</footer>
		</div>
  </body>
</html>