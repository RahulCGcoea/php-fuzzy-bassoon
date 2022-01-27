<?php

session_start();

//check if the user is already logged in

if (isset($_SESSION['reg_num'])) {
  header("location:welcome.php");
  exit(1);
}

require_once "config.php";

$reg_num = $password = "";
$reg_num_err = $password_err = "";

//if request method is post
if ($_SERVER['REQUEST_METHOD'] == "POST") {
  if (empty(trim($_POST["reg_num"]))) {
    $reg_num_err = "Please enter your Registration Number";
  } else {
    $reg_num = trim($_POST["reg_num"]);
  }

  if (empty(trim($_POST["password"]))) {
    $password_err = "Please enter your password";
  } else {
    $password = trim($_POST["password"]);
  }


  if (empty($reg_num_err) && empty($password_err)) {
    $sql = "SELECT sno, reg_num, password, first_name,last_name,user_type FROM register WHERE reg_num = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "s", $p_reg_num);
      $p_reg_num = $reg_num;
      // try to execute this statement
      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
          mysqli_stmt_bind_result($stmt, $sno, $reg_num, $hashed_password, $first_name, $last_name,$user_type);

          if (mysqli_stmt_fetch($stmt)) {
            if (password_verify($password, $hashed_password)) {
              //this means password valid
              session_start();
              $_SESSION["reg_num"] = $reg_num;
              $_SESSION["sno"] = $sno;
              $_SESSION["loggedin"] = true;
              $_SESSION["first_name"] = $first_name;
              $_SESSION["last_name"] = $last_name;
              $_SESSION["user_type"] = $user_type;

              if($user_type == "student"){
                //redirect user to welcome page
                header("location:welcome.php");
              }else if($user_type == "admin"){
                session_destroy();
                header("location:/phpmyadmin/index.php");
              } 
            }
          }
        }else{
          header("location:register.php");
        }
      }
    }
  }
}








?>
<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

  <title>PHP login page</title>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Php Login System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ml-auto">
        <!-- <li class="nav-item active">
          <a class="nav-link" href="#">Contact us</a>
        </li> -->
        <li class="nav-item active">
            <a class="nav-link" href="contact.php">Contact us</a>
          </li>
      </ul>
      <div class="navbar-collapse collapse">
        <ul class="navbar-nav ml-auto">
          
          <li class="nav-item active">
            <a class="btn btn-primary" href="Register.php" role="button">Register</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container mt-4">
    <h3>Please Login Here:</h3>
    <hr>
    <form action="" method="post">
      <div class="form-group">
        <label for="reg_num">Registration Number</label>
        <input type="text" class="form-control" name="reg_num" id="reg_num" aria-describedby="emailHelp" placeholder="Registration Number">
        <small id="display" class="form-text  text-danger"><?php if (!empty($reg_num_err)) {
                                                              echo "<b>" . $reg_num_err . "</b>";
                                                            } ?></small>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
        <small id="display" class="form-text text-danger"><?php if (!empty($password_err)) {
                                                            echo "<b>" . $password_err . "</b>";
                                                          } ?></small>
      </div>
      <div class="form-check">
        <input type="checkbox" class="form-check-input" id="exampleCheck1">
        <label class="form-check-label" for="exampleCheck1">Check me out</label>
      </div>
      <button type="submit" class="btn btn-primary my-1">Login</button>
      <a class="btn btn-primary my-1" href="forgot.php" role="button">Forgot Password</a>
    </form>
    <div class="container">
    
    </div>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>