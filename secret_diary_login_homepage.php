<?php

  session_start();
  if (isset($_COOKIE["userName"])) {
      $_SESSION['userName'] = $_COOKIE['userName'];
      echo 'Welcome to your account "'.$_COOKIE['userName'].'". You have chosen to stay logged in. Click the following link to log out: <a href="secret_diary.php?logout=1">Log Out</a>';
  } else if (isset($_SESSION["userName"])) {
      echo 'Welcome to your account "'.$_SESSION["userName"].'". You have chosen to NOT stay logged in. Click the following link to log out: <a href="secret_diary.php?logout=1">Log Out</a>';
  } else {
      header("Location: secret_diary.php");
  }

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fonts.css">
    <script src="js/popper.js"></script>
    <script src="js/bootstrap-jquery.js"></script>
    <script src="js/bootstrap.js"></script>
  </head>
  <style>

    html {
      background: url("secret_forest.jpg") no-repeat center center fixed;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }

    .container {
      margin-top: 3em;
      margin-left: auto;
      margin-right: auto;
      width: 60%;
    }

    body {
      background: none;
    }


    h1 {
      margin-bottom: 1em;
    }

  </style>
  <body>
    <div class="container">

    </div>
  </body>
</html>
