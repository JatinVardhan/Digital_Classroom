<?php
session_start();
include '../Home/Connection.php';
include "navbar.php";

if (strlen($_SESSION['login']) != 1) {
  echo "<script> window.location.href = '../Home/Login.php'; </script>";
}

$row = $errorMsg = $succMsg =  $edit_id = $formattedDateTime = "";

$isValid = true;

if (isset($_GET['class_id'])) {
  $edit_id = $conn->real_escape_string($_GET['class_id']);
} else {
  $errorMsg = "No class ID provided...";
}

if (isset($_GET['att_id'])) {
  $att_id = $conn->real_escape_string($_GET['att_id']);

  // Fetch attendance details
  $sql = "SELECT * FROM attendance WHERE att_id = '$att_id'";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    $attendance = $result->fetch_assoc();
    $dateTime = new DateTime($attendance['date_time']);
    $formattedDateTime = $dateTime->format('d M Y g:i A');
  } else {
    $errorMsg = "No attendance found with the provided ID.";
  }

  // Fetch student attendance details
  $sql = "SELECT students.student_rollno, students.student_name, students.phone_number, attendance_details.attendance
            FROM attendance_details 
            JOIN students ON attendance_details.std_id = students.student_rollno
            WHERE attendance_details.att_id = '$att_id'";
  $attendanceDetailsResult = $conn->query($sql);
  $attendanceDetails = [];
  if ($attendanceDetailsResult->num_rows > 0) {
    while ($row = $attendanceDetailsResult->fetch_assoc()) {
      $attendanceDetails[] = $row;
    }
  }
} else {
  $errorMsg = "No attendance ID provided.";
}
if (isset($_GET['delete_att_id'])) {
  $delete_att_id = $conn->real_escape_string($_GET['delete_att_id']);

  // Begin a transaction
  $conn->begin_transaction();
  try {
    // Delete from attendance_details
    $delete_details_sql = "DELETE FROM attendance_details WHERE att_id=?";
    $delete_details_stmt = $conn->prepare($delete_details_sql);
    $delete_details_stmt->bind_param("i", $delete_att_id);
    $delete_details_stmt->execute();

    // Delete from attendance
    $delete_att_sql = "DELETE FROM attendance WHERE att_id=?";
    $delete_att_stmt = $conn->prepare($delete_att_sql);
    $delete_att_stmt->bind_param("i", $delete_att_id);
    $delete_att_stmt->execute();

    // Commit transaction
    $conn->commit();

    $succMsg = "Attendance deleted successfully.";
    echo "<script>setTimeout(function(){ window.location.href = 'class.php?class_id= $edit_id ' ;}, 2000);</script>";
  } catch (Exception $e) {
    // Rollback transaction
    $conn->rollback();
    $errorMsg = "Error: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <title>View Attendance</title>
  <style>
    .modal-content {
      background-color: #fefefe;
      margin: 8% auto;
      width: 60%;
      max-width: 600px;
      border-radius: 10px;
      position: relative;
    }

    .close {
      color: var(--first-color-light);
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px; 
    }

    .close:hover,
    .close:focus {
      color: white;
      text-decoration: none;
      cursor: pointer;

    }


    .ass_head {
      display: flex;
      justify-content: space-between;
      background-color: #3d8def !important;
      color: white !important;
      padding: 0 20px;
      border-radius: 10px 10px 0 0;

    }

    .ass_body {
      padding: 10px;
      margin: 0 6%;
    }

    .ass_head p { 
      margin-top:-10px;
      padding: 0 ;
      text-align: center!important;
      font-size: small;
    }

    h2 {
      text-align: center !important;
      font-family: "Merienda", cursive !important;
      font-weight: 700 !important;

      padding: 15px !important;
      border-radius: 10px 10px 0 0;
      color: white !important;
    }

    .tools {
      color: black !important;
      margin: 10px !important;
      text-decoration: none !important;
    }

    .tools:hover {
      color: black !important;
      color: gary !important;
      text-decoration: none !important;
    }
  </style>
</head>

<body>
  <main>
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
    <div class="modal-content">
    <div class="ass_head">
        <div style="width: 5%!important;margin:15px 0; ">
          <span class="close " onclick=" window.location.href = 'class.php?class_id=<?php echo  $edit_id; ?>';"><i class="fa-solid fa-arrow-left-long"></i></span>
        </div>
        <div style="width:90%">
          <h2>Attendance Details</h2>
          <p class="mark_due"><?php echo $formattedDateTime; ?></p>
        </div>
        <div style="width: 5%!important;margin:20px 0;font-size:larger; ">
          <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false" style="color:white">
            <i class="fas fa-ellipsis-v px-3"></i>
          </a>
          <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
            <li><a class="tools" href="editAttendance.php?class_id=<?php echo  $edit_id; ?>&att_id=<?php echo $attendance['att_id']; ?>"> Edit </a></li>
            <li><a class="tools"  href="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo  $edit_id; ?>&att_id=<?php echo $attendance['att_id'];?>&delete_att_id=<?php echo $attendance['att_id']; ?>"> Delete </a></li>
          </ul>
        </div>
      </div>
     
      <div class="ass_body">
        <p class="p-2"><?php echo $attendance['att_description']; ?></p>
        <table class="table mb-4">
          <thead>
            <tr>
              <th>Roll Number</th>
              <th>Name</th>
              <th>Phone Number</th>
              <th>Attendance</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($attendanceDetails as $detail) : ?>
              <tr>
                <td><?php echo $detail['student_rollno']; ?></td>
                <td><?php echo $detail['student_name']; ?></td>
                <td><?php echo $detail['phone_number']; ?></td>
                <td><?php echo $detail['attendance'] == 1 ? 'Present' : 'Absent'; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</body>

</html>