<?php
  $conn = new mysqli('localhost', 'admin', 'toor', 'test');

  if ($conn->connect_error) {
    die("Connection failed");
  }

  $userid = $_POST['userid'];
  $userpw = $_POST['userpw'];
  $username = $_POST['username'];
  $useremail = $_POST['useremail'];

  $sql = "insert into user (id, pw, name, email) values('$userid', '$userpw', '$username', '$useremail');";

  if ($conn->query($sql) === TRUE) {
    echo "Success";
    echo "<br>";
    echo "<input type='button' value='Back' onclick='main()'>";
  } else {
    echo "Please check your ID";
    echo "<br>";
    echo "<input type='button' value='Back' onclick='re()'>";
  }

  $conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <script>
      function main() {
        window.location.href = "./login.html";
      }
      function re() {
        window.location.href = "./signup.html";
      }
    </script>
  </body>
</html>
