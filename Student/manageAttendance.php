<?php
session_start();
include "navbar.php";
include '../Home/Connection.php';
$std_id = $_SESSION['s_id'];
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
$row = $errorMsg = $succMsg = "";

$query = "SELECT classes.course_name, classes.semester, classes.total_taken_classes, teachers.teacher_name, join_class.total_attendance, join_class.att_percentage
          FROM join_class
          INNER JOIN classes ON join_class.class_id = classes.course_id
          INNER JOIN teachers ON classes.teacher_id = teachers.teacher_id
          WHERE join_class.std_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $std_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <Style>
    .btn1 {
      height: 30px;
    }
  </Style>
  <title>Attendance</title>
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
      <h2 class="h2">Check Your Attendance </h2>
      <?php
      if ($result->num_rows > 0) {
      ?>
        <table class="my-4">
          <thead>
            <tr>
            <th>Class Name</th>
            <th>Teacher</th>
              <th>Semester</th>
              <th>Total classes</th>
              <th>Attendance</th>
              <th>Percentage</th>
         
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) {
            ?>

              <tr>
              <td> <?php echo $row['course_name']; ?></td>
                <td> <?php echo $row['teacher_name']; ?></td>
                <td class="cen"><?php echo $row['semester']; ?></td>
                <td class="cen"> <?php echo $row['total_taken_classes']; ?></td>
                <td class="cen"> <?php echo $row['total_attendance']; ?></td>
                <td class="cen"><?php echo $row['att_percentage']; ?>%</td>
            
              </tr>

          <?php
            }
          } else {
            echo "<h4> No student found....</h4>";
          }
          ?>
          </tbody>
        </table>
        <div class="my-2 text-center">
          <button type="button" class="btn1" onclick="goBack()" style="height: 40px;"><i class="fa-solid fa-arrow-left"></i>&nbsp; Back</button>
        </div>
    </div>
  </main>
</body>

</html>