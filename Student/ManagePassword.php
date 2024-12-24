<?php
session_start();
include '../Home/Connection.php';
include "navbar.php";
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
$row = $errorMsg = $succMsg = "";
$passwordErr = $conpassErr = "";
$isValid = true;
if (isset($_SESSION['s_id'])) {
  $edit_id = $conn->real_escape_string($_SESSION['s_id']);
} else {
  $errorMsg = "No id provided...";
}
if (isset($_POST['update'])) {

  if (empty($_POST["pass"])) {
    $passwordErr = "Password is required.";
    $isValid = false;
  } else {
    $updatepass = $conn->real_escape_string($_POST['pass']);
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/", $updatepass)) {
      $passwordErr = "Please enter a strong password.";
      $note = 'Password must include at least one symbol,one capital lettor,one small letter & minimun 8 charactor.';
      $isValid = false;
    }
    if (empty($_POST["confirmpass"])) {
      $conpassErr = "Please confirm your password.";
      $isValid = false;
    } else {
      $conpass = $conn->real_escape_string($_POST["confirmpass"]);
      }
      if ($updatepass !== $conpass) {
        $conpassErr = "Passwords do not match.";
        $isValid = false;
    }
    if ($isValid) {
      $updateSql = "UPDATE students Set student_password=? Where  student_rollno=?";
      $updateStmt = $conn->prepare($updateSql);
      $updateStmt->bind_param("si", $updatepass, $edit_id);
      $updateStmt->execute();
      if ($updateStmt->affected_rows > 0) {
        $succMsg = "Record updated sucessfully...";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
      } else {
        if ($updateStmt->error) {
          $errorMsg = "Error updating record: " . $updateStmt->error;
        } else {
          $errorMsg = "Password can not be same as current password....";
        }
      }
      $updateStmt->close();
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <style>
    .dlt {
      background-color: rgb(228, 50, 50) !important;
    }

    .dlt:focus {
      box-shadow: rgb(216, 40, 40) 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(216, 40, 40) 0 -3px 0 inset !important;
    }

    .dlt:hover {
      box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(216, 40, 40) 0 -3px 0 inset !important;
      transform: translateY(-2px) !important;
    }

    .dlt:active { 
      box-shadow: rgb(216, 40, 40) 0 3px 7px inset !important; 
      transform: translateY(2px) !important;
    }
  </style>
  <title>Edit Profile</title>
</head>

<body>
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
    <div class="container col-6">
      <form class="pt-4 " action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2 class="h2">Change Password</h2>
        <div class="form-group mx-auto mb-2">
          <label for="pass">New Password</label>
          <input type="password" class="form-control" id="pass" name="pass" placeholder="Enter password" />
          <div class="error"><?php echo $passwordErr; ?></div>
        </div>
        <div class="form-group mx-auto ">
          <label for="confirmpass">Confirm New Password</label>
          <input type="password" class="form-control" id="confirmpass" name="confirmpass" placeholder="Confirm password" />
          <div class="error"><?php echo $conpassErr; ?></div>
        </div>
        <div class="text-center my-5">
          <button type="submit" class="btn1" name="update"><i class="bi bi-arrow-repeat pr"> </i>&nbsp; Update</button>
          &emsp;&emsp;&emsp;
          <button type="button" class="btn1 dlt" onclick="goBack()"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>