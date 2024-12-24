<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";

$row = $errorMsg = $succMsg = $class_id = "";

if (isset($_GET['class_id'])) {
    $class_id = $conn->real_escape_string($_GET['class_id']);
    $sql = "SELECT * FROM classes WHERE course_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        $errorMsg = "No record found...";
    }
} else {
    $errorMsg = "No id provided...";
}
$dpt_id = $row['dpt_id'];
$sem_id = $row['semester'];
$classsql = "SELECT COUNT(*) FROM students WHERE dpt_id=$dpt_id AND semester=$sem_id";
$classstmt = $conn->prepare($classsql);
$classstmt->execute();
$classresult = $classstmt->get_result();
$result1 = $classresult->fetch_assoc();

$classsql = "SELECT COUNT(*) FROM join_class WHERE class_id=$class_id ";
$classstmt = $conn->prepare($classsql);
$classstmt->execute();
$classresult = $classstmt->get_result();
$result2 = $classresult->fetch_assoc();

$sql = "SELECT students.*, departments.dpt_name FROM students 
LEFT JOIN join_class ON students.student_rollno = join_class.std_id 
LEFT JOIN departments ON students.dpt_id = departments.dpt_id WHERE join_class.class_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$r1 = $stmt->get_result();

$sql = "SELECT students.* FROM students
WHERE dpt_id=? AND semester=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $dpt_id, $sem_id);
$stmt->execute();
$r2 = $stmt->get_result();

if (isset($_GET['delete_id'])) {
    $delete_student_id = $conn->real_escape_string($_GET['delete_id']);
    $class_id =$_GET['class_id'];
    // Begin a transaction
    $conn->begin_transaction();
    try {
        // Delete from join_class
        $delete_join_sql = "DELETE FROM join_class WHERE std_id=? AND class_id=?";
        $delete_join_stmt = $conn->prepare($delete_join_sql);
        $delete_join_stmt->bind_param("ii", $delete_student_id, $class_id);
        $delete_join_stmt->execute();
        
        // Get att_ids from attendance table for the class
        $att_ids = [];
        $attendance_sql = "SELECT att_id FROM attendance WHERE class_id=?";
        $attendance_stmt = $conn->prepare($attendance_sql);
        $attendance_stmt->bind_param("i", $class_id);
        $attendance_stmt->execute();
        $attendance_result = $attendance_stmt->get_result();
        while ($row = $attendance_result->fetch_assoc()) {
            $att_ids[] = $row['att_id'];
        }
        
        // Delete from attendance_details based on att_ids
        if (!empty($att_ids)) {
            $att_ids_placeholder = implode(',', array_fill(0, count($att_ids), '?'));
            $delete_attendance_sql = "DELETE FROM attendance_details WHERE std_id=? AND att_id IN ($att_ids_placeholder)";
            $delete_attendance_stmt = $conn->prepare($delete_attendance_sql);
            $params = array_merge([$delete_student_id], $att_ids);
            $delete_attendance_stmt->bind_param(str_repeat('i', count($params)), ...$params);
            $delete_attendance_stmt->execute();
        }
        
        // Get ass_ids from assignment table for the class
        $ass_ids = [];
        $assignment_sql = "SELECT ass_id FROM assignmnet WHERE class_id=?";
        $assignment_stmt = $conn->prepare($assignment_sql);
        $assignment_stmt->bind_param("i", $class_id);
        $assignment_stmt->execute();
        $assignment_result = $assignment_stmt->get_result();
        while ($row = $assignment_result->fetch_assoc()) {
            $ass_ids[] = $row['ass_id'];
        }
        
        // Delete from upload_work based on ass_ids
        if (!empty($ass_ids)) {
            $ass_ids_placeholder = implode(',', array_fill(0, count($ass_ids), '?'));
            $delete_upload_sql = "DELETE FROM upload_work WHERE std_id=? AND ass_id IN ($ass_ids_placeholder)";
            $delete_upload_stmt = $conn->prepare($delete_upload_sql);
            $params = array_merge([$delete_student_id], $ass_ids);
            $delete_upload_stmt->bind_param(str_repeat('i', count($params)), ...$params);
            $delete_upload_stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        
        $succMsg = "Student removed successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id=$class_id'; }, 2000);</script>";
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $errorMsg = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['delete_student_id'])) {
    $delete_student_id = $conn->real_escape_string($_GET['delete_student_id']);
    $delete_join_sql = "DELETE FROM join_class WHERE std_id=?";
    $delete_join_stmt = $conn->prepare($delete_join_sql);
    $delete_join_stmt->bind_param("i", $delete_student_id);
    if ($delete_join_stmt->execute()) {
        $succMsg = "Student removed successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id= $class_id  '; }, 2000);</script>";
    } else {
        $errorMsg = "Error :" . $dltstmt->error;
    }
}
if (isset($_GET['archive_id'])) {
    $archive_id = $conn->real_escape_string($_GET['archive_id']);
    $upd_sql = "UPDATE classes SET archive=1 WHERE course_id=?";
    $upd_stmt = $conn->prepare($upd_sql);
    $upd_stmt->bind_param("i",  $archive_id);
    if ($upd_stmt->execute()) {
        $succMsg = "Class archived successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id= $class_id  '; }, 2000);</script>";
    } else {
        $errorMsg = "Error :" . $upd_stmt->error;
    }
}
if (isset($_GET['restore_id'])) {
    $restore_id = $conn->real_escape_string($_GET['restore_id']);
    $upd_sql = "UPDATE classes SET archive=0 WHERE course_id=?";
    $upd_stmt = $conn->prepare($upd_sql);
    $upd_stmt->bind_param("i",  $restore_id);
    if ($upd_stmt->execute()) {
        $succMsg = "Class restored successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id= $class_id  '; }, 2000);</script>";
    } else {
        $errorMsg = "Error :" . $upd_stmt->error;
    }
}
if (isset($_GET['enroll_id'])) {
    $enroll_id = $conn->real_escape_string($_GET['enroll_id']);
    $enroll_sql = "INSERT INTO join_class (class_id, std_id) VALUES (?, ?)";
    $enroll_stmt = $conn->prepare($enroll_sql);
    $enroll_stmt->bind_param("ii", $class_id, $enroll_id);
    if ($enroll_stmt->execute()) {
        $succMsg = "Student enrolled successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id=$class_id'; }, 2000);</script>";
    } else {
        $errorMsg = "Error: " . $enroll_stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css?<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <title>Classt</title>
    <style>
        .dlt {
            background-color: rgb(228, 50, 50) !important;
        }

        .btn1 {
            height: 30px !important;
        }

        .dlt:focus {
            box-shadow: rgb(179, 32, 32) 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px,
                rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(179, 32, 32) 0 -3px 0 inset !important;
        }

        .dlt:hover {
            box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
                rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(179, 32, 32) 0 -3px 0 inset !important;
            transform: translateY(-2px) !important;
        }

        .dlt:active {
            box-shadow: rgb(179, 32, 32) 0 3px 7px inset !important;
            transform: translateY(2px) !important;
        }
        .class_box{
            width:77% !important;
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
        <div class="container col-11 ">
            <div class="class-card" style=" background-color: <?php echo $row['background']; ?>;">
                <div class="bar">
                    <ul class="options">
                        <?php
                        if ($row['archive'] == 0) {
                        ?>

                            <a href="class.php?class_id=<?php echo $class_id; ?>">
                                <li class="li"> Classwork</li>
                            </a>
                            <a href="studentInfo.php?class_id=<?php echo $class_id; ?>">
                                <li class="li"> Students</li>
                            </a>
                            <li class="li"> <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>

                                <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="tools" href="ClassEdit.php?class_id=<?php echo  $class_id  ?>"> Edit</a></li>
                                    <div class="dropdown-divider my-0 mx-1"></div>
                                    <li><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?archive_id=<?php echo $row['course_id']; ?>&class_id=<?php echo  $class_id  ?>">Archive</a></li>
                                    <div class="dropdown-divider my-0 mx-1"></div>
                                    <li><a class=" delete tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['course_id']; ?>&class_id=<?php echo  $class_id  ?>"> Delete </a></li>
                                </ul>
                            </li>

                        <?php
                        } else {
                        ?>
                            <li class="li"> <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </a>

                                <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?restore_id=<?php echo $row['course_id']; ?>&class_id=<?php echo  $class_id  ?>">Restore</a></li>
                                    <div class="dropdown-divider my-0 mx-1"></div>
                                    <li><a class=" delete tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['course_id']; ?>"> Delete </a></li>
                                </ul>
                            </li>
                        <?php

                        }
                        ?>
                    </ul>
                </div>
                <h1><?php echo $row['course_name']; ?></h1>
                <p><?php echo $row['description']; ?></p>

            </div>
            <section class="p-1">
                <aside style="width:22%">
                    <div class="class_work" onclick="showBox('box1')">
                        <h5>Total student count</h5>
                        <h1><?php echo $result1['COUNT(*)']; ?></h1>
                    </div>
                    <div class="class_work" onclick="showBox('box2')">
                        <h5>Enrolled student count</h5>
                        <h1><?php echo $result2['COUNT(*)']; ?></h1>
                    </div>
                </aside>
                <div class="class_box" id="box1">
                    <?php
                    if ($r2->num_rows > 0) {
                    ?>
                        <table class="my-4">
                            <thead>
                                <tr>
                                <th>Roll Number</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone Number</th>
                                    <th>Action</th> <!-- New column for Action -->
                                </tr>
                            </thead>
                            <?php while ($row = $r2->fetch_assoc()) { ?>
                                <tr>
                                <td><?php echo $row['student_rollno']; ?></td>
                                    <td><?php echo $row['student_name']; ?></td>
                                    <td><?php echo $row['student_email']; ?></td>
                                    <td><?php echo $row['phone_number']; ?></td>
                                    <td>
                                        <?php
                                        $enrolled = false;
                                        // Check if student is enrolled in the class
                                        $enrollmentQuery = "SELECT * FROM join_class WHERE class_id=? AND std_id=?";
                                        $enrollmentStmt = $conn->prepare($enrollmentQuery);
                                        $enrollmentStmt->bind_param("ii", $class_id, $row['student_rollno']);
                                        $enrollmentStmt->execute();
                                        $enrollmentResult = $enrollmentStmt->get_result();
                                        if ($enrollmentResult->num_rows > 0) {
                                            // Student is enrolled
                                            $enrolled = true;
                                        }
                                        ?>
                                        <?php if ($enrolled) : ?>
                                            <span>Enrolled</span>
                                        <?php else : ?>
                                            <!-- Add button for students not enrolled -->
                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?enroll_id=<?php echo $row['student_rollno']; ?>&class_id=<?php echo $class_id; ?>" class="btn1"><i class="bi bi-plus-lg"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>



                        <?php
                            }
                        } else {
                            echo "<h4> No Student Found....</h4>";
                        }
                        ?>
                        </tbody>
                        </table>
                </div>
                <div class="class_box" id="box2" style="display: none;">
                    <?php
                    if ($r1->num_rows > 0) {
                    ?> <table class="my-4">
                            <thead>
                                <tr>
                                <th>Roll Number</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone No.</th>
                                     <th>Sem</th>
                                     <th>Department</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $r1->fetch_assoc()) {
                                ?>

                                    <tr>
                                    <td> <?php echo $row['student_rollno']; ?></td>
                                        <td> <?php echo $row['student_name']; ?></td>
                                        <td><?php echo $row['student_email']; ?></td>
                                        <td><?php echo $row['phone_number']; ?></td>
                                        <td class="cen"><?php echo $row['semester']; ?></td>
                                        <td class="cen"><?php echo $row['dpt_name']; ?></td>
                                        <td>
                                            <a href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_student_id=<?php echo $row['student_rollno']; ?>&class_id=<?php echo $class_id; ?>" class="btn1 dlt delete1"><i class="bi bi-x-lg"></i></a>
                                        </td>

                                        </td>
                                    </tr>

                            <?php
                                }
                            } else {
                                echo "<h4>  No Student Found....</h4>";
                            }
                            ?>
                            </tbody>
                        </table>
                </div>
            </section>
        </div>
    </main>
    <script>
        function showBox(boxId) {
            var box1 = document.getElementById('box1');
            var box2 = document.getElementById('box2');
            if (boxId == 'box1') {
                box1.style.display = 'block';
                box2.style.display = 'none';
            } else if (boxId == 'box2') {
                box1.style.display = 'none';
                box2.style.display = 'block';
            }
        }

        setTimeout(function() {
            document.getElementsByClassName('alert-success')[0].remove();
        }, 2000);

        document.addEventListener("DOMContentLoaded", function() {
      var deleteButtons = document.getElementsByClassName("delete1");

      for (var i = 0; i < deleteButtons.length; i++) {
        deleteButtons[i].addEventListener("click", confirmDelete);
      }

      function confirmDelete() {
        var confirmation = confirm("Are you sure you want to remove this student ?");
        if (!confirmation) {
          event.preventDefault();
          return false;
        }
      }
    });
    </script>
</body>

</html>