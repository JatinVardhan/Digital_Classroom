<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";
$errorMsg = $succMsg = $passErr = "";
$dpt_id = $_SESSION['a_dpt_id'];
if (isset($_SESSION['a_id'])) {
  $edit_id = $conn->real_escape_string($_SESSION['a_id']);
} else {
  $errorMsg = "No id provided...";
}
if (isset($_POST['delete'])) {
  if (!isset($_POST['verify'])) {
    $errorMsg = "Please confirm that you want to delete your profile ...";
  } else {
    if (empty($_POST["pass"])) {
      $passErr = "Please enter your password.";
    } else {
      $Enterd_pass = $conn->real_escape_string($_POST['pass']);
      $sql = "SELECT * FROM teachers WHERE teacher_id=?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("i", $edit_id);
      $stmt->execute();
      $result = $stmt->get_result();
      $row = $result->fetch_assoc();
      $pass = $row["teacher_password"];
      if ($pass != $Enterd_pass) {
        $passErr = "You entered a wrong Password";
      } else {
        $chk_sql = "SELECT COUNT(*) FROM teachers WHERE role=1 AND dpt_id=?";
        $chk_stmt = $conn->prepare($chk_sql);
        $chk_stmt->bind_param("i", $dpt_id);
        $chk_stmt->execute();
        $chk_result = $chk_stmt->get_result();
        $chk_row = $chk_result->fetch_assoc();

        if ($chk_row['COUNT(*)'] > 1) {
          $dlt_sql = "DELETE FROM teachers WHERE  teacher_id=?";
          $dlt_stmt = $conn->prepare($dlt_sql);
          $dlt_stmt->bind_param("i", $edit_id);
          if ($dlt_stmt->execute()) {
            echo "<script> setTimeout(function(){ window.location.href = 'LogOut.php'; }, 2000);</script>";
          } else {
            $errorMsg = "Error updating record: " . $dlt_stmt->error;
          }
        } else {
          $errorMsg = " You cannot delete your account because you are the only admin for your branch.";
        }
      }
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
  <Style>
    p {
      text-align: justify;
      font-weight: 600;
    }

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
  </Style>
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
    <div class="container col-6">
      <form class="py-4 " action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $edit_id; ?>" method="POST">
        <h2 class="h2">Delete Profile</h2>
        <p class="px-5 text-danger">If you proceed to delete your account, it will be permanently deleted, and you may lose all associated data.</p>
        <div class="text-center ">
          <input type="checkbox" class="form-check-input" id="Check1" name="verify">
          <label class="form-check-label" for="Check1">Are you sure you want to delete your profile.</label>
        </div>
        <div class="form-group align-item-center" id="rollNoField" style="display: none;">
          <label for="pass">Please Enter Password</label>
          <input type="password" class="form-control" id="pass" name="pass" placeholder="Enter password" />
          <div class="error"><?php echo $passErr; ?></div>
        </div>
        <div class="text-center my-4">
          <button type="submit" class="btn1 dlt delete" name="delete"><i class="fa-regular fa-trash-can"></i>&nbsp; Delete</button>
          &emsp;&emsp;&emsp;
          <button type="button" class="btn1 " onclick="goBack()"><i class="fa-solid fa-arrow-left"></i>&nbsp; Back</button>
        </div>
      </form>
    </div>
  </main>
  <script>
    document.getElementById('Check1').addEventListener('change', function() {
      var rollNoField = document.getElementById('rollNoField');
      console.log(this.value);
      if (this.value == 'on') {
        rollNoField.style.display = 'block';
      } else {
        rollNoField.style.display = 'none';
      }
    });
  </script>
</body>

</html>