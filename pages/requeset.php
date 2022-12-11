<?
  class Database {
    public function __construct() {
      $hostname = "141.8.192.31";
      $username = "a0643156_test";
      $password = "123123";
      $name = "a0643156_test";
  
      $this->database = new mysqli($hostname, $username, $password, $name);
      
  
      if (!$this->database) {
        http_response_code(500);
        exit;
      }
    }
  
    public function query($query) {
      return $this->database->query($query);
    }
  }

  if (!empty($_POST['name'])) {
    $db = new Database;

    $result = $db->query("SELECT * FROM `user` WHERE `email` = '".quotemeta($_POST['email'])."'");

    $user = false;

    while ($row = $result->fetch_assoc()) {
      $user = $row['id'];
      break;
    }

    if ($user != false) {
      $result = $db->query("SELECT * FROM `contract` WHERE `user_id` = ".$user."");

      $check = false;

      while ($row = $result->fetch_assoc()) {
        $check = true;
        break;
      }

      if (!$check) {
        $db->query("INSERT INTO `contract` (`id`, `file_path`, `user_id`) VALUES (NULL, '', ".$user.");");
      }
      
      $db->query("INSERT INTO `service_request` (`id`, `status`, `description`, `service_type`, `name`, `email`, `user_id`, `admin_id`) VALUES (NULL, 'inProcess', '".$_POST['description']."', '".$_POST['service_type']."', '".$_POST['name']."', '".$_POST['email']."', ".$user.", 1);");
    }

    exit('ok');
  }
?>

<!DOCTYPE html>

<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forma</title>
    <link rel="stylesheet" href="index.css">
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  </head>

  <body>
    <div id="app" class="test">
      <form id="form" action="#" @submit.prevent='checkForm' class="wrapper">
        <div class="suptitle" v-cloak>
          Service IT
        </div>

        <div class="inner">
          <div class="message" :class="{'active':show_message}">
            Your ticket was submitted!

            <a href="#" class="button">Back Home</a>
          </div>

          <div class="inputs">
            <input name="name" required minlength="3" placeholder="Name and lastname" type="text">
            <input name="email" required  placeholder="Email (the one you entered during registration)" type="email">
          </div>
          
          <div class="block">
            <div class="block_title">
              Select avaiable options:
            </div>

            <div class="block_content">
              <div class="radio">
                <input required name="service_type" value="laptop repair" type="radio" class="radio_input">
                <div class="radio_text">laptop repair</div>
              </div>

              <div class="radio">
                <input required  name="service_type" value="phone repair" type="radio" class="radio_input">
                <div class="radio_text">phone repair</div>
              </div>

              <div class="radio">
                <input required  name="service_type" value="hardware / software problems" type="radio" class="radio_input">
                <div class="radio_text">hardware / software problems</div>
              </div>
            </div>
          </div>

          <div class="block">
            <div class="block_title">
              Describe the request
            </div>

            <div class="block_content two">
              <textarea name="description" required ></textarea>
              <button type="submit">Submit</button>
            </div>
          </div>
        </div>

        <div class="form_footer">
          <div class="links">
            <a href="#" class="link">Home</a>
            <a href="#" class="link">New Service</a>
            <a href="#" class="link">Ticket</a>
            <a href="#" class="link">Profile</a>
          </div>

          <div class="copyright">
            Copyright 2022 Service IT. All rights are reserved
          </div>
        </div>
      </form>
    </div>

    <script>
      let app = {
        data() {
          return {
            show_message: false,
          }
        },

        methods: {
          checkForm: function (e) {
            vueData = this;

            $.ajax({
              url: '/test/',
              method: 'post',
              dataType: 'html',
              data: $('#form').serialize(),
              success: function (data) {
                vueData.show_message = true;
              }
            });
          }
        }
      }

      Vue.createApp(app).mount("#app");
    </script>
  </body>
</html>