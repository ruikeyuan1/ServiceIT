<?php
  session_start();

  if (!isset($_SESSION['userId'])) {
    header('Location: /loginPage.php');
    exit;
  }

  function checkAdminLoginStatus(){
    if (isset($_GET['page'])){
        unset($_SESSION ['adminId']);
        $_SESSION["adminLoggedin"] = false;
        header("Location: adminLog.php");
    } else {
        if (!isset($_SESSION['adminId'])){
            $_SESSION["adminLoggedin"] = false;
            header("Location: adminLog.php");
        }
    }
}

  checkAdminLoginStatus();
  
  require_once "connect.php";

  function sqlValue($value) {
    global $conn;
    return mysqli_real_escape_string($conn, $_POST[$value]);
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['method'] == 'get_messages') {
      $result = $conn->query("SELECT message.*, user.name FROM `message` INNER JOIN user ON message.user_id = user.id WHERE `user_id` = ".$_POST['user_id']."");
      $data = array();
        while ($row = $result->fetch_assoc()) {
          $data[] = $row;
        }

        $json = json_encode($data);

        echo $json;
      exit;
    } else if ($_POST['method'] == 'send_message') {
      $result = $conn->query("INSERT INTO `message` (`user_id`, `admin_id`, `text`) VALUES (".$_POST['user_id'].", ".$_SESSION['userId'].", '".sqlValue('text')."')");
    } else if ($_POST['method'] == 'get_messages_list') {
      $conn->query("SET @@SESSION.sql_mode = '';");
      $result = $conn->query("SELECT message.*, user.name FROM message INNER JOIN user ON message.user_id = user.id GROUP BY `user_id`");
      printf($conn->error);

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
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
		<link rel="stylesheet" href="stylesheet.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
	</head>

	<body class="chat_by_root">
		<form @submit.prevent="sendMessage" class="wrapper" id="app">
      <a href="/adminPanel.php" class="exit">Exit</a>

      <div class="messages_list active">
        <div class="item" v-for="item in messages_list" :class="{'active': item['user_id'] == user_id}" @click.prevent="setUserID(item['user_id'])">
          {{ item['name'] }}
        </div>
			</div>

			<div class="messages">
				<div class="message" v-for="item in messages">
					<div class="author" v-if="item['admin_id'] == 0">
						{{ item['name'] }}
					</div>

          <div class="author" v-else>
						Administrator
					</div>

					<div class="text">
            {{ item['text'] }}
					</div>
				</div>
			</div>

			<div class="inputs">
				<input type="text" v-model="input" placeholder="Text message" required>
				<button type="submit">SEND</button>
			</div>
		</form>

    <script>
      let app = {
        data() {
          return {
            user_id: 0,
            messages: [],
            input: "",
            messages_list: []
          }
        },

        methods: {
          setUserID(id) {
            this.user_id = id;
            this.getMessages();
          },

          getMessages() {
            let vue = this;

            $.ajax({
              url: '',
              method: 'post',
              dataType: 'html',
              data: "method=get_messages&user_id=" + vue.user_id,
              success: function(data) {
                vue.messages = JSON.parse(data).reverse();
              }
            });

            $.ajax({
              url: '',
              method: 'post',
              dataType: 'html',
              data: "method=get_messages_list",
              success: function(data) {
                vue.messages_list = JSON.parse(data).reverse();
              }
            });
          },

          sendMessage() {
            let vue = this;

            $.ajax({
              url: '',
              method: 'post',
              dataType: 'html',
              data: "method=send_message&text=" + vue.input + "&user_id=" + vue.user_id,
              success: function(data) {
                vue.getMessages();
                vue.input = "";
              }
            });
          }
        },

        mounted() {
          let vue = this;
          this.getMessages();

          setInterval(vue.getMessages, 500);
        },
      }

      Vue.createApp(app).mount("body");
    </script>
	</body>
</html>