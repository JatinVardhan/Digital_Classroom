<?php
session_start();
include '../Home/Connection.php';
include "navbar.php";

if(strlen($_SESSION['login']) != 1) {
    echo "<script> window.location.href = '../Home/Login.php'; </script>";
}

$row = $errorMsg = $succMsg =  $edit_id = $formattedDateTime = $att_id = "";
$students = [];
$attendanceDetails = [];

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
        $formattedDate = $dateTime->format('Y-m-d');
        $formattedTime = $dateTime->format('H:i');
        $att_description = $attendance['att_description'];
    } else {
        $errorMsg = "No attendance found with the provided ID.";
    }

    // Fetch students who joined the class
    $sql = "SELECT students.* FROM students 
            LEFT JOIN join_class ON students.student_rollno = join_class.std_id 
            WHERE join_class.class_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $r1 = $stmt->get_result();
    while ($st_row = $r1->fetch_assoc()) {
        $students[] = $st_row;
    }

    // Fetch student attendance details
    $sql = "SELECT students.student_rollno, students.student_name, students.phone_number, attendance_details.attendance
            FROM attendance_details 
            JOIN students ON attendance_details.std_id = students.student_rollno
            WHERE attendance_details.att_id = '$att_id'";
    $attendanceDetailsResult = $conn->query($sql);
    if ($attendanceDetailsResult->num_rows > 0) {
        while ($row = $attendanceDetailsResult->fetch_assoc()) {
            $attendanceDetails[$row['student_rollno']] = $row;
        }
    }
} else {
    $errorMsg = "No attendance ID provided.";
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $attdate = $conn->real_escape_string($_POST['attdate']);
    $atttime = $conn->real_escape_string($_POST['atttime']);
    $att_description = $conn->real_escape_string($_POST['att_description']);
    $attendance_status = isset($_POST['attendance_status']) ? $_POST['attendance_status'] : [];

    // Update attendance table
    $datetime = $attdate . ' ' . $atttime;
    $updateAttendanceSql = "UPDATE attendance SET date_time = ?, att_description = ? WHERE att_id = ?";
    $stmt = $conn->prepare($updateAttendanceSql);
    $stmt->bind_param("ssi", $datetime, $att_description, $att_id);
    $attendanceUpdateResult = $stmt->execute();

    // Check if attendance table update was successful
    $attendanceUpdateSuccess = ($stmt->affected_rows > 0);

    // Update attendance_details table
    $attendanceDetailsUpdateSuccess = true;
    $updateAttendanceDetailsSql = "UPDATE attendance_details SET attendance = ? WHERE att_id = ? AND std_id = ?";
    $stmt = $conn->prepare($updateAttendanceDetailsSql);
    foreach ($students as $student) {
        $rollno = $student['student_rollno'];
        $attendance = isset($attendance_status[$rollno]) ? 1 : 0;
        $stmt->bind_param("iii", $attendance, $att_id, $rollno);
        if (!$stmt->execute()) {
            $attendanceDetailsUpdateSuccess = false;
            break;
        }
    }

    // Determine the success of the updates
    if ($attendanceUpdateSuccess || $attendanceDetailsUpdateSuccess) {
        $succMsg = "Attendance updated successfully.";
        echo "<script> setTimeout(function(){ window.location.href = 'VeiwAttendance.php?class_id= $edit_id&att_id= $att_id'; }, 2000);</script>";
    } else {
        $errorMsg = "No updates were made.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style1.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <title>View Attendance</title>
  <style>
    .modal-content {
        background-color: #fefefe;
        margin: 8% auto;
        width: 60%!important;
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
        background-color:#3d8def !important;
        color: white !important;
        padding: 0 20px;
        border-radius: 10px 10px 0 0;
    }

    .ass_body {
        padding: 10px;
        margin: 0 6%;
    }

    .marks-due {
        display: flex;
        justify-content: space-between;
        margin-left: 10%;
        padding-top: 5px;
        font-size: small;
    }
    
    .row1 {
        display: flex;
        justify-content: space-between;
    }

    .h2 span {
        float: left !important;
    }
    table,
td,
th {
  border: 3px solid rgb(0, 0, 0) !important;
  border-collapse: collapse !important;
  padding:6px 8px !important;
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
        <div class="modal-content">
            <h2 class="h2"> 
                <span class="close" onclick="window.location.href = 'VeiwAttendance.php?class_id=<?php echo $edit_id;?>&att_id=<?php echo $att_id;?>';">
                    <i class="fa-solid fa-arrow-left-long"></i>
                </span>
                <i class="fa-regular fa-clipboard"></i>&nbsp; ATTENDANCE 
            </h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo $edit_id;?>&att_id=<?php echo $att_id;?>>" method="POST" class="col-sm-10 p-3 mx-auto">
                <div class="row1 mb-2">
                    <div class="form-group col-md-5">
                        <label for="attdate" class="file-upload">Date</label><br>
                        <input type="date" class="form-control" id="attdate" name="attdate" value="<?php echo $formattedDate; ?>" />
                    </div>
                    <div class="form-group col-md-6">
                        <label for="atttime" class="file-upload">Time</label><br>
                        <input type="time" class="form-control" id="atttime" name="atttime" value="<?php echo $formattedTime; ?>" />
                    </div>
                </div>
                <div class="form-group mb-2">
                    <textarea class="form-control" id="attDescription" name="att_description" rows="3" style="resize: none;" placeholder="Description (optional)"><?php echo $att_description; ?></textarea>
                </div>
                <div class="form-group mb-2">
                    <?php if (!empty($students)) : ?>
                        <table class="my-4">
                            <thead>
                                <tr>
                                    <th>Roll Number</th>
                                    <th>Name</th>
                                    <th>Phone Number</th>
                                    <th colspan="2">Attendance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student) : ?>
                                    <?php 
                                    $rollno = $student['student_rollno'];
                                    $isPresent = isset($attendanceDetails[$rollno]) && $attendanceDetails[$rollno]['attendance'] == 1;
                                    ?>
                                    <tr>
                                        <td><?php echo $student['student_rollno']; ?></td>
                                        <td><?php echo $student['student_name']; ?></td>
                                        <td><?php echo $student['phone_number']; ?></td>
                                        <td>
                                            <input type="checkbox" class="attendance-checkbox" name="attendance_status[<?php echo $student['student_rollno']; ?>]" onclick="handleCheckbox(this)" <?php echo $isPresent ? 'checked' : ''; ?>> Present
                                        </td>
                                        <td>
                                            <input type="checkbox" class="attendance-checkbox " onclick="handleCheckbox(this)" <?php echo !$isPresent ? 'checked' : ''; ?>> Absent
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else : ?>
                        <h4>No Students Found...</h4>
                    <?php endif; ?>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn1" name="update"><i class="bi bi-arrow-repeat"></i>&nbsp; Update</button>
                    </div>
                </div>
            </form>
        </div>
  </main>
  <Script>
       function handleCheckbox(checkbox) {
            var parentRow = checkbox.closest('tr');
            var checkboxesInRow = parentRow.querySelectorAll('.attendance-checkbox');
            checkboxesInRow.forEach(function(cb) {
                if (cb !== checkbox) {
                    cb.checked = false;
                }
            });
        }

  </Script>
</body>

</html>
