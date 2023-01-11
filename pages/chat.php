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
    if ($_POST['method'] == 'get_messages') {
      $result = $conn->query("SELECT message.text, message.admin_id, user.name FROM `message` INNER JOIN user ON message.user_id = user.id WHERE message.user_id = ".$_SESSION['userId']."");

      $data = array();
        while ($row = $result->fetch_assoc()) {
          $data[] = $row;
        }

        $json = json_encode($data);

        echo $json;
      exit;
    } else if ($_POST['method'] == 'send_message') {
      $result = $conn->query("INSERT INTO `message` (`user_id`, `admin_id`, `text`) VALUES (".$_SESSION['userId'].", 0, '".sqlValue('text')."')");
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

	<body class="white chat_by_root">
		<form @submit.prevent="sendMessage" class="wrapper" id="app">
      <a href="/home.php" class="exit">Exit</a>

			<div class="messages">
				<div class="message" v-for="item in messages">
          <div class="author" v-if="item['admin_id'] == '0'">
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
            messages: [],
            input: "",
          }
        },

        methods: {
          getMessages() {
            let vue = this;

            $.ajax({
              url: '',
              method: 'post',
              dataType: 'html',
              data: "method=get_messages",
              success: function(data) {
                vue.messages = JSON.parse(data).reverse();
              }
            });
          },

          sendMessage() {
            let vue = this;

            $.ajax({
              url: '',
              method: 'post',
              dataType: 'html',
              data: "method=send_message&text=" + vue.input,
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