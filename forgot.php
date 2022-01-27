<?php

require_once "config.php";

$reg_num =  $que_num = $answer =  $password = $confirm_password = $password_update_status1 = $password_update_status2 = "";
$reg_num_err = $que_num_err = $answer_err =  $password_err = $confirm_password_err = "";

function ifRegNumExist()
{
    //security Question
    $temp_que_num = trim($_POST["que_num"]);
    //empty string
    if (empty($temp_que_num)) {
        $GLOBALS['que_num_err']  = "Security question cannot be empty.";
        //valid string 
    } else {
        $GLOBALS['que_num'] = $temp_que_num;
    }
    //answer
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
    }else{
        $GLOBALS['confirm_password'] = trim($_POST["password"]);
    }
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {

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
                    $reg_num = trim($_POST["reg_num"]);
                    ifRegNumExist();
                } else {
                    $reg_num_err = "Registration Number does not exists.";
                }
            } else {
                echo "stmt not executed";
            }
        }
    }
    mysqli_stmt_close($stmt);

    if (empty($reg_num_err) && empty($que_num_err) && empty($answer_err_err)) {

        $sql = "SELECT reg_num, que_num,answer FROM forgotpassword WHERE reg_num = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $p_reg_num);
            $p_reg_num = $reg_num;
            // try to execute this statement
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $db_reg_num, $db_que_num, $db_answer);
                    if (mysqli_stmt_fetch($stmt)) {

                        if ($db_reg_num == $reg_num && $db_que_num == $que_num && $db_answer == $answer && empty($password_err) && empty($confirm_password_err) ) {
                            //this means password can be updated as user validated with database values
                            $sql2 = "UPDATE register SET password = ? where reg_num = ?";
                            $stmt2 = mysqli_prepare($conn, $sql2);
                            if ($stmt2) {
                                mysqli_stmt_bind_param($stmt2, "ss", $p_password, $p_reg_num);
                                $p_password = password_hash($confirm_password, PASSWORD_DEFAULT);
                                $p_reg_num = $reg_num;
                                if (mysqli_stmt_execute($stmt2)) {
                                    $password_update_status1 = "Password updated successfully,head to Log in page";
                                } else {
                                    $password_update_status2 = "Password updation failed, check your security question and answer.";
                                }
                            } else {
                                echo "update query not prepared";
                            }
                            mysqli_stmt_close($stmt2);
                        } else {
                            echo "ERROR: user values not matched with database";
                        }
                    } else {
                        echo "ERROR: fetching statement ";
                    }
                } else {
                    header("location:register.php");
                }
            }
            mysqli_stmt_close($stmt);
        }else{
            echo "select query failed ";
        }
    }
}
mysqli_close($conn);


?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>PHP forgot page</title>
    <style>

    </style>
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
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="contact.php">Contact us</a>
                </li>
            </ul>
            <div class="navbar-collapse collapse">
                <ul class="navbar-nav ml-auto">

                    <li class="nav-item active">
                        <a class="btn btn-primary" href="login.php" role="button">Log in</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h3> Forgot Password:</h3>
        <hr>
        <form action="" method="post">
            <div class="form-group">
                <label for="reg_num">Registration Number</label>
                <input type="text" class="form-control" name="reg_num" id="reg_num" aria-describedby="emailHelp" placeholder="Registration Number">
                <small id="display" class="form-text  text-danger"><?php if (!empty($reg_num_err)) {
                                                                        echo "<b>" . $reg_num_err . "</b>";
                                                                    } ?></small>
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
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                    <small id="display" class="form-text  text-danger"><?php if (!empty($password_err)) {
                                                                            echo "<b>" . $password_err . "</b>";
                                                                        } ?></small>
                </div>

                <div class="form-group col-md-6">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                    <small id="display" class="form-text  text-danger"><?php if (!empty($confirm_password_err)) {
                                                                            echo "<b>" . $confirm_password_err . "</b>";
                                                                        } ?></small>
                </div>
            </div>
            <div class="form-check my-1">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
            </div>
            <button type="submit" class="btn btn-primary my-1">Reset Password</button>
            <small id="display" class="form-text  text-success"><?php if (!empty($password_update_status1)) {
                 echo "<b>" . $password_update_status1 . "</b>";
                } ?></small>
                <small id="display" class="form-text  text-danger"><?php if (!empty($password_update_status2)) {
                 echo "<b>" . $password_update_status2 . "</b>";
                } ?></small>
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