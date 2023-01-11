<?php
  session_start();

  if (!isset($_SESSION['userId'])) {
    header('Location: /loginPage.php');
    exit;
  }

  session_start();
  
  require_once "connect.php";

  function sqlValue($value) {
    global $conn;
    return mysqli_real_escape_string($conn, $_POST[$value]);
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['method'] == 'get_users') {
			$sort = "id";
			$sort_type = "ASK";

			if (isset($_POST['sort']) && isset($_POST['sort_type'])) {
				$sort = $_POST['sort'];
				$sort_type = $_POST['sort_type'];

				if ($sort_type == "true") {
					$sort_type = "DESC";
				} else {
					$sort_type = "ASC";
				}
			}
			
      $result = $conn->query("SELECT user.*, COUNT(service_request.id) as request_count FROM user LEFT JOIN service_request ON service_request.user_id = user.id GROUP BY user.id ORDER BY ".$sort." ".$sort_type."");

      $data = array();
        while ($row = $result->fetch_assoc()) {
          $data[] = $row;
        }

        $json = json_encode($data);

        echo $json;
    }

    exit;
  }
?>

<!DOCTYPE html>

<html lang="en">
	<head>
		<title>Service IT</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" href="stylesheet.css">
		<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	</head>

	<body class="users_by_root">
		<div class="wrapper">
			<a href="/adminPanel.php" class="title">
				Admin Panel
			</a>

			<div class="items_title">
				<div class="item_title" @click="getUsers('user.id')">ID</div>
				<div class="item_title" @click="getUsers('user.username')">Username</div>
				<div class="item_title" @click="getUsers('user.name')">Name</div>
				<div class="item_title" @click="getUsers('user.email')">Email</div>
				<div class="item_title" @click="getUsers('request_count')">Requests</div>
			</div>

			<div class="items">
				<div class="item" v-for="item in users">
					<div class="value">{{ item['id'] }}</div>
					<div class="value">{{ item['username'] }}</div>
					<div class="value">{{ item['name'] }}</div>
					<div class="value">{{ item['email'] }}</div>
					<div class="value">{{ item['request_count'] }}</div>
				</div>
			</div>
		</div>

		<script>
			let app = {
				data() {
					return {
						sort_type: true,
						sort: "user.id",
						users: [],
					}
				},

				methods: {
					getUsers(sort="user.id") {
						let vue = this;
						
						if (sort == this.sort) {
							this.sort_type = ! this.sort_type;
						} else {
							this.sort = sort;
							this.sort_type = true;
						}

						$.ajax({
							url: '',
							method: 'post',
							dataType: 'html',
							data: `method=get_users&sort=${vue.sort}&sort_type=${vue.sort_type}`,
							success: function(data) {
								vue.users = JSON.parse(data);
							}
						});
					}
				},

				mounted() {
					this.getUsers();
				}
			}

			Vue.createApp(app).mount("body");
		</script>
	</body>
</html>