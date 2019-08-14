<?php
  session_start();
  $login = $_SESSION['id'];
  echo "<h2>Welcome, $login!</h2>";
  echo "<input type='button' value='Sign Out' onclick='signout()'>";
?>
<html>
<head>
  <meta charset="utf-8">
  <title>Main</title>
</head>
<body>
  <script>
    function signout() {
      alert("See ya!");
      <?php
        session_unset();
        session_destroy();
      ?>
      window.location.href = "./login.html";
    }
  </script>
</body>
</html>
