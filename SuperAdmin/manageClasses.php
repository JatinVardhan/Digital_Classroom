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
$sql = "SELECT classes.*,departments.dpt_name,teachers.teacher_name FROM classes
LEFT JOIN departments ON classes.dpt_id=departments.dpt_id
LEFT JOIN teachers ON classes.teacher_id=teachers.teacher_id
WHERE classes.dpt_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dpt_id);
$stmt->execute();
$result = $stmt->get_result();

if (isset($_GET['delete_id'])) {
  $deleteid = $conn->real_escape_string($_GET['delete_id']);

  // Delete related records first
  $dltsql1 = "DELETE FROM announcement WHERE class_id=?";
  $dltstmt1 = $conn->prepare($dltsql1);
  $dltstmt1->bind_param("i", $deleteid);
  $dltstmt1->execute();

  $dltsql2 = "DELETE FROM upload_work WHERE ass_id IN (SELECT ass_id FROM assignmnet WHERE class_id=?)";
  $dltstmt2 = $conn->prepare($dltsql2);
  $dltstmt2->bind_param("i", $deleteid);
  $dltstmt2->execute();

  $dltsql3 = "DELETE FROM attendance_details WHERE att_id IN (SELECT att_id FROM attendance WHERE class_id=?)";
  $dltstmt3 = $conn->prepare($dltsql3);
  $dltstmt3->bind_param("i", $deleteid);
  $dltstmt3->execute();

  // Then delete from join_class, assignmnet, attendance, and classes table
  $dltsql4 = "DELETE FROM join_class WHERE class_id=?";
  $dltstmt4 = $conn->prepare($dltsql4);
  $dltstmt4->bind_param("i", $deleteid);
  $dltstmt4->execute();

  $dltsql5 = "DELETE FROM assignmnet WHERE class_id=?";
  $dltstmt5 = $conn->prepare($dltsql5);
  $dltstmt5->bind_param("i", $deleteid);
  $dltstmt5->execute();

  $dltsql6 = "DELETE FROM attendance WHERE class_id=?";
  $dltstmt6 = $conn->prepare($dltsql6);
  $dltstmt6->bind_param("i", $deleteid);
  $dltstmt6->execute();

  $dltsql7 = "DELETE FROM classes WHERE course_id=?";
  $dltstmt7 = $conn->prepare($dltsql7);
  $dltstmt7->bind_param("i", $deleteid);
  $dltstmt7->execute();

  // Check if all deletion queries executed successfully
  if ( $dltstmt7->affected_rows > 0) {
      $succMsg = "Data updated successfully.";
      echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
  } else {
      $errorMsg = "Error: Unable to delete class.";
      echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}'; }, 2000);</script>";
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
  <title>Manage Classes</title>
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
      <h2 class="h2">Classes Details</h2>
      <?php
      if ($result->num_rows > 0) {
      ?>
        <table class="my-4">
          <thead>
            <tr>
              <th>Subject Name</th>
              <th>Department</th>
              <th>Semester</th>
              <th>Teacher</th>
              <th>Taken Classes</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) {
            ?>

              <tr>
                <td> <?php echo $row['course_name']; ?></td>
                <td class="cen"><?php echo $row['dpt_name']; ?></td>
                <td class="cen"><?php echo $row['semester']; ?></td>
                <td> <?php echo $row['teacher_name']; ?></td>
                <td class="cen"><?php echo $row['total_taken_classes']; ?></td>
                <td>
                  <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['course_id']; ?>" class="btn1 dlt delete"><i class="bi bi-x-lg"></i></a>
                </td>
              </tr>

          <?php
            }
          } else {
            echo "<h4> No classes found....</h4>";
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