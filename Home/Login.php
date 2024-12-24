<?php
session_start();
include 'Connection.php';
$errorMsg = $role = "";
$formSubmit = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $Uname = $pass = "";
  $isValid = true;
  if (empty($_POST["email"]) && empty($_POST["pass"])) {
    $errorMsg = "Please enter your email or password.";
    echo "<script> hideErrorAlert();</script>";
    $isValid = false;
  }
  if ($isValid) {
    $loginemail = $conn->real_escape_string($_POST['email']);
    $loginpass = $conn->real_escape_string($_POST['pass']);
    $role = isset($_POST["verify"]) ? true : false;
    if ($role == 1) {
      $sql = "SELECT * FROM students WHERE student_email=? ";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $loginemail);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pass = $row['student_password'];
        $email = $row['student_email'];
        $role = $row['Role'];

        if ($email == $loginemail && $loginpass == $pass) {
          $_SESSION['login'] =1;
          $_SESSION['s_name'] = $row['student_name'];
          $_SESSION['s_id'] = $row['student_rollno'];
          $_SESSION['s_dpt_id'] = $row['dpt_id'];
          header("Location: ../Student/SDashboard.php");
        } else {
          $errorMsg = "Your username or password is incorrect.";
        }
      } else {
        $errorMsg = "User not found";
        //  header("Location: Login.php");
      }
      $stmt->close();
      $conn->close();
    } else {
      $sql = "SELECT * FROM teachers WHERE teacher_email=? ";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $loginemail);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pass = $row['teacher_password'];
        $email = $row['teacher_email'];
        $role = $row['role'];


        if ($email == $loginemail && $loginpass == $pass) {
          $_SESSION['login'] =1;
          if ($role == 1) {
            $_SESSION['a_name'] = $row['teacher_name'];
            $_SESSION['a_id'] = $row['teacher_id'];
            $_SESSION['a_dpt_id'] = $row['dpt_id'];
            header("Location: ../SuperAdmin/SADashboard.php");
          } else {
            $_SESSION['name'] = $row['teacher_name'];
            $_SESSION['id'] = $row['teacher_id'];
            $_SESSION['dpt_id'] = $row['dpt_id'];
            header("Location: ../Teacher/ADashboard.php");
          }
        } else {
          $errorMsg = "Your username or password is incorrect.";
        }
      } else {
        $errorMsg = "User not found";
        //  header("Location: Login.php");
      }
      $stmt->close();
      $conn->close();
    }
  } else {
    $errorMsg = "Please enter your username or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="../media/images/icon1.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <title>Login Page</title>
  <style>
    main {
      height: 100vh !important;
      overflow-y: auto !important;
    }
  </style>
</head>

<body>
  <header class="header mb-3" id="header">
    <div class="header_logo mx-auto ">
      <h1>DIGITAL CLASSROOM</h1>
    </div>
  </header>
  <main>
    <?php if (!empty($succMsg)) : ?>
      <div class="alert alert-success custom-alert" role="alert">
        <div class="circle1 ">
          <i class="bi bi-check-circle-fill succ"></i>
        </div>
        <div class="alert-text">
          <?php echo $succMsg; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($errorMsg)) : ?>
      <div class="alert alert-danger custom-alert" role="alert">
        <div class="circle2">
          <i class="bi bi-exclamation-triangle-fill err"></i>
        </div>
        <div class="alert-text">
          <?php echo $errorMsg;
          ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="container col-5">
      <form class="p-4" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2 class="h2">Login Here</h2>
        <div class="form-group">
          <label for="exampleInputEmail1">Email address</label>
          <input type="email" class="form-control  m-0" id="exampleInputEmail1" aria-describedby="emailHelp" name="email" placeholder="Enter email">
          <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
        </div>
        <div class="form-group ">
          <label for="exampleInputPassword1">Password</label>
          <input type="password" class="form-control m-0" id="exampleInputPassword1" name="pass" placeholder="Password">
        </div>
        <div class="form-group form-check">
          <input type="checkbox" class="form-check-input" id="exampleCheck1" name="verify">
          <label class="form-check-label" for="exampleCheck1">Are you a student.</label>
        </div>
        <div class="text-center">
          <button type="submit" class="btn1 mb-2">Login</button>
        </div>
        <div class="mt-3">Click here to <a href="Registration.php" style="color: #3d8def;"><b>Sign-up</b></a></div>
      </form>
    </div>
  </main>
  <script>
    setTimeout(function() {
      document.getElementsByClassName('alert-danger')[0].remove();
    }, 2000);
  </script>
</body>

</html>