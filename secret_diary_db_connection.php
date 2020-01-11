<?php
  $user = 'root';
  $password = '';
  $db = 'secret_diary_db';

  $link = mysqli_connect('localhost', $user, $password, $db);
  $error = mysqli_connect_error();
  if ($error) {
    //echo $error;
    die("Unable to connect");
  }

  //include('header.php');
?>
