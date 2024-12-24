<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";
$row = $errorMsg = $succMsg = "";
$dptErr = $dpt_id = "";
$isValid = true;
$deptQuery = "SELECT departments.* FROM departments
  LEFT JOIN teachers ON departments.dpt_id = teachers.dpt_id
  LEFT JOIN classes ON departments.dpt_id = classes.dpt_id
  LEFT JOIN students ON departments.dpt_id = students.dpt_id
  WHERE teachers.dpt_id IS NULL AND classes.dpt_id IS NULL AND students.dpt_id IS NULL";
$deptResult = $conn->query($deptQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_POST["department"])) {
    $dptErr = "Please select a department.";
  } else {
    $dpt_id = $conn->real_escape_string($_POST["department"]);
    $dpt_sql = "DELETE FROM departments WHERE dpt_id=?";
    $dpt_stmt = $conn->prepare($dpt_sql);
    $dpt_stmt->bind_param("i", $dpt_id);
    if ($dpt_stmt->execute()) {
      $succMsg = "Department deleted successfully...";
      echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
    } else {
      $errorMsg = "Error deleting department: " . $dpt_stmt->error;
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
  <title>Delete Department</title>
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
    <div class="container col-sm-6">
      <form class="pt-4 " action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2 class="h2">Delete Department</h2>
        <p class="px-5 text-danger"><em>You can only delete a department if there are no teachers, classes and students currently associated with it.</em></p>
        <div class="form-group mx-5 px-5">
          <label for="Department">Department</label>
          <select name="department" class="form-control" id="department-select" name="department">
            <option value="null" selected disabled>--Choose department--</option>
            <?php
            if ($deptResult->num_rows > 0) {
              while ($row = $deptResult->fetch_assoc()) {
                echo "<option value='" . $row["dpt_id"] . "'>" . htmlspecialchars($row["dpt_name"]) . "</option>";
              }
            } else {
              echo "<option value=''>No departments available</option>";
            }
            ?>
          </select>
          <div class="error"><?php echo $dptErr; ?></div>
        </div>
        <div class="text-center my-5">
          <button type="submit" class="btn1 dlt delete" name="update"><i class="fa-regular fa-trash-can"></i>&nbsp; Delete</button>&emsp;&emsp;&emsp;
          <button type="button" class="btn1 " onclick="goBack()"><i class="fa-solid fa-arrow-left"></i>&nbsp; Back</button>
        </div>
      </form>
    </div>
    <br><br>
  </main>
  <script>
    document.getElementById('Hod').addEventListener('change', function() {
      var rollNoField = document.getElementById('phoneNoField');
      var rollNoField = document.getElementById('emailField');
      if (this.value > '0') {
        phoneNoField.style.display = 'block';
        emailField.style.display = 'block';
        // var selectedValue = this.value;
        // // Redirect to the same page with the selected value in the URL
        // window.location.href = '<?php echo $_SERVER['PHP_SELF']; ?>?teacher_id=' + selectedValue;
      } else {
        phoneNoField.style.display = 'none';
        emailField.style.display = 'none';
      }
    });
  </script>
</body>

</html>