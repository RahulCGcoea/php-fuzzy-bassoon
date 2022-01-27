<!-- ; -->

<?php
require_once "config.php";

//to store each variable data from _post if correct
$reg_num = $password = $confirm_password = $first_name = $last_name = $email = $mobile = $gender = $dob = $que_num = $answer = "";
//error varibles for each field
$reg_num_err = $password_err = $confirm_password_err = $first_name_err = $last_name_err = $email_err = $mobile_err = $gender_err = $dob_err = $que_num_err = $answer_err = "";

function otherInfo()
{
  //Check for password
  if (empty(trim($_POST["password"]))) {
    $GLOBALS['password_err'] = "Password cannot be empty";
  } elseif (strlen(trim($_POST["password"])) < 8) {
    $GLOBALS['password_err'] = "Password cannot be less than 8 characters";
  } else {
    $GLOBALS['password'] = trim($_POST["password"]);
  }

  //check for confirm password field
  if (trim($_POST["confirm_password"]) != trim($_POST["password"])) {
    $GLOBALS['confirm_password_err'] = "Password dont match";
  }


  //check first name field
  $temp = trim($_POST["first_name"]);
  //empty string
  if (empty($temp)) {
    $GLOBALS['first_name_err']  = "First Name cannot be empty";
    //valid string 
  } elseif (!preg_match("/^[a-zA-z]*$/", $temp)) {
    $GLOBALS['first_name_err'] = "Only alphabets are allowed.";
    // echo $ErrMsg;  
  } else {
    $GLOBALS['first_name'] = $temp;
  }

  //check last name field
  //check valid string
  $temp_ln = trim($_POST["last_name"]);
  if (!preg_match("/^[a-zA-z]*$/", $temp_ln)) {
    $GLOBALS['last_name_err'] = "Only alphabets are allowed.";
    //echo $ErrMsg;  
  } else {
    $GLOBALS['last_name'] = $temp_ln;
  }
  //validate email
  $temp_email = trim($_POST["email"]);
  $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";
  if (empty($temp_email)) {
    $GLOBALS['email_err']  = "Email cannot be empty.";
  } else if (!preg_match($pattern, $temp_email)) {
    $GLOBALS['email_err'] = "Email is not valid.";
    //echo $ErrMsg;  
  } else {
    $GLOBALS['email'] = $temp_email;
  }

  //validate mobile number
  $mobileno = trim($_POST["mobile"]);
  if (empty($mobileno)) {
    $GLOBALS['mobile_err'] = "Mobile Number cannot be empty.";
  } elseif (!preg_match("/^[0-9]*$/", $mobileno)) {
    $GLOBALS['mobile_err'] = "Only numeric value is allowed.";
    //echo $ErrMsg;  
  } else if (strlen($mobileno) != 10) {
    $GLOBALS['mobile_err'] = "Mobile Number must contain 10 digits";
  } else {
    $GLOBALS['mobile'] = $mobileno;
  }
  //gender checks
  if (empty(trim($_POST["gender"]))) {
    $GLOBALS['gender_err'] = "Gender cannot be empty.";
  } elseif (strlen(trim($_POST["gender"])) != 1)
    $GLOBALS['gender_err'] = "Gender is of only 1 character.";
  else if (trim($_POST["gender"]) == 'M' || trim($_POST["gender"]) == 'F' || trim($_POST["gender"]) == 'O') {
    $GLOBALS['gender'] = trim($_POST["gender"]);
  } else {
    $GLOBALS['gender_err'] = "gender should be one of M/F/O";
  }
  //
  $input_date = $_POST['dob'];
  if (empty($input_date)) {
    $GLOBALS['dob_err'] = "Date of Birth cannot be empty.";
  }
  $GLOBALS['dob'] = date("Y-m-d H:i:s", strtotime($input_date));
  //echo $dob;

  //check for security question
  $temp_que_num = trim($_POST["que_num"]);
  //empty string
  if (empty($temp_que_num)) {
    $GLOBALS['que_num_err']  = "Security question cannot be empty.";
    //valid string 
  }else{
    $GLOBALS['que_num'] = $temp_que_num;
  }

  //answer check
  $temp_ans = trim($_POST["answer"]);
  //empty string
  if (empty($temp_ans)) {
    $GLOBALS['answer_err']  = "Answer cannot be empty, important for password recovery.";
    //valid string 
  } elseif (!preg_match("/^[a-zA-z]*$/", $temp_ans)) {
    $GLOBALS['answer_err'] = "Only alphabets are allowed.";
     
  } else {
    $GLOBALS['answer'] = $temp_ans;
  }
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
  //Check for empty username
  //
  if (empty(trim($_POST["reg_num"]))) {
    $reg_num_err = "Registration Number cannot be empty";
  } else {
    $sql = "SELECT sno FROM register WHERE reg_num = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
      mysqli_stmt_bind_param($stmt, "s", $param_reg_num);
      //Set the val of param_reg_num
      $param_reg_num = trim($_POST["reg_num"]);

      //Try to execute this statement
      if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
          $reg_num_err = "Registration Number already exists.";
        } else {
          $reg_num = trim($_POST["reg_num"]);
          otherInfo();
        }
      } else {
        echo "stmt not executed";
      }
    }
  }
  mysqli_stmt_close($stmt);



  //If no error till here and enter in database
  if (empty($que_num_err) && empty($answer_err) && empty($reg_num_err) && empty($password_err) && empty($confirm_password_err) && empty($first_name_err) && empty($email_err) && empty($mobile_err) && empty($gender_err) && empty($dob_err)) {
    // echo "not empty";
    $sql = "INSERT INTO register (reg_num,password,first_name,last_name,email,mobile,gender,dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $sql2 = "INSERT INTO forgotpassword (reg_num, que_num, answer) VALUES (?, ?, ?)";
    $stmt2 = mysqli_prepare($conn, $sql2);
    if ($stmt && $stmt2) {
      mysqli_stmt_bind_param($stmt, "ssssssss", $p_reg_num, $p_password, $p_first_name, $p_last_name, $p_email, $p_mobile, $p_gender, $p_dob);
      //set above farameters
      $p_reg_num = $reg_num;
      $p_password = password_hash($password, PASSWORD_DEFAULT);
      $p_first_name = $first_name;
      $p_last_name = $last_name;
      $p_email = $email;
      $p_mobile = $mobile;
      $p_gender = $gender;
      $p_dob = $dob;

      mysqli_stmt_bind_param($stmt2, "sss",$p_reg_num, $p_que_num,$p_answer);
      $p_que_num = $que_num;
      $p_answer = $answer;
      //execute
      if (mysqli_stmt_execute($stmt) && mysqli_stmt_execute($stmt2)) {
        header("location: login.php");
        //echo "execute";
      } else {
        echo "query execution failed";
      }
    }
    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt2);
  }
  mysqli_close($conn);
}
?>



<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  <title>PHP login system!</title>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#">Php Login System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item active">
          <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        
        <li class="nav-item active">
          <a class="nav-link" href="login.php">Login</a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="contact.php">Contact us</a>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container mt-4">
    <h3>Please Register Here:</h3>
    <hr>
    <form action="" method="post">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="reg_num">Registration Number</label>
          <input type="text" class="form-control" name="reg_num" id="reg_num" placeholder="eg. 2021SW39">
          <small id="display" class="form-text  text-danger"><?php if (!empty($reg_num_err)) {
                                                                echo "<b>" . $reg_num_err . "</b>";
                                                              } ?></small>
        </div>
        <div class="form-group col-md-6">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="password" id="password" placeholder="Password">
          <small id="display" class="form-text  text-danger"><?php if (!empty($password_err)) {
                                                                echo "<b>" . $password_err . "</b>";
                                                              } ?></small>
        </div>
      </div>
      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
        <small id="display" class="form-text  text-danger"><?php if (!empty($confirm_password_err)) {
                                                              echo "<b>" . $confirm_password_err . "</b>";
                                                            } ?></small>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="first_name">First Name</label>
          <input type="text" class="form-control" name="first_name" id="first_name" placeholder="eg. John">
          <small id="display" class="form-text  text-danger"><?php if (!empty($first_name_err)) {
                                                                echo "<b>" . $first_name_err . "</b>";
                                                              } ?></small>
        </div>
        <div class="form-group col-md-6">
          <label for="last_name">Last Name</label>
          <input type="text" class="form-control" name="last_name" id="last_name" placeholder="eg. Wick">
          <small id="display" class="form-text  text-danger"><?php if (!empty($last_name_err)) {
                                                                echo "<b>" . $last_name_err . "</b>";
                                                              } ?></small>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="email">Email</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="eg. sample@example.com">
          <small id="display" class="form-text  text-danger"><?php if (!empty($email_err)) {
                                                                echo "<b>" . $email_err . "</b>";
                                                              } ?></small>
        </div>
        <div class="form-group col-md-6">
          <label for="mobile">Mobile Number</label>
          <input type="text" class="form-control" name="mobile" id="mobile" placeholder="eg. 9845263556">
          <small id="display" class="form-text  text-danger"><?php if (!empty($mobile_err)) {
                                                                echo "<b>" . $mobile_err . "</b>";
                                                              } ?></small>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="gender">Gender</label>
          <input type="text" class="form-control" name="gender" id="gender" placeholder="eg. M">
          <small id="display" class="form-text  text-danger"><?php if (!empty($gender_err)) {
                                                                echo "<b>" . $gender_err . "</b>";
                                                              } ?></small>
        </div>
        <div class="form-group col-md-6">
          <label for="dob">Date of Birth</label>
          <input type="date" class="form-control" name="dob" id="dob" placeholder="eg. 28/02/2002">
          <small id="display" class="form-text  text-danger"><?php if (!empty($dob_err)) {
                                                                echo "<b>" . $dob_err . "</b>";
                                                              } ?></small>
        </div>
      </div>
      <div class="form-row">
      <div class="form-group col-md-6">
                <label for="que_num">Security Question</label>
                <select required name="que_num" id="que_num" class="form-check form-control" aria-describedby="emailHelp">
                    <option selected>Select security question</option>
                    <option value="1">What is your pet's name?</option>
                    <option value="2">What is your favourite sports?</option>
                    <option value="3">What is your favourite colour?</option>
                    <option value="4">What is your primary school name?</option>
                    <option value="5">What is your favourite actor/actress?</option>
                </select>
                <small id="display" class="form-text  text-danger"><?php if (!empty($que_num_err)) {
                                                                        echo "<b>" . $que_num_err . "</b>";
                                                                    } ?></small>
            </div>
            <div class="form-group col-md-6">
                <label for="answer">Answer</label>
                <input type="text" class="form-control" name="answer" id="answer" aria-describedby="emailHelp" placeholder="Answer for your security question">
                <small id="display" class="form-text  text-danger"><?php if (!empty($answer_err)) {
                                                                        echo "<b>" . $answer_err . "</b>";
                                                                    } ?></small>
            </div>
      </div>
      <div class="form-group">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="gridCheck">
          <label class="form-check-label" for="gridCheck">
            Check me out
          </label>
        </div>
      </div>
      <button type="submit" class="btn btn-primary">Register</button>
    </form>
  </div>

  <!-- Optional JavaScript -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>

</html>