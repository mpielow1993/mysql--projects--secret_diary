<?php

  session_start();
  $signupError = "";
  $signupSuccess = "";
  $signupErrorCounter = 0;

  $loginError = "";
  $loginSuccess = "";
  $loginErrorCounter = 0;

  if (array_key_exists('logout', $_GET)) {
      //echo "logout working";
      unset($_SESSION["userName"]);
      setcookie("userName", "", time() - 60 * 60);
  //$_COOKIE['userName'] = "";
  } elseif ((array_key_exists('userName', $_SESSION) and $_SESSION['userName']) or (array_key_exists('userName', $_COOKIE) and $_COOKIE['userName'])) {
      //echo "else if executed instead";
      header("Location: secret_diary_login_homepage.php");
  }

  //userSignUp key is the base key
  if (array_key_exists("userSignUp", $_POST)) {
      //print_r($_POST);

      //Check userSignUp value
      if ($_POST["userSignUp"] == "1") {
          $attributes = array(
        "signupUsername" => $_POST['signupUsername'],
        "signupPassword" => $_POST['signupPassword'],
        "signupPasswordConfirmation" => $_POST['signupPasswordConfirmation']
      );
      } elseif ($_POST["userSignUp"] == "0") {
          $attributes = array(
        "loginUsername" => $_POST["loginUsername"],
        "loginPassword" => $_POST["loginPassword"]
      );
      }

      //Create the attributes array based on what parameters are created
      foreach ($attributes as $key => $value) {
          //remember that 'stayLoggedIn' can be
          if ($value == '') {
              if ($_POST["userSignUp"] == "1") {
                  $signupError = "".$signupError."<p>".ucwords(preg_replace('/[A-Z]/', " $0", $key))." cannot be empty</p>";
                  ++$signupErrorCounter;
              } else {
                  $loginError = "".$loginError."<p>".ucwords(preg_replace('/[A-Z]/', " $0", $key))." cannot be empty</p>";
                  ++$loginErrorCounter;
              }
              //echo $error;
          } else {
              //check the db that username selected is not already in use
              if ($key == "signupUsername") {

                  include('secret_diary_db_connection.php');

                  $_signupUsername = mysqli_real_escape_string($link, $value);
                  $query = "SELECT `Username` FROM `users` WHERE `Username` = '".$_signupUsername."'";
                  $result = mysqli_query($link, $query);
                  $rows = mysqli_num_rows($result); //number of rows returned
                  //print_r($rows);
                  if ($rows > 0) {
                      $signupError = $signupError."<p>Username '".$value."' is already in use</p>";
                      ++$signupErrorCounter;
                  }
                  mysqli_close($link);
              }
          }
      }

      //check for signupPassword and signupPasswordConfirmation keys
      if (array_key_exists("signupPassword", $attributes) && array_key_exists("signupPasswordConfirmation", $attributes)) {
          if ($_POST["signupPassword"] != $_POST["signupPasswordConfirmation"]) {
              $signupError = $signupError."<p>'Password' &amp; 'Password Confirmation' must match</p>";
              ++$signupErrorCounter;
          }
      }

      //evaluate the error and errorCounter
      if ($_POST["userSignUp"] == "1") {
          if ($signupError == "" && $signupErrorCounter == 0) {

              //echo "<p>Signup Successful</p>";
              include('secret_diary_db_connection.php');

              $escapedAttributes = array();
              foreach ($attributes as $key => $value) {
                  $escapedAttributes[$key] = mysqli_real_escape_string($link, $value);
                  //echo $attributes[$key];
              }

              $query = "INSERT INTO `users` (`Username`, `Password`) VALUES ('".$escapedAttributes["signupUsername"]."', '".$escapedAttributes["signupPassword"]."')";
              //echo $query;

              $result = mysqli_query($link, $query);
              $queryError = mysqli_error($link);
              if ($result) {
                  $passwordSalt = hash("sha256", (mysqli_insert_id($link)).$escapedAttributes['signupPassword'], false);
                  $passwordHash = hash("sha256", $passwordSalt, false);
                  //echo $passwordHash."<br>";
                  //echo mysqli_insert_id($link);
                  $query = "UPDATE `users` SET `Password` = '".$passwordHash."' WHERE `UserID` = ".mysqli_insert_id($link)." LIMIT 1";
                  //provides the most recently inserted if in the database
                  //mysqli_insert_id();
                  $result = mysqli_query($link, $query);
                  $queryError = mysqli_error($link);
                  if ($result) {
                      //$row = mysqli_fetch_array($result);
                      //print_r($row);

                      $signupSuccess = '<div class="alert alert-success">Signed up successfully as "'.$escapedAttributes["signupUsername"].'"</div>';

                      $_SESSION['userName'] = $escapedAttributes["signupUsername"];

                      if (array_key_exists("stayLoggedInSignUp", $_POST)) {
                          setcookie("userName", $row["Username"], time() + 60 * 60 * 24);
                      }

                      header("Location: secret_diary_login_homepage.php");
                  } else {
                      echo $queryError;
                  }
              } else {
                  echo $queryError;
              }
              mysqli_close($link);

          //session_start();
            //if ($_POST["userSignUp"] == "1") {
          } else {
              $signupError = '<div class="alert alert-danger"><p><strong>The Signup Form contains '.$signupErrorCounter.' error(s):</strong></p>'.$signupError.'</div>';
          }
      } elseif ($_POST["userSignUp"] == "0") {
          if ($loginError == "" && $loginErrorCounter == 0) {

              include('secret_diary_db_connection.php');

              $escapedAttributes = array();
              foreach ($attributes as $key => $value) {
                  $escapedAttributes[$key] = mysqli_real_escape_string($link, $value);
                  //echo $attributes[$key];
              }

              $query = "SELECT * FROM `users` WHERE `Username` = '".$escapedAttributes["loginUsername"]."' LIMIT 1";
              $result = mysqli_query($link, $query);
              $queryError = mysqli_error($link);

              if ($result) {
                  $row = mysqli_fetch_array($result);
                  $passwordSalt = hash("sha256", $row["UserID"].$escapedAttributes['loginPassword'], false);
                  $passwordHash = hash("sha256", $passwordSalt, false);
                  //echo $passwordSalt."<br>";
                  //echo $passwordHash;

                  //print_r($row);
                  $query = "SELECT * FROM `users` WHERE `Username` = '".$escapedAttributes["loginUsername"]."' AND `Password` = '".$passwordHash."' LIMIT 1";
                  $result = mysqli_query($link, $query);
                  $numberOfRows = mysqli_num_rows($result);
                  $queryError = mysqli_error($link);

                  if ($numberOfRows > 0) {
                      $row = mysqli_fetch_array($result);
                      $loginSuccess = '<div class="alert alert-success"> Logged in as "'.$row["Username"].'"</div>';
                      //echo $result;

                      //$loginSuccess = '<div class="alert alert-success">Login Successful</div>';
                      //$_SESSION['userID'] = $row["UserID"];
                      $_SESSION['userName'] = $row["Username"];

                      if (array_key_exists("stayLoggedInLogIn", $_POST)) {
                          //setcookie("userID", $escapedAttributes["loginUsername"], time() + 60 * 60 * 24);
                          //setcookie("userID", $row["UserID"], time() + 60 * 60 * 24);
                          setcookie("userName", $row["Username"], time() + 60 * 60 * 24);
                      }

                      header("Location: secret_diary_login_homepage.php");
                  } else {
                      //echo $queryError;
                      ++$loginErrorCounter;
                      $loginError = '<div class="alert alert-danger"><p><strong>The Login Form contains '.$loginErrorCounter.' error(s):</strong></p>Incorrect Username&#47;Password Combination</div>';
                  }
              } else {
                  echo $queryError;
              }
              mysqli_close($link);
          } else {
              $loginError = '<div class="alert alert-danger"><p><strong>The Login Form contains '.$loginErrorCounter.' error(s):</strong></p>'.$loginError.'</div>';
          }
      }
  }

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include('header.php'); ?>
  </head>
  <body>
    <div class="container home-page">
      <h1>Secret Diary</h1>
      <p style="color: #FFFFFF;">Store your thoughts permanently and securely</p>
      <p style="color: #FFFFFF;">Interested? Sign Up now!</p>
      <fieldset id="signupFieldset">
        <div id="errorMessage"><?php echo $signupError.$signupSuccess; ?></div>
        <form action="secret_diary.php" method="post" id="signupForm">
          <div class="form-group">
            <input type="text" name="signupUsername" id="signupUsername" placeholder="Username" class="form-control">
          </div>
          <div class="form-group">
            <input type="password" name="signupPassword" id="signupPassword" placeholder="Password" class="form-control">
          </div>
          <div class="form-group">
            <input type="password" name="signupPasswordConfirmation" id="signupPasswordConfirmation" placeholder="Password Confirmation" class="form-control">
          </div>
          <div class="checkbox">
            <input type="checkbox" name="stayLoggedInSignUp" id="stayLoggedSignUp">
            <label for="stayLoggedSignUp">Stay Logged In</label>
          </div>
          <input type="hidden" name="userSignUp" value="1">
          <div class="form-group center">
            <input type="submit" name="signUp" value="Sign Up" id="signUp" class="btn btn-success">
          </div>
        </form>
      </fieldset>
      <fieldset id="loginFieldset">
        <div id="errorMessage"><?php echo $loginError.$loginSuccess; ?></div>
        <form action="secret_diary.php" method="post" id="loginForm">
          <div class="form-group">
            <input type="text" name="loginUsername" id="loginUsername" placeholder="Username" class="form-control">
          </div>
          <div class="form-group">
            <input type="password" name="loginPassword" id="loginPassword" placeholder="Password" class="form-control">
          </div>
          <input type="hidden" name="userSignUp" value="0">
          <div class="checkbox">
            <input type="checkbox" name="stayLoggedInLogIn" id="stayLoggedInLogIn">
            <label for="stayLoggedInLogIn">Stay Logged In</label>
          </div>
          <div class="form-group center">
            <input type="submit" name="logIn" value="Log In" id="logIn" class="btn btn-success">
          </div>
        </form>
      </fieldset>
      <div style="margin-top: 2em;">
        <p style="color: #FFFFFF;"><button id="showSignUp" class="btn btn-primary">Sign Up</button> or <button id="showLogIn">Log In</button></p>
      </div>
    </div>
  </body>
  <?php include('footer.php'); ?>
</html>
