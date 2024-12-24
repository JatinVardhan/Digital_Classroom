<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include "navbar.php";
include '../Home/Connection.php';
$dpt_id = $_SESSION['a_dpt_id'];
$row = $errorMsg = $succMsg = "";
$sql = "SELECT teachers.*,departments.dpt_name FROM teachers
LEFT JOIN departments ON teachers.dpt_id=departments.dpt_id
WHERE teachers.dpt_id=? OR teachers.dpt_id=0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dpt_id);
$stmt->execute();
$result = $stmt->get_result();
if (isset($_GET['delete_id'])) {
  $deleteid = $conn->real_escape_string($_GET['delete_id']);
  
  $check_sql = "SELECT * FROM classes WHERE teacher_id = ?";
  $check_stmt = $conn->prepare($check_sql);
  $check_stmt->bind_param("i", $deleteid);
  $check_stmt->execute();
  $check_result = $check_stmt->get_result();

  if($check_result->num_rows == 0) {
  $dltsql = "DELETE FROM teachers WHERE teacher_id=?";
  $dltstmt = $conn->prepare($dltsql);
  $dltstmt->bind_param("i", $deleteid);
  if ($dltstmt->execute()) {
    $succMsg = "Data updated successfully..";
    echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
  } else {
    $errorMsg = "Error :" . $dltstmt->error;
  }
}else{
  $errorMsg = "Cann't delete, because teacher have an active class..";
}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <Style>
    body {
      background-image: linear-gradient(to right, lightblue, violet, blue, #3d8def) !important;
      font-family: "Roboto", sans-serif !important;
    }

    .dlt {
      background-color: rgb(228, 50, 50);
    }

    .btn1 {
      height: 30px;
    }

    .dlt:focus {
      box-shadow: rgb(179, 32, 32) 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(179, 32, 32) 0 -3px 0 inset;
    }

    .dlt:hover {
      box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(179, 32, 32) 0 -3px 0 inset;
      transform: translateY(-2px);
    }

    .dlt:active {
      box-shadow: rgb(179, 32, 32) 0 3px 7px inset;
      transform: translateY(2px);
    }
  </Style>
  <title>Manage Teachers</title>
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
    <div class="container col-9 py-2">
      <h2 class="h2">Teachers Details</h2>
      <?php

      if ($result->num_rows > 0) {
      ?>
        <table class="my-4">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Department</th>
              <th>Edit</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) {
              if ($row['role'] == 2) { ?>

                <tr>
                  <td> <?php echo $row['teacher_name']; ?></td>
                  <td> <?php echo $row['teacher_email']; ?></td>
                  <td> <?php echo $row['phone_number']; ?></td>
                  <td class="cen"><?php echo $row['dpt_name']; ?></td>
                  <td>
                    <a href="EditTeacher.php?id=<?php echo $row['teacher_id']; ?>" class="btn1"><i class="bi bi-pencil-square"></i></a>
                  </td>
                  <td>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['teacher_id']; ?>" class="btn1 dlt delete"><i class="bi bi-x-lg"></i></a>
                  </td>
                </tr>

          <?php
              }
            }
          } else {
            echo "<h4>no teacher found....<h1>";
          }
          ?>
          </tbody>
        </table>
        <div class="my-2 text-center">
          <button type="button" class="btn1 " onclick="goBack()" style="height: 40px;"><i class="fa-solid fa-arrow-left"></i>&nbsp; Back</button>
        </div>
    </div>
  </main>
</body>

</html>