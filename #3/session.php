<?php
  session_start();

  $id = $_POST['userid'];
  $pw = $_POST['userpw'];

  $conn = new mysqli('localhost', 'admin', 'toor', 'test');

  $sql = "select * from user where id='$id'";
  $result = $conn->query($sql);

  if($result->num_rows > 0) {
    $row = $result->fetch_array();
    if($row['pw'] == $pw) {
      $_SESSION['id'] = $id;
      if(isset($_SESSION['id'])) {
        header('Location: ./main.php');
      }
      else echo "Login Failed";
    }
    else {
      echo "<script>alert('Check the ID and PW');</script>";
      echo "<script>window.location.href = './login.html';</script>";
    }
  }
?>
