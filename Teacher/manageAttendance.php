<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include "navbar.php";
include '../Home/Connection.php';
$teacher_id = $_SESSION['id'];
$row = $errorMsg = $succMsg = "";


$query = "SELECT students.*, classes.course_name, classes.total_taken_classes, departments.dpt_name, join_class.total_attendance, join_class.att_percentage
FROM students
INNER JOIN join_class ON students.student_rollno = join_class.std_id
INNER JOIN classes ON join_class.class_id = classes.course_id
INNER JOIN departments ON students.dpt_id = departments.dpt_id
WHERE classes.teacher_id = ?
AND join_class.att_percentage <= 75";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $teacher_id);
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
  <title>Attendance Alert</title>
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
      <h2 class="h2">Low Attendance Check</h2>
      <?php
      if ($result->num_rows > 0) {
      ?>
        <table class="my-4">
          <thead>
            <tr>
             <th>Name</th>
             <th>Roll Number</th>
              <th>Department</th>
              <th>Semester</th>
              <th>Class Name</th>
              <th>Total Classes</th>
              <th>Attended</th>
              <th>Attendance</th>
         
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) {
            ?>

              <tr>
              <td> <?php echo $row['student_name']; ?></td>
              <td> <?php echo $row['student_rollno']; ?></td>
                <td class="cen"><?php echo $row['dpt_name']; ?></td>
                <td class="cen" ><?php echo $row['semester']; ?></td>
                <td> <?php echo $row['course_name']; ?></td>
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