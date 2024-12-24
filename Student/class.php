<?php
session_start();
include '../Home/Connection.php';
include "navbar.php";
if (strlen($_SESSION['login']) != 1) {
    echo "<script> window.location.href = '../Home/Login.php'; </script>";
}

$row = $ass_row = $ann_row = $errorMsg = $succMsg = $class_id =    $DueDate = $UploadDate = "";
$due_date = new DateTime();
$due_date->modify('+1 week');

if (isset($_GET['class_id'])) {
    $class_id = $conn->real_escape_string($_GET['class_id']);
    $sql = "SELECT classes.*, teachers.teacher_name FROM classes JOIN teachers ON classes.teacher_id = teachers.teacher_id WHERE classes.course_id=?";
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

// Fetch attendance information for the class
$attQuery = "SELECT * FROM attendance WHERE class_id=? ORDER BY date_time DESC";
$attStmt = $conn->prepare($attQuery);
$attStmt->bind_param("i", $class_id);
$attStmt->execute();
$attResult = $attStmt->get_result();

// Fetching assignments related to the class
$assQuery = "SELECT * FROM assignmnet WHERE class_id=?";
$assStmt = $conn->prepare($assQuery);
$assStmt->bind_param("s", $class_id);
$assStmt->execute();
$assResult = $assStmt->get_result();

// Fetching announcements related to the class
$annQuery = "SELECT * FROM announcement WHERE class_id=?";
$annStmt = $conn->prepare($annQuery);
$annStmt->bind_param("s", $class_id);
$annStmt->execute();
$annResult = $annStmt->get_result();

// Merging assignments and announcements
$mergedResults = array_merge_recursive($assResult->fetch_all(MYSQLI_ASSOC), $annResult->fetch_all(MYSQLI_ASSOC));
function sortByUploadTime($a, $b)
{
    return strtotime($b['upload_date']) - strtotime($a['upload_date']);
}
usort($mergedResults, 'sortByUploadTime');

if (isset($_GET['unenrole_id'])) {
    $unenrole = $conn->real_escape_string($_GET['unenrole_id']);
    $dltsql = "DELETE FROM join_class WHERE std_id=? AND class_id=?";
    $dltstmt = $conn->prepare($dltsql);
    $dltstmt->bind_param("si", $unenrole, $class_id);
    if ($dltstmt->execute()) {

        echo "<script>  window.location.href = 'SDashboard.php'; </script>";
    } else {
        $errorMsg = "Error :" . $dltstmt->error;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assignment_id'])) {
    $student_id = $_SESSION['s_id'];
    $assignment_id = $conn->real_escape_string($_POST['assignment_id']);
    $class_id = $_GET['class_id']; // Assuming class_id is obtained from URL

    // Check if the assignment is already uploaded by the student
    $check_sql = "SELECT * FROM upload_work WHERE ass_id = ? AND std_id = ? ";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $assignment_id, $student_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $errorMsg = "You have already uploaded the work for this assignment.";
    } else {
        // Retrieve the due date for the assignment
        $due_date_sql = "SELECT due_date FROM assignmnet WHERE ass_id = ?";
        $due_date_stmt = $conn->prepare($due_date_sql);
        $due_date_stmt->bind_param("i", $assignment_id);
        $due_date_stmt->execute();
        $due_date_result = $due_date_stmt->get_result();

        if ($due_date_result->num_rows > 0) {
            $due_date_row = $due_date_result->fetch_assoc();
            $due_date_str = $due_date_row['due_date'];

            if (!empty($due_date_str)) {
                $due_date = new DateTime($due_date_str);
            }
        }

        // Process uploaded files
        $file_paths = [];
        $target_directory = "../images/"; // Directory to store uploaded files

        if (!empty($_FILES['work_file']['name'][0])) {
            foreach ($_FILES['work_file']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['work_file']['name'][$key];
                $file_tmp = $_FILES['work_file']['tmp_name'][$key];
                $file_error = $_FILES['work_file']['error'][$key];

                // Check if file is uploaded successfully
                if ($file_error === 0) {
                
                    $file_new_name = uniqid('file_') . '_' . $file_name;
                    $target_path = $target_directory . $file_new_name;

                    if (move_uploaded_file($file_tmp, $target_path)) {
                        $file_paths[] = $target_path; // Store file path
                    }
                }
            }
        }

        // Convert file paths array to JSON string
        $media_files_json = json_encode($file_paths);

        // Store uploaded work file paths into the database
        $sql = "INSERT INTO upload_work (ass_id, std_id, ass_file) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $assignment_id, $student_id,  $media_files_json);

        if ($stmt->execute()) {
            $current_date = new DateTime();
            if ($current_date > $due_date) {
                $errorMsg = "Work uploaded late.";
            } else {
                $succMsg = "Work uploaded successfully...";
                echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id=$class_id'; }, 2000);</script>";
            }
        } else {
            $errorMsg = "Failed to upload work.";
        }
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
    <title>Class</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

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

        body.modal-open {
            filter: blur(5px);
            -webkit-filter: blur(5px);
            pointer-events: none;
        }

        body.modal-open main {
            overflow: hidden;
        }

        .ass_head {
            display: flex;
            justify-content: space-between;
            background-color: rgb(58, 58, 70) !important;
            color: white !important;
            padding: 0 20px;
            border-radius: 10px 10px 0 0;

        }

        .ass_body {
            padding: 10px;
            margin: 0 6%;
        }

        .ass-detail {
            color: var(--first-color-light);
            margin: 0 6%;
            margin-top: -10px;
            font-size: small;
        }

        .marks-due {
            display: flex;
            justify-content: space-between;
            margin-left: 6%;
            padding-top: 5px;
            font-size: small;
        }

        .btn_choose {
            background: rgb(58, 58, 70);
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            padding: .4em 1.5em;
            margin-left: 1em;
            box-shadow: 2px 2px 5px lightgray;
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

                        <li class="li">
                            <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                <li><a class=" delete1 tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?unenrole_id=<?php echo $_SESSION['s_id']; ?>"> Leave Class </a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <h1><?php echo $row['course_name']; ?></h1>
                <p><?php echo $row['description']; ?></p>
            </div>
            <section>
                <?php if ($assResult->num_rows === 0 && $annResult->num_rows === 0) : ?>
                    <?php if ($row['archive'] == 0) : ?>
                        <h2 class=" my-4"> Post your work here...</h2>
                    <?php else : ?>
                        <h2 class=" my-4"> You didn't posted anything...</h2>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="post1">
                        <h1><b>Attendance</b></h1>
                        <?php while ($atten_row = $attResult->fetch_assoc()) : ?>
                            <?php
                            $attendanceDateTime = new DateTime($atten_row['date_time']);
                            $formattedDate = $attendanceDateTime->format('Y-m-d');
                            $formattedTime = $attendanceDateTime->format('h:i A');
                            ?>
                            <div class="class-activity" onclick=" window.location.href = 'VeiwAttendance.php?class_id=<?php echo $class_id; ?>&att_id=<?php echo $atten_row['att_id'] ?>';">
                                <div class="icon1" style="background-color :rgb(58, 58, 70) !important;">
                                    <svg xmlns="http://www.w3.org/2000/svg" height="21" width="21" viewBox="0 0 448 512">
                                        <path fill="#ffffff" d="M128 0c13.3 0 24 10.7 24 24V64H296V24c0-13.3 10.7-24 24-24s24 10.7 24 24V64h40c35.3 0 64 28.7 64 64v16 48V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V192 144 128C0 92.7 28.7 64 64 64h40V24c0-13.3 10.7-24 24-24zM400 192H48V448c0 8.8 7.2 16 16 16H384c8.8 0 16-7.2 16-16V192zM329 297L217 409c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47 95-95c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z" />
                                    </svg>
                                </div>
                                <div class="content1">
                                    <h6 class="m-0 p-0"><strong>Attendance : </strong><?php echo  $formattedDate; ?></h6>
                                    <p class="time"><?php echo  $formattedTime; ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="post2">
                        <?php foreach ($mergedResults as $post2_row) : ?>
                            <?php
                            $currentDate = date_create(date('Y-m-d'));
                            if (strtotime($post2_row['upload_date']) !== false) {
                                $uploadDate = date_create($post2_row['upload_date']);

                                if ($uploadDate->format('Y-m-d') == $currentDate->format('Y-m-d')) {
                                    $UploadDate = $uploadDate->format('h:i A');
                                } elseif ($uploadDate->format('Y-m-d') == date('Y-m-d', strtotime('-1 day'))) {
                                    $UploadDate = 'Yesterday';
                                } else {
                                    $UploadDate = date_format($uploadDate, 'd M Y');
                                }
                            } else {
                                $UploadDate = "";
                            }
                            ?>
                            <?php if (isset($post2_row['ass_name'])) : ?>
                                <?php
                                if (strtotime($post2_row['due_date']) !== false) {
                                    $dueDate = date_create($post2_row['due_date']);

                                    if ($dueDate->format('Y-m-d') == $currentDate->format('Y-m-d')) {
                                        $DueDate = 'Today, ' . date_format($dueDate, 'H:i');
                                    } elseif ($dueDate->format('Y-m-d') == date('Y-m-d', strtotime('+1 day'))) {
                                        $DueDate = 'Tomorrow, ' . date_format($dueDate, 'H:i');
                                    } else {
                                        $DueDate = date_format($dueDate, 'd M Y, H:i');
                                    }
                                } else {
                                    $DueDate = "";
                                }
                                ?>
                                <!-- Display assignment -->
                                <div class="class-activity" onclick="showModal('<?php echo $post2_row['ass_id']; ?>','<?php echo $post2_row['ass_name']; ?>', '<?php echo $post2_row['ass_description']; ?>', '<?php echo htmlspecialchars($post2_row['ass_media'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo $row['teacher_name'] . ' - ' .  $UploadDate; ?>', '<?php echo $post2_row['marks']; ?>', '<?php echo $DueDate; ?>')">
                                    <div class="icon1">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="23" width="23" viewBox="0 0 384 512">
                                            <path fill="#ffffff" d="M280 64h40c35.3 0 64 28.7 64 64V448c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128C0 92.7 28.7 64 64 64h40 9.6C121 27.5 153.3 0 192 0s71 27.5 78.4 64H280zM64 112c-8.8 0-16 7.2-16 16V448c0 8.8 7.2 16 16 16H320c8.8 0 16-7.2 16-16V128c0-8.8-7.2-16-16-16H304v24c0 13.3-10.7 24-24 24H192 104c-13.3 0-24-10.7-24-24V112H64zm128-8a24 24 0 1 0 0-48 24 24 0 1 0 0 48z" />
                                        </svg>
                                    </div>
                                    <div class="content2">
                                        <p class="m-0 p-0"><strong><?php echo $row['teacher_name']; ?> posted a new assignment: </strong><?php echo $post2_row['ass_name']; ?></p>
                                        <p class="time"><?php echo  $UploadDate; ?></p>
                                    </div>
                                    <div> <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false" style="color:black">
                                            <i class="fas fa-ellipsis-v px-3"></i>
                                        </a>
                                        <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                            <li><a class="tools" href="#"> Edit </a></li>
                                            <li><a class="tools" href="#"> Delete </a></li>
                                        </ul>
                                    </div>
                                </div>

                            <?php elseif (isset($post2_row['ann_text'])) : ?>
                                <!-- Display announcement -->
                                <div class="class-activity" style="flex-direction:column;">
                                    <div class="class_ann" style="display:flex;align-items:center;width:100%;">
                                        <div class="icon1">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512">
                                                <path fill="#ffffff" d="M480 32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9L381.7 53c-48 48-113.1 75-181 75H192 160 64c-35.3 0-64 28.7-64 64v96c0 35.3 28.7 64 64 64l0 128c0 17.7 14.3 32 32 32h64c17.7 0 32-14.3 32-32V352l8.7 0c67.9 0 133 27 181 75l43.6 43.6c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6V300.4c18.6-8.8 32-32.5 32-60.4s-13.4-51.6-32-60.4V32zm-64 76.7V240 371.3C357.2 317.8 280.5 288 200.7 288H192V192h8.7c79.8 0 156.5-29.8 215.3-83.3z" />
                                            </svg>
                                        </div>
                                        <?php
                                        $ann_text = $post2_row['ann_text'];
                                        $max_length = 60;
                                        $ellipsis = strlen($ann_text) > $max_length ? '...' : '';
                                        ?>
                                        <div class="content2">
                                            <p class="m-0 p-0"><strong>New announcement: </strong><span class="announce-text"><?php echo substr($ann_text, 0, $max_length) . $ellipsis; ?></span></p>
                                            <p class="time"><?php echo  $UploadDate; ?></p>
                                        </div>
                                        <div> <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false" style="color:black">
                                                <i class="fas fa-ellipsis-v px-3"></i>
                                            </a>
                                            <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                                <li><a class="tools" href="#"> Edit </a></li>
                                                <li><a class="tools" href="#"> Delete </a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="view-more" onclick="toggleViewContent(this)">
                                        <div class="view-content" style="display: none;">
                                            <p><?php echo $post2_row['ann_text']; ?></p>
                                            <?php if (!empty($post2_row['ann_media'])) : ?>
                                                <div class="attachment mx-auto my-3">
                                                    <?php
                                                    $media_files = json_decode($post2_row['ann_media'], true); // Decode JSON string into array
                                                    ?>

                                                    <?php foreach ($media_files as $media_file) : ?>
                                                        <?php
                                                        $file_name = basename($media_file);
                                                        $file_name_without_unique = preg_replace('/_([^_]+)$/', '', $file_name);
                                                        ?>

                                                        <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $media_file)) : ?>
                                                            <div class="attachment_item">
                                                                <a href="<?php echo $media_file; ?>" target="_blank">
                                                                    <img src="<?php echo $media_file; ?>" alt="Attachment" width="100" height="100">
                                                                    <figcaption><?php echo $file_name_without_unique; ?></figcaption>
                                                                </a>
                                                            </div> 
                                                        <?php else : ?>
                                                            
                                                            <div class="attachment_item">
                                                                <a href="<?php echo $media_file; ?>" target="_blank">
                                                                    <img src="../media/images/file.png" alt="Attachment" width="100" height="100">
                                                                    <figcaption><?php echo $file_name_without_unique; ?></figcaption>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>


                                        <div class="dropdown-divider my-2 mx-5 "></div>
                                        <p class="view-more-text">View More</p>
                                    </div>
                                </div>
                                <!-- <div class="dropdown-divider my-0 mx-1"></div> -->

                            <?php endif; ?>

                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
    <div id="assignmentModal" class="modal">

        <div class="modal-content">
            <div class="ass_head">
                <div style="width:90%">
                    <h3 class="h3 mx-0"><i class="fa-regular fa-clipboard "></i> <span id="assignmentTitle"></span></h3>
                    <p class="ass-detail" id="assignmentDetails"></p>
                    <p class="marks-due">
                        <span id="assignmentMarks"></span>
                        <span id="assignmentDue"></span>
                    </p>
                </div>
                <span class="close" onclick="hideModal()"><i class="fa-solid fa-arrow-left-long"></i></span>
            </div>
            <div class="ass_body">
                <p class="p-2" id="assignmentDescription"></p>
                <div class="attachment" id="assignmentAttachment"></div>

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo $class_id ?>" method="POST" enctype="multipart/form-data" class="mt-5">
                    <div class="dropdown-divider"></div>
                    <div class="form-group mb-2 mx-auto" style="width:90%;">
                        <input type="hidden" name="assignment_id" id="id">
                        <div class="outer" id="selected_files_container"></div>
                        <div class="form-group my-2 text-center">
                            <label for="work_file" class="form-label">Upload Work</label>
                            <input type="file" id="work_file" name="work_file[]" style="display: none;" multiple required>
                            <button type="button" class="btn_choose" onclick="document.getElementById('work_file').click();">Choose File</button>
                        </div>
                        <div class="text-center my-4">
                            <button type="submit" class="btn1">Submit</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <div id="attendanceModal" class="modal fade" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attendanceModalLabel">Attendance Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6><strong>Date: </strong><span id="attendanceDate"></span></h6>
                    <h6><strong>Time: </strong><span id="attendanceTime"></span></h6>
                    <h6><strong>Description: </strong><span id="attendanceDescription"></span></h6>
                    <h6><strong>Absent Students: </strong></h6>
                    <ul id="absentStudentsList"></ul>
                </div>
            </div>
        </div>
    </div>
    <script>
        function setAssignmentId(id) {
            document.getElementById("id").value = id;
        }

        function showModal(id, title, description, attachment, details, marks, dueDate) {
            var modal = document.getElementById("assignmentModal");
            var titleElem = document.getElementById("assignmentTitle");
            var descriptionElem = document.getElementById("assignmentDescription");
            var attachmentElem = document.getElementById("assignmentAttachment");
            var detailsElem = document.getElementById("assignmentDetails");
            var marksElem = document.getElementById("assignmentMarks");
            var dueElem = document.getElementById("assignmentDue");
            var idElem = document.getElementById("id");

            titleElem.innerText = title;
            descriptionElem.innerText = description;
            detailsElem.innerText = details;
            if (marks !== "") {
                marksElem.innerText = marks + " marks";
            } else {
                marksElem.innerText = "";
            }

            if (dueDate !== "") {
                dueElem.innerText = "Due " + dueDate;
            } else {
                dueElem.innerText = "";
            }

            if (attachment !== "") {
                var attachments = JSON.parse(attachment);
                var attachmentHtml = "";

                attachments.forEach(function(media_file) {
                    var file_name = media_file.split('/').pop();
                    var file_name_without_unique = file_name.replace(/_([^_]+)$/, '');
                    var imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];

                    var extension = media_file.split('.').pop().toLowerCase();
                    if (!imageExtensions.includes(extension)) {
                        // Non-image file
                        attachmentHtml += '<div class="attachment_item">' +
                            '<a href="' + media_file + '" target="_blank">' +
                            '<img src="../media/images/file.png" alt="Attachment" width="100" height="100">' +
                            '<figcaption>' + file_name_without_unique + '</figcaption>' +
                            '</a></div>';
                    } else {
                        // Image file
                        attachmentHtml += '<div class="attachment_item">' +
                            '<a href="' + media_file + '" target="_blank">' +
                            '<img src="' + media_file + '" alt="Attachment" width="100" height="100">' +
                            '<figcaption>' + file_name_without_unique + '</figcaption>' +
                            '</a></div>';
                    }
                });

                attachmentElem.innerHTML = attachmentHtml;
            } else {
                attachmentElem.innerHTML = "";
            }

            idElem.value = id;
            modal.style.display = "block";
        }

        // Function to hide modal
        function hideModal() {
            var modal = document.getElementById("assignmentModal");
            modal.style.display = "none";
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            var modal = document.getElementById("assignmentModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };


        document.addEventListener("DOMContentLoaded", function() {
            var deleteButtons = document.getElementsByClassName("delete1");

            for (var i = 0; i < deleteButtons.length; i++) {
                deleteButtons[i].addEventListener("click", confirmDelete);
            }

            function confirmDelete() {
                var confirmation = confirm("Are you sure you want to leave this class ?");
                if (!confirmation) {
                    event.preventDefault();
                    return false;
                }
            }
        });

        // Function to handle file input change event
        document.getElementById('work_file').addEventListener('change', handleFileSelect);

        function handleFileSelect(event) {
            const files = event.target.files;
            const selectedFilesContainer = document.getElementById('selected_files_container');


            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const fileItem = document.createElement('div');
                const fileName = document.createElement('span');
                const removeIcon = document.createElement('span');

                fileName.textContent = file.name;
                fileItem.style.width = 'fit-content';
                fileItem.style.backgroundColor = '#dbe5e8';
                fileItem.style.borderRadius = '5px';
                fileItem.style.margin = '5px';
                fileItem.style.padding = '5px';


                removeIcon.innerHTML = '&nbsp;<i class="bi bi-x-lg pr"></i>';
                removeIcon.classList.add('remove-file');
                removeIcon.addEventListener('click', function() {
                    fileItem.remove();
                });

                fileItem.appendChild(fileName);
                fileItem.appendChild(removeIcon);


                selectedFilesContainer.appendChild(fileItem);
            }
        }


        function toggleViewContent(clickedElement) {
            var parentActivity = clickedElement.closest('.class-activity');
            var viewContent = parentActivity.querySelector('.view-content');
            var viewMoreText = parentActivity.querySelector('.view-more-text');
            var announcementText = parentActivity.querySelector('.announce-text');

            if (viewContent.style.display === "none" || !viewContent.style.display) {
                viewContent.style.display = "block";
                viewContent.style.height = "auto";
                viewMoreText.innerText = "View Less";
                announcementText.style.display = "none";
            } else {
                viewContent.style.display = "none";
                viewContent.style.height = "0";
                viewMoreText.innerText = "View More";
                announcementText.style.display = "inline";
            }
        }
    </script>

</body>

</html>

