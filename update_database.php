<?php
  session_start();
  if (array_key_exists("content", $_POST)) {
    //echo $_POST["content"];
    include("secret_diary_db_connection.php");

    $escapedContent = mysqli_real_escape_string($link, $_POST["content"]);
    $escapedUsername = mysqli_real_escape_string($link, $_SESSION["userName"]);
    $query = "UPDATE `users` SET `DiaryContent` = '".$escapedContent."' WHERE `Username` = '".$escapedUsername."' LIMIT 1";
    $result = mysqli_query($link, $query);
    $queryError = mysqli_error($link);
    if ($result) {
      $row = mysqli_affected_rows($link);
      if ($row > 0) {
        echo "Diary Content Update Successful";
      } else {
        echo "Diary Content Update Failed";
      }
    } else {
      echo $queryError;
    }
  }

?>
