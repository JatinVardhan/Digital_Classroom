<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";
$row = $errorMsg = $succMsg = "";
$teacherErr = $dptnameErr = $name = $teacher_id = "";
$isValid = true;
$deptQuery = "SELECT * FROM teachers WHERE dpt_id=9999";
$deptResult = $conn->query($deptQuery);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $dptnameErr = "Please enter a department name.";
    $isValid = false;
  } else {
    $name = $conn->real_escape_string($_POST['name']);
    if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
      $dptnameErr = "Only letters, spaces, and dashes are allowed.";
      $isValid = false;
    }
  }
  if (empty($_POST["hod"]) || $_POST["hod"] == "none") {
    $teacherErr = "Please select hod's name.";
    $isValid = false;
  } else {
    $teacher_id = $conn->real_escape_string($_POST['hod']);
  }
  if ($isValid) {
    $dpt_sql = "INSERT INTO departments ( dpt_name) VALUES (?)";
    $dpt_stmt = $conn->prepare($dpt_sql);
    $dpt_stmt->bind_param("s", $name);
    if ($dpt_stmt->execute()) {

      $depsql = "SELECT * FROM departments WHERE dpt_name=?";
      $dptstmt = $conn->prepare($depsql);
      $dptstmt->bind_param("s", $name);
      $dptstmt->execute();
      $result = $dptstmt->get_result();
      $row = $result->fetch_assoc();
      $dpt_id = $row['dpt_id'];


      $hod_sql = "UPDATE teachers SET dpt_id=?, role=1 WHERE  teacher_id=?";
      $hod_stmt = $conn->prepare($hod_sql);
      $hod_stmt->bind_param("ii", $dpt_id, $teacher_id);
      if ($hod_stmt->execute()) {
        $succMsg = "Department added successfully... ";
        echo "<script>setTimeout(function(){ window.location.href = 'manageAdmins.php'; }, 2000);</script>";
      } else {
        $errorMsg = "Error:" . $hod_stmt->error;
      }
    } else {
      $errorMsg = "Error:" . $hod_stmt->error;
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
  <title>Add Department</title>
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
    <div class="container col-6 ">
      <form class="pt-4 mx-auto" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2 class="h2">Add New Department</h2>
        <div class="form-group mb-2">
          <label for="Name">Department Name</label>
          <input type="text" class="form-control" id="Name" name="name" placeholder="Enter department name" />
          <div class="error"><?php echo $dptnameErr; ?></div>
        </div>
        <div class="form-group mb-2">
          <label for="Hod">HOD Name</label>
          <select class="form-control" id="Hod" name="hod" onchange="fetchHODData()">
            <option value="none" selected disabled>--Select Name--</option>
            <?php
            if ($deptResult->num_rows > 0) {
              while ($row = $deptResult->fetch_assoc()) {
                echo "<option value='" . $row["teacher_id"] . "'>" . htmlspecialchars($row["teacher_name"]) . "</option>";
              }
            } else {
              echo "<option value=''>No departments available</option>";
            }
            ?>
          </select>
          <div class="error"><?php echo $teacherErr; ?></div>
        </div>
        <div class="text-center my-5">
          <button type="submit" class="btn1" name="update"><i class="bi bi-plus-lg"></i> &nbsp;Add</button>
          &emsp;&emsp;&emsp;
          <button type="button" class="btn1 dlt" onclick="goBack()"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
        </div>
      </form>
    </div>
    <br><br>
  </main>
</body>

</html>