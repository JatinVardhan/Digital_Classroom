<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";

$row = $errorMsg = $succMsg = "";
$nameErr = $emailErr = $phoneErr = "";
$updatename = $updateemail = $updatephone = "";
$isValid = true;
if (isset($_SESSION['id'])) {
  $edit_id = $conn->real_escape_string($_SESSION['id']);
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
  if (empty($_POST["name"])) {
    $nameErr = "Name is required.";
    $isValid = false;
  } else {
    $updatename = $conn->real_escape_string($_POST['name']);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $updatename)) {
      $nameErr = "Only letters, spaces, and dashes are allowed.";
      $isValid = false;
    }
  }
  if (empty($_POST["email"])) {
    $emailErr = "Email is required.";
    $isValid = false;
  } else {
    $updateemail = $conn->real_escape_string($_POST['email']);
    if (!filter_var($updateemail, FILTER_VALIDATE_EMAIL)) {
      $emailErr = "Invalid email format.";
      $isValid = false;
    }
  }
  if (empty($_POST["phone"])) {
    $phoneErr = "Phone number is required.";
    $isValid = false;
  } else {
    $updatephone = $conn->real_escape_string($_POST['phone']);
    if (!preg_match("/^\d{10}$/", $updatephone)) {
      $phoneErr = "Please enter a valid 10-digit phone number.";
      $isValid = false;
    }
  }
  if ($isValid) {

    $updateSql = "UPDATE teachers Set teacher_name=?, teacher_email=?, phone_number=? Where teacher_id=?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssii", $updatename, $updateemail, $updatephone, $edit_id);

    $updateStmt->execute();
    if ($updateStmt->affected_rows > 0) {
      $succMsg = "Information updated sucessfully...";
      echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
    } else {
      if ($updateStmt->error) {
        $errorMsg = "Error updating record: " . $updateStmt->error;
      } else {
        $errorMsg = "No changes made to the record...";
      }
    }
    $updateStmt->close();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <title>Edit Profile</title>
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
    <div class="container col-6">
      <form class="pt-3 col-sm-7" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2 class="h2">Edit Information</h2>
        <div class="form-group mx-auto mb-2">
          <label for="Name">Name</label>
          <input type="text" class="form-control" id="Name" name="name" value="<?php echo $row['teacher_name']; ?>" placeholder="Enter name" />
          <div class="error"><?php echo $nameErr; ?></div>
        </div>
        <div class="form-group mx-auto mb-2">
          <label for="Email1">Email address</label>
          <input type="email" class="form-control" id="Email1" aria-describedby="emailHelp" name="email" value="<?php echo htmlspecialchars($row['teacher_email']); ?>" placeholder="Enter email" />
          <div class="error"><?php echo $emailErr; ?></div>
        </div>
        <div class="form-group mx-auto ">
          <label for="PhoneNumber">Phone number</label>
          <input type="tel" class="form-control" id="PhoneNumber" name="phone" value="<?php echo htmlspecialchars($row['phone_number']); ?>" placeholder="Enter phone number" />
          <div class="error"><?php echo $phoneErr; ?></div>
        </div>
        <div class="my-5 text-center">
          <button type="submit" class="btn1" name="update" onclick="alert1()"> <i class="bi bi-arrow-repeat pr"> </i>&nbsp; Update</button>&emsp;&emsp;&emsp;
          <button type="button" class="btn1 dlt" onclick="goBack()"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
        </div>
      </form>
    </div>
    <br><br>
  </main>
</body>

</html>