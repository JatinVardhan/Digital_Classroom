<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include "navbar.php";
include '../Home/Connection.php';
$row = $errorMsg = $succMsg = "";
$a_id=$_SESSION['a_id'];
$sql = "SELECT teachers.*,departments.dpt_name FROM teachers
LEFT JOIN departments ON teachers.dpt_id=departments.dpt_id WHERE NOT teachers.teacher_id= $a_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
if (isset($_GET['delete_id'])) {
  $deleteid = $conn->real_escape_string($_GET['delete_id']);
  $checkAdminQuery = "SELECT * FROM teachers WHERE dpt_id=(SELECT dpt_id FROM teachers WHERE teacher_id=?) AND role=1";
  $checkAdminStmt = $conn->prepare($checkAdminQuery);
  $checkAdminStmt->bind_param("i", $deleteid);
  $checkAdminStmt->execute();
  $adminResult = $checkAdminStmt->get_result();

  if ($adminResult->num_rows == 1) {
   
    $errorMsg = "Sorry, this admin cannot be deleted as they are the only admin for their department.";
  } else {
    
    $dltsql = "DELETE FROM teachers WHERE teacher_id=?";
    $dltstmt = $conn->prepare($dltsql);
    $dltstmt->bind_param("i", $deleteid);
    if ($dltstmt->execute()) {
      $succMsg = "Data updated successfully..";
      echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
    } else {
      $errorMsg = "Error :" . $dltstmt->error;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <Style>
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
    <div class="container conatiner1 col-9 py-4">
      <h2 class="h2">Admin Details</h2>
      <?php
      if ($result->num_rows > 0) {
      ?>
        <table class="my-3">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Department</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) {
              if ($row['role'] == 1) { ?>

                <tr>
                  <td> <?php echo $row['teacher_name']; ?></td>
                  <td> <?php echo $row['teacher_email']; ?></td>
                  <td> <?php echo $row['phone_number']; ?></td>
                  <td class="cen"><?php echo $row['dpt_name']; ?></td>
                  <td>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['teacher_id']; ?>" class="btn1 dlt delete"><i class="bi bi-x-lg"></i></a>
                  </td>
                </tr>

          <?php
              }
            }
          } else {
            echo "<h4>No other super admin found....<h4>";
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