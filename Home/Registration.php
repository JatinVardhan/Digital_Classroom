<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;

include '../phpmailer/src/PHPMailer.php';
include '../phpmailer/src/SMTP.php';
include '../phpmailer/src/Exception.php';
include 'Connection.php';
function generateVerificationCode()
{
  return rand(100000, 999999);
}
// Fetch departments
$deptQuery = "SELECT * FROM departments";
$deptResult = $conn->query($deptQuery);
$isValid = true;
$mailSent = false;
$succMsg = $errorMsg = $verificationCode = $remainingTime = "";
$nameErr = $emailErr = $passErr = $rollErr = $departmentErr = $phoneErr = $roleErr = $semesterErr = "";
$name = $email = $password = $department = $role = $phone_no = $rollno = $semester = "";
$_SESSION['email'] = isset($_SESSION['email']) ? $_SESSION['email'] : '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['otp'])) {

  if (empty($_POST["role"]) || $_POST["role"] == "none") {
    $roleErr = "Please select your role.";
    $isValid = false;
  } else {
    $_SESSION['role'] = $conn->real_escape_string($_POST['role']);
  }

  if (empty($_POST["name"])) {
    $nameErr = "Name is required.";
    $isValid = false;
  } else {
    $_SESSION['name']  = $conn->real_escape_string($_POST['name']);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $_SESSION['name'])) {
      $nameErr = "Only letters, spaces, and dashes are allowed.";
      $isValid = false;
    }
  }
  if ($_SESSION['role'] == 3) {
    if (empty($_POST["rollno"])) {
      $rollErr = "Roll number is required.";
      $isValid = false;
    } else {
      $_SESSION['rollno'] = $conn->real_escape_string($_POST['rollno']);
      echo  $_SESSION['rollno'];
      if (!preg_match("/^[a-zA-Z0-9]*$/", $rollno)) {
        $rollErr = "Roll number can contain combination of numbers and letters only.";
        $isValid = false;
      } else {
        $checkRollQuery = "SELECT * FROM students WHERE student_rollno = ?";
        $checkRollStmt = $conn->prepare($checkRollQuery);
        $checkRollStmt->bind_param("s", $rollno);
        $checkRollStmt->execute();
        $checkRollResult = $checkRollStmt->get_result();

        if ($checkRollResult->num_rows > 0) {
          $errorMsg = "Student with this roll number already exists.";
          $isValid = false;
        }
      }
    }
    if (empty($_POST['semester']) || !in_array($_POST['semester'], [1, 2, 3, 4, 5, 6, 7, 8])) {
      $semesterErr = "Please select a valid semester.";
      $isValid = false;
    } else {
      $_SESSION['semester'] = $conn->real_escape_string($_POST['semester']);
    }
  }
  if (empty($_POST["email"])) {
    $emailErr = "Email is required.";
    $isValid = false;
  } else {
    $email =  $_SESSION['email'] = $conn->real_escape_string($_POST['email']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format.";
      $isValid = false;
    }
  }

  if (empty($_POST["password"])) {
    $passErr = "Password is required.";
    $isValid = false;
  } else {
    $_SESSION['password']  = $conn->real_escape_string($_POST['password']);
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $_SESSION['password'])) {
      $passErr = "Please enter a strong password.";
      $note = "Password must include at least one symbol, one capital letter, one small letter, and minimum 8 characters.";
      $isValid = false;
    }
  }

  if (empty($_POST["department"]) || $_POST["department"] == "null") {
    $departmentErr = "Please select your department.";
    $isValid = false;
  } else {
    $_SESSION['department'] = $conn->real_escape_string($_POST['department']);
  }
  if (empty($_POST["phone"])) {
    $phoneErr = "Phone number is required.";
    $isValid = false;
  } else {
    $phone_no = $_SESSION['phone_no'] = $conn->real_escape_string($_POST['phone']);
    if (!preg_match("/^\d{10}$/", $_SESSION['phone_no'])) {
      $phoneErr = "Please enter a valid 10-digit phone number.";
      $isValid = false;
    }
  }
  // Check if teacher with the same phone number already exists
  $checkQuery = "SELECT * FROM teachers WHERE (phone_number = ? OR teacher_email = ?) AND role=2";
  $checkStmt = $conn->prepare($checkQuery);
  $checkStmt->bind_param("ss", $phone_no, $email);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    $errorMsg = "A teacher with the same phone number or email already exists.";
    $isValid = false;
  }

  $checkQuery = "SELECT * FROM students WHERE (phone_number = ? OR student_email = ?) ";
  $checkStmt = $conn->prepare($checkQuery);
  $checkStmt->bind_param("ss", $phone_no, $email);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();

  if ($checkResult->num_rows > 0) {
    $errorMsg = "A student with the same phone number or email already exists.";
    $isValid = false;
  }
  $mailSent = false;
  if ($isValid) {
    // Generate a verification code
    $verificationCode = generateVerificationCode();
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jatin071103@gmail.com';
    $mail->Password = 'mbrc deso itrf zjet';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('jatin071103@gmail.com', 'Jatin');
    $mail->addAddress($email);
    $mail->Subject = 'Email Verification';
    $mail->Body = 'Your verification code is: ' . $verificationCode;

    if ($mail->send()) {
      $_SESSION['verification_code'] = $verificationCode;
      $_SESSION['email_sent_time'] = time();
      $_SESSION['email1'] = true;
    } else {
      $errorMsg = "Failed to send verification email. Please try again later.";
    }
  }
}
$remainingMinutes = $remainingSeconds = "";
if (isset($_SESSION['email_sent_time'])) {
  $expirationTime = 10 * 60; // 10 minutes in seconds
  $elapsedTime = time() - $_SESSION['email_sent_time'];
  $remainingTime = max(0, $expirationTime - $elapsedTime);

  // Calculate remaining minutes and seconds
  $remainingMinutes = floor($remainingTime / 60);
  $remainingSeconds = $remainingTime % 60;
} else {
  $remainingMinutes = 0;
  $remainingSeconds = 0;
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp1'])) {
  if ($_SESSION['email1']) {

    $enteredOTP = '';
    for ($i = 1; $i <= 6; $i++) {
      $enteredOTP .= $_POST["otp$i"];
    }
    // Verify OTP
    if ($enteredOTP ==  $_SESSION['verification_code']) {
      $name = $_SESSION['name'];
      $email = $_SESSION['email'];
      $password = $_SESSION['password'];
      $department = $_SESSION['department'];
      $role = $_SESSION['role'];
      $phone_no = $_SESSION['phone_no'];
      if ($_SESSION['role'] == 3) {
        $rollno = $_SESSION['rollno'];
        $semester = $_SESSION['semester'];
      }

      if ($role == 2) {
        $tr_sql = "INSERT INTO teachers ( teacher_name, teacher_email, teacher_password, dpt_id, phone_number) VALUES (?,?,?,?,?)";
        $tr_stmt = $conn->prepare($tr_sql);
        $tr_stmt->bind_param("sssii", $name, $email, $password, $department, $phone_no);
        if ($tr_stmt->execute()) {
          $succMsg = "Registered Successfully";
          session_unset();
          session_destroy();
          echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
        } else {
          $errorMsg = "Error:" . $tr_stmt->error;
        }
      } else {

        $st_sql = "INSERT INTO students ( student_rollno,student_name,semester, student_email, student_password, dpt_id, phone_number) VALUES (?,?,?,?,?,?,?)";
        $st_stmt = $conn->prepare($st_sql);
        $st_stmt->bind_param("ssissii", $rollno, $name, $semester, $email, $password, $department, $phone_no);
        if ($st_stmt->execute()) {
          $succMsg = "Registered Successfully";
          session_unset();
          session_destroy();
          echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
        } else {
          $errorMsg = "Error:" . $st_stmt->error;
        }
      }
    } else {
      // Wrong OTP, display error message
      $errorMsg = "Wrong OTP. Please enter the correct OTP.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="../media/images/icon1.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <title>Registration Page</title>
  <style>
    .background {
      display: none;
      position: fixed;
      z-index: 999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .container1 {
      background-color: white;
      position: relative;
      margin: 8% auto;
      width: 60%;
      max-width: 600px;
      border-radius: 10px;
      padding: 20px;


    }

    .visible {
      display: block !important;
    }

    .hidden {
      display: none !important;
      /* display: block !important; */
    }

    .bottom {
      display: flex;
      justify-content: space-between;
      color: #3d8def !important;
      font-weight: bold;
      width: 70%;
    }

    .bottom p {
      cursor: pointer;
    }

    .btn1.disabled {
      background-color: #999;
      cursor: not-allowed;
      pointer-events: none;
      color: #777;
    }


    .btn1.disabled:hover,
    .btn1.disabled:active,
    .btn1.disabled:focus {
      background-color: #999 !important;
      color: #777 !important;
      box-shadow: none !important;
      border-color: transparent !important;
      outline: none !important;
    }

    .opt-input {
      display: flex;
      flex-direction: row;
    }

    .container1 input[type="text"] {
      width: 3rem;
      height: 3rem;
      padding: 5px;
      margin-bottom: 15px;
      border: 2px solid rgb(58, 58, 70);
      border-radius: 5px;
      box-sizing: border-box;
      background-color: rgb(233, 235, 235);
      font-size: 1.5em;
      text-align: center;
    }

    .container1 input[type="text"]:hover,
    .container1 input[type="text"]:focus {
      width: 3rem;
      height: 3rem;
      padding: 5px 10px;
      margin-bottom: 15px;
      border: 2px solid rgb(58, 58, 70);
      border-radius: 5px;
      /* box-shadow:0 0 3px 2px rgb(181, 179, 179); */
      background-color: rgb(232, 240, 250);
      box-shadow: none;
    }
  </style>
</head>

<body>
  <header class="header" id="header">
    <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
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
          <?php echo $errorMsg; ?>
        </div>
      </div>
    <?php endif; ?>
    <?php if (!empty($note)) : ?>
      <div class="alert alert-warning custom-alert1" role="alert">
        <div class="circle3">
          <i class="bi bi-exclamation-triangle-fill info"></i>
        </div>
        <div class="note-text">
          <?php echo $note; ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="container my-4 col-sm-5">
      <form class="p-3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2 class="h2">Register Here</h2>
        <div class="form-group">
          <label for="role">Register As:</label><br>
          <select name="role" id="role" class="form-control">
            <option value="none" <?php echo isset($_SESSION['role']) && $_SESSION['role'] == 'none' ? 'selected' : ''; ?> disabled>--Select your role --</option>
            <option value="2" <?php echo isset($_SESSION['role']) && $_SESSION['role'] == '2' ? 'selected' : ''; ?>>Teacher</option>
            <option value="3" <?php echo isset($_SESSION['role']) && $_SESSION['role'] == '3' ? 'selected' : ''; ?>>Student</option>
          </select>
          <div class="error"><?php echo $roleErr; ?></div>
        </div>
        <div class="form-group">
          <label for="Name">Name</label>
          <input type="text" class="form-control" id="Name" name="name" placeholder="Enter name" value="<?php echo isset($_SESSION['name']) ? $_SESSION['name'] : ''; ?>" />
          <div class="error"><?php echo $nameErr; ?></div>
        </div>
        <div class="form-group" id="rollNoField" <?php echo (isset($_SESSION['rollno']) ? $_SESSION['rollno'] : '' == 3) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
          <label for="RollNo">Roll no</label>
          <input type="text" class="form-control" id="RollNo" name="rollno" placeholder="Enter roll number" value="<?php echo isset($_SESSION['rollno']) ? $_SESSION['rollno'] : ''; ?>" />
          <div class="error"><?php echo $rollErr; ?></div>
        </div>
        <div class="form-group" id="semField" <?php echo (isset($_SESSION['rollno']) ? $_SESSION['rollno'] : '' == 3) ? 'style="display: block;"' : 'style="display: none;"'; ?>>
          <label for="semester">Semester</label>
          <select class="form-select" id="semester" name="semester">
            <option selected disabled>-- Select semester --</option>
            <option value="1" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '1' ? 'selected' : ''; ?>>Semester 1</option>
            <option value="2" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '2' ? 'selected' : ''; ?>>Semester 2</option>
            <option value="3" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '3' ? 'selected' : ''; ?>>Semester 3</option>
            <option value="4" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '4' ? 'selected' : ''; ?>>Semester 4</option>
            <option value="5" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '5' ? 'selected' : ''; ?>>Semester 5</option>
            <option value="6" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '6' ? 'selected' : ''; ?>>Semester 6</option>
            <option value="7" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '7' ? 'selected' : ''; ?>>Semester 7</option>
            <option value="8" <?php echo isset($_SESSION['semester']) && $_SESSION['semester'] == '8' ? 'selected' : ''; ?>>Semester 8</option>
          </select>
          <div class="error"><?php echo $semesterErr; ?></div>
        </div>
        <div class="form-group">
          <label for="Email1">Email address</label>
          <input type="email" class="form-control" id="Email1" aria-describedby="emailHelp" name="email" placeholder="Enter email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" />
          <div class="error"><?php echo $emailErr; ?></div>
        </div>
        <div class="form-group">
          <label for="Password1">Password</label>
          <input type="password" class="form-control" id="Password1" name="password" placeholder="Password" value="<?php echo isset($_SESSION['password']) ? $_SESSION['password'] : ''; ?>" />
          <div class="error"><?php echo $passErr; ?></div>
        </div>
        <div class="form-group">
          <label for="Department">Department</label><br>
          <select name="department" class="form-control" id="department-select" name="department">
            <option value="none" selected disabled>--Select your department--</option>

            <?php
            if ($deptResult->num_rows > 0) {
              while ($row = $deptResult->fetch_assoc()) {
                $selected = isset($_SESSION['department']) && $_SESSION['department'] == $row["dpt_id"] ? 'selected' : '';
                echo "<option value='" . $row["dpt_id"] . "'$selected >" . htmlspecialchars($row["dpt_name"]) . "</option>";
              }
            } else {
              echo "<option value=''>No departments available</option>";
            }
            ?>
            <option value="9999" <?php echo isset($_SESSION['department']) && $_SESSION['department'] == '9999' ? 'selected' : ''; ?>>None</option>
          </select>
          <div class="error"><?php echo $departmentErr; ?></div>
        </div>
        <div class="form-group">
          <label for="PhoneNumber">Phone number</label>
          <input type="tel" class="form-control" id="PhoneNumber" name="phone" placeholder="Enter phone number" value="<?php echo isset($_SESSION['phone_no']) ? $_SESSION['phone_no'] : ''; ?>" />
          <div class="error"><?php echo $phoneErr; ?></div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn1 mt-4 mb-2" id="resubmit">Submit</button>
        </div>
        <div class="mt-3">Click here to <a href="Login.php" style="color: #3d8def;"><b>Login</b></a></div>
      </form>
    </div>

    <div id="verificationBox" class="background <?php echo $_SESSION['email1'] ? 'visible' : 'hidden'; ?>">
      <!-- Add id to the verification box -->
      <div class="container1 text-center px-5 py-4 <?php echo $_SESSION['email1'] ? 'visible' : 'hidden'; ?>">
        <div class="verification  mx-4 mt-2 mb-4">
          <img src="../media/images/email.png" alt="Email Logo" height="70" width="70">
          <h3 class="my-2">VERIFY YOUR EMAIL ADDRESS</h3>
        </div>
        <div class="dropdown-divider "></div>
        <div class="verification-message">
          <h5 class="my-4">A verification code has been sent to <br> <b><?php echo $_SESSION['email'] ?></b></h5>
          <p style="text-align: justify !important;">Please check your inbox and enter the verification code below to verify your email address. The code will expire in <span id="countdown" data-remaining-time="<?php echo $remainingTime; ?>"><?php echo sprintf('%02d:%02d', $remainingMinutes, $remainingSeconds); ?></span>.</p>
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" class="otp-form ">
          <div class="form-group otp-input ">
            <input type="text" id="otp1" name="otp1" maxlength="1" oninput="validateInput(this)" placeholder="__" />
            <input type="text" id="otp2" name="otp2" maxlength="1" oninput="validateInput(this)" placeholder="__" />
            <input type="text" id="otp3" name="otp3" maxlength="1" oninput="validateInput(this)" placeholder="__" />
            <input type="text" id="otp4" name="otp4" maxlength="1" oninput="validateInput(this)" placeholder="__" />
            <input type="text" id="otp5" name="otp5" maxlength="1" oninput="validateInput(this)" placeholder="__" />
            <input type="text" id="otp6" name="otp6" maxlength="1" oninput="validateInput(this)" placeholder="__" />
          </div>

          <button type="submit" id="submitBtn" class="btn1 <?php echo $remainingTime <= 0 ? 'disabled' : ''; ?>" <?php echo $remainingTime <= 0 ? 'disabled' : ''; ?>>Verify</button>
        </form>
        <div class="bottom mx-auto mt-4">
          <p onclick="changeEmail()">Change email</p> 
          <p id="resendBtn">Resent code</p>
        </div>
      </div>
    </div>
    <?php if ($_SESSION['email']) : ?>
      <script>
        setInterval(updateRemainingTime, 1000);

        // Function to update the remaining time
        function updateRemainingTime() {
          var countdownElement = document.getElementById("countdown");
          var remainingTime = parseInt(countdownElement.dataset.remainingTime);

        
          remainingTime = Math.max(0, remainingTime - 1);
          var remainingMinutes = Math.floor(remainingTime / 60);
          var remainingSeconds = remainingTime % 60;
          countdownElement.textContent = remainingMinutes.toString().padStart(2, '0') + ':' + remainingSeconds.toString().padStart(2, '0');
          countdownElement.dataset.remainingTime = remainingTime;

          // Check if the time has expired
          if (remainingTime === 0) {
            document.getElementById("submitBtn").disabled = true;
          }
        }

        function checkExpiration() {
          var countdown = document.getElementById("countdown").innerText;
          if (countdown === "00:00") {
            document.getElementById("submitBtn").disabled = true;
          }
        }

        // Call the checkExpiration function when the page loads
        document.addEventListener("DOMContentLoaded", checkExpiration);

        function changeEmail() {
          var verificationBox = document.getElementById("verificationBox");
          var emailInput = document.getElementById("Email1");

          // Hide the verification box
          verificationBox.classList.remove("visible");
          verificationBox.classList.add("hidden");

          // Place cursor in the email input field
          emailInput.focus();
        }
      </script>
    <?php endif; ?>
    <script>
      document.getElementById('role').addEventListener('change', function() {
        var rollNoField = document.getElementById('rollNoField');
        var semField = document.getElementById('semField');
        if (this.value === '3') {
          rollNoField.style.display = 'block';
          semField.style.display = 'block';
        } else {
          rollNoField.style.display = 'none';
          semField.style.display = 'none';
        }
      });
      setTimeout(function() {
        document.getElementsByClassName('alert-danger')[0].remove();
      }, 2000);
      setTimeout(function() {
        document.getElementsByClassName('alert-warning')[0].remove();
      }, 2000);

      function checkExpiration() {
        var countdown = document.getElementById("countdown").innerText;
        if (countdown === "00:00") {
          document.getElementById("submitBtn").disabled = true;
        }
      }
      document.getElementById('resendBtn').addEventListener('click', function() {
        // Trigger click event on the submit button with ID resubmit
        document.getElementById('resubmit').click();
      });

      function validateInput(input) {
        var maxLength = parseInt(input.getAttribute('maxlength'));
        var nextInputId = parseInt(input.id.slice(3)) + 1;
        var nextInput = document.getElementById('otp' + nextInputId);

        if (isNaN(input.value)) {
          // Clear the input if a non-numeric value is entered
          input.value = '';
          return;
        }

        if (input.value.length >= maxLength) {
          // Move to the next input field when the current one is filled
          if (nextInput) {
            nextInput.focus();
          }
        }
      }
      document.querySelectorAll('.otp-input input').forEach(input => {
        input.addEventListener('keydown', function(event) {
          if (event.key === 'Backspace' && this.value === '') {
            var currentId = parseInt(this.id.slice(3));
            var previousId = currentId - 1;
            var previousInput = document.getElementById('otp' + previousId);
            if (previousInput) {
              previousInput.focus();
            }
          }
        });
      });
    </script>
  </main>
</body>

</html>