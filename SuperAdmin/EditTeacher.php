<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";
$row = $errorMsg = $succMsg = "";
$nameErr = $emailErr = $phoneErr = $passErr = "";
$updatename = $updateemail = $updatephone = "";
$isValid = true;
if (isset($_GET['id'])) {
  $edit_id = $conn->real_escape_string($_GET['id']);
  $sql = "SELECT * FROM teachers WHERE teacher_id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $edit_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
  } else {
    $errorMsg = "No record found...";
  }
} else {
  $errorMsg = "No id provided...";
}

if (isset($_POST['update'])) {
  $updaterole = $conn->real_escape_string($_POST['role']);
  if ($updaterole == 1) {
  $updatename = $row['teacher_name'];
  $updateemail = $row['teacher_email'];
  $updatephone = $row['phone_number'];
  $dpt = $row['dpt_id'];
 
  if (empty($_POST["password"])) {
    $passErr = "Password is required.";
    $isValid = false;
  } else {
    $password  = $conn->real_escape_string($_POST['password']);
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password)) {
      $passErr = "Please enter a strong password.";
      $note = "Password must include at least one symbol, one capital letter, one small letter, and minimum 8 characters.";
      $isValid = false;
    }
  }
  // Check if the email already exists in the teachers table
  $email = $updateemail;
  $checkEmailSql = "SELECT * FROM teachers WHERE teacher_email = ?";
  $checkEmailStmt = $conn->prepare($checkEmailSql);
  $checkEmailStmt->bind_param("s", $email);
  $checkEmailStmt->execute();
  $checkEmailResult = $checkEmailStmt->get_result();
  
  if ($checkEmailResult->num_rows > 1) {
      $errorMsg = "Teacher is already an admin";
      $isValid = false;
  } 
  if ($isValid) {

      $insertSql = "INSERT INTO teachers (teacher_name, teacher_email, phone_number, role, dpt_id, teacher_password) VALUES (?, ?, ?, ?, ?, ?)";
      $insertStmt = $conn->prepare($insertSql);
      $insertStmt->bind_param("ssiiis", $updatename, $updateemail, $updatephone, $updaterole, $dpt, $password);
      
      $insertStmt->execute();
      if ($insertStmt->affected_rows > 0) {
        $succMsg = "New admin account created successfully...";
        echo "<script> setTimeout(function(){ window.location.href = 'ManageTeachers.php'; }, 2000);</script>";
      } else {
        $errorMsg = "Error creating new admin account: " . $insertStmt->error;
      }
      $insertStmt->close();
    } 
  }else {
    $errorMsg = "No change made to the record...";
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
      background-color: rgb(228, 50, 50);
    }

    .dlt:focus {
      box-shadow: rgb(216, 40, 40) 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(216, 40, 40) 0 -3px 0 inset;
    }

    .dlt:hover {
      box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(216, 40, 40) 0 -3px 0 inset;
      transform: translateY(-2px);
    }

    .dlt:active {
      box-shadow: rgb(216, 40, 40) 0 3px 7px inset;
      transform: translateY(2px);
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
      <form class="pt-4 " action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $edit_id; ?>" method="POST">
        <h2 class="h2">Edit Information</h2>
        <div class="form-group mx-auto mb-2">
          <label for="Name">Name</label>
          <input type="text" class="form-control" id="Name" name="name" value="<?php echo $row['teacher_name']; ?>" readonly>
          <div class="error"><?php echo $nameErr; ?></div>
        </div>
        <div class="form-group mx-auto mb-2">
          <label for="Email1">Email address</label>
          <input type="email" class="form-control" id="Email1" aria-describedby="emailHelp" name="email" value="<?php echo htmlspecialchars($row['teacher_email']); ?>" readonly>
          <div class="error"><?php echo $emailErr; ?></div>
        </div>
        <div class="form-group mx-auto mb-2">
          <label for="PhoneNumber">Phone number</label>
          <input type="tel" class="form-control" id="PhoneNumber" name="phone" value="<?php echo htmlspecialchars($row['phone_number']); ?>" readonly>
          <div class="error"><?php echo $phoneErr; ?></div>
        </div>
        <div class="form-group mx-auto">
          <label for="role">Change Role </label>
          <select name="role" id="role" class="form-control">
            <option value="2" selected>Teacher</option>
            <option value="1">Super Admin</option>
          </select>
        </div>
        <div class="form-group" id="passField" style="display:none;">
          <label for="Password1">Password</label>
          <input type="password" class="form-control" id="Password1" name="password" placeholder="Enter a new password for admin * "  />
          <div class="error"><?php echo $passErr; ?></div>
        </div>
        <div class="text-center mt-4 mb-5">
          <button type="submit" class="btn1" name="update"><i class="bi bi-arrow-repeat pr"> </i>&nbsp; Update</button>
          &emsp;&emsp;&emsp;
          <button type="button" class="btn1 dlt" onclick="goBack()"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
        </div>
      </form>
    </div>
    <br><br>
  </main>
</body>
<script>
   document.getElementById('role').addEventListener('change', function() {
        var passField = document.getElementById('passField');
        if (this.value === '1') {
          passField.style.display = 'block';
          
        } else {
          passField.style.display = 'none';
        
        }
      });
</script>

</html>