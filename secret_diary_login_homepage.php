<?php

  session_start();
  if (isset($_COOKIE["userName"])) {
      $_SESSION['userName'] = $_COOKIE['userName'];
      //echo 'Welcome to your account "'.$_COOKIE['userName'].'". You have chosen to stay logged in. Click the following link to log out: <a href="secret_diary.php?logout=1">Log Out</a>';
  }

  if (isset($_SESSION["userName"])) {
      //echo 'Welcome to your account "'.$_SESSION["userName"].'". You have chosen to NOT stay logged in. Click the following link to log out: <a href="secret_diary.php?logout=1">Log Out</a>';

      include("secret_diary_db_connection.php");

      $escapedUsername = mysqli_real_escape_string($link, $_SESSION["userName"]);
      $query = "SELECT * FROM `users` WHERE `Username` = '".$escapedUsername."'";
      $result = mysqli_query($link, $query);
      $error = mysqli_error($link);

      if ($result) {
        $row = mysqli_fetch_array($result);
        $diaryContent = $row["DiaryContent"];
      } else {
        echo $queryError;
      }
  } else {
      header("Location: secret_diary.php");
  }

?>
<!DOCTYPE html>
<html>
  <head><?php include('header.php'); ?></head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
        <a class="navbar-brand" href="#">Diary Content</a>
        <!--<ul class="navbar-nav mr-auto mt-2 mt-lg-0">
          <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Link</a>
          </li>
          <li class="nav-item">
            <a class="nav-link disabled" href="#">Disabled</a>
          </li>
        </ul> -->
      </div>
      <div class="pull-md-right">
          <strong>Logged In As:&nbsp;&nbsp;</strong> <?php echo $_SESSION["userName"]."&nbsp;&nbsp;&nbsp;&nbsp;"; ?>
          <strong>Stay Logged In&#63;&nbsp;&nbsp;</strong>
          <?php
            if (isset($_COOKIE["userName"])) {
              echo "Yes&nbsp;&nbsp;&nbsp;&nbsp;";
            } else {
              echo "No&nbsp;&nbsp;&nbsp;&nbsp;";
            }
          ?>
          <a href="secret_diary.php?logout=1" class="btn btn-outline-success my-2 my-sm-0">Log Out</a>
      </div>
    </nav>
    <div class="container-fluid" style="margin-top: 1em;">
      <?php
        //session_start();
        //include('navbar.php');
      ?>
      <textarea id="diary"><?php echo $diaryContent; ?></textarea>
    </div>
  </body>
  <?php include('footer.php'); ?>
</html>
