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
    $class_id = $conn->real_escape_string($_GET['class_id']);
} else {
    $errorMsg = "No class ID provided...";
}
$sql = "SELECT students.* FROM students 
LEFT JOIN join_class ON students.student_rollno = join_class.std_id WHERE join_class.class_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$r1 = $stmt->get_result();

if (isset($_POST["box3"])) {
    if (empty($_POST["attdate"]) || empty($_POST["atttime"])) {
        $errorMsg = "Date and time fields cannot be empty.";
    } else {
        $class_id = $_GET['class_id'];
        $att_date = $_POST['attdate'];
        $att_time = $_POST['atttime'];
        $att_description = $conn->real_escape_string($_POST['att_description']);
        $att_datetime = $att_date . ' ' . $att_time;
        $att_Query = "INSERT INTO attendance (date_time, att_description, class_id) VALUES (?, ?, ?)";
        $att_Stmt = $conn->prepare($att_Query);
        $att_Stmt->bind_param("ssi", $att_datetime, $att_description, $class_id);

        if ($att_Stmt->execute()) {

            $att_id = $att_Stmt->insert_id;

            // Loop through each student to get attendance status
            $r1->data_seek(0);
            if ($r1->num_rows > 0) {
                while ($row4 = $r1->fetch_assoc()) {
                    $std_id = $row4['student_rollno'];
                    $attendance_status = isset($_POST['attendance_status'][$std_id]) ? 1 : 0;
                    $att_DetailQuery = "INSERT INTO attendance_details (att_id, std_id, attendance) VALUES (?, ?, ?)";
                    $att_DetailStmt = $conn->prepare($att_DetailQuery);
                    $att_DetailStmt->bind_param("iii", $att_id, $std_id, $attendance_status);
                    if ($att_DetailStmt->execute()) {
                        $succMsg = "Attendance submitted successfully.";
                        echo "<script>setTimeout(function(){ window.location.href = 'class.php?class_id= $class_id'; }, 2000);</script>";
                    } else {
                        $att_DetailStmt = "Error: " . $att_Stmt->error;
                    }
                }
            }
        } else {
            $errorMsg = "Error: " . $att_Stmt->error;
        }
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
        .box-content {
            background-color: #fefefe;
            margin: 7% auto;
            width: 97%;
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
            margin-top: -10px;
            padding: 0;
            text-align: center !important;
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

        table {
            
            margin-left: auto !important;
            margin-right: auto !important;
        }

        table,
        td,
        th {
            border: 3px solid rgb(0, 0, 0) !important;
            border-collapse: collapse !important;
            padding:5px 6px !important;
        }
        th{
            width: 7vh !important;
        }
        td,tbody {
            width: 7vh !important;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis !important;
            height: 20px !important;
        }

        thead tr {
            background-color: rgb(58, 58, 70) !important;
            color: white !important;
            text-align: center !important;
        }

        tbody tr:nth-child(odd) {
            background-color: white !important;
        }

        tbody tr:nth-child(even) {
            background-color: rgb(214, 215, 215) !important;
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

        .row1 {
            display: inline-flex;
            flex-direction: column;
        }

        .row {
            display: flex;
            flex-direction: row;
        }

        .form1 {
            width: 90% !important;
            display: flex !important;
            flex-direction: row !important;
        }

        .tables-container {
            display: flex;
            justify-content: space-between;
        }

        .table {
            width: 32%;
            margin: 0 1%;
        }

        .table th,
        .table td {
            text-align: center;
        }
    </style>
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

        <div class="box-content" id="box3">
            <div class="ass_head">
                <span class="close" onclick=" window.location.href = 'class.php?class_id=<?php echo  $class_id; ?>';"><i class="fa-solid fa-arrow-left-long"></i></span>
                <h2 class="h2"><i class="fa-regular fa-clipboard"></i>&nbsp; ATTENDANCE </h2>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo $class_id ?>" method="POST" class="col-sm-11 p-3 mx-auto">
                <div class="row">
                    <div class="row1 mb-2 col-md-4">
                        <div class="form1">
                            <label for="due-date" class="file-upload" style="margin:3px 20px;">Date</label>
                            <input type="date" class="form-control" id="attdate" name="attdate" value="<?php echo date('Y-m-d'); ?>" />
                        </div>
                        <div class="form1">
                            <label for="time" class="file-upload" style="margin:3px 20px;">Time</label>
                            <input type="time" class="form-control" id="atttime" name="atttime" />
                        </div>
                    </div>
                    <div class="form-group mb-2 col-md-8">
                        <textarea class="form-control" id="attDescription" name="att_description" rows="3" style="resize: none;" placeholder="Description (optional)"></textarea>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <?php
                    if ($r1->num_rows > 0) {
                        $students = [];
                        while ($st_row = $r1->fetch_assoc()) {
                            $students[] = $st_row;
                        }

                        $totalStudents = count($students);
                        $chunkSize = ceil($totalStudents / 3);
                        $studentChunks = array_chunk($students, $chunkSize);
                    ?>
                        <div class="tables-container">
                            <?php foreach ($studentChunks as $chunk) { ?>
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Roll Number</th>
                                            <th>Name</th>
                                            <th>Attendance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($chunk as $st_row) { ?>
                                            <tr>
                                                <td><?php echo $st_row['student_rollno']; ?></td>
                                                <td><?php echo $st_row['student_name']; ?></td>
                                                <td>
                                                    <input type="checkbox" class="attendance-checkbox" name="attendance_status[<?php echo $st_row['student_rollno']; ?>]" style="margin:0!important"/> Present
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                        <div class="text-center mt-4">
                        <button type="button" class="btn1" onclick="checkAll()">Present All</button>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button class="btn1" type="submit" name="box3"><i class="fa-solid fa-check"></i> &nbsp;Submit</button>
                        </div>
                    <?php
                    } else {
                        echo "<h4>No Student Found....</h4>";
                    }
                    ?>
                </div>
            </form>
        </div>
    </main>

</body>
<script>
    function checkAll() {
      var checkboxes = document.querySelectorAll('.attendance-checkbox');
      checkboxes.forEach(function(checkbox) {
        checkbox.checked = true;
      });
    }
    </script>

</html>