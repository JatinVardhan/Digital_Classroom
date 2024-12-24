<?php
session_start();
if (strlen($_SESSION['login']) != 1) {
    echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";

// Fetching class details and handling errors
$row = $errorMsg = $succMsg = $class_id = $UploadDate = "";

if (isset($_GET['class_id'])) {
    // Fetching class details
    $class_id = $conn->real_escape_string($_GET['class_id']);
    $sql = "SELECT classes.*, teachers.teacher_name FROM classes
      INNER JOIN teachers ON classes.teacher_id = teachers.teacher_id
     WHERE course_id=?";
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
include 'records.php';
include 'operations.php';

// Adding announcement to the class
if (isset($_POST["box1"])) {
    $ann_text = $conn->real_escape_string($_POST['ann_text']);
    $class_id = $_GET['class_id'];

    // Process uploaded files
    $file_paths = [];
    $target_directory = "../images/"; // Directory to store uploaded files

    if (!empty($_FILES['work_file']['name'][0])) {
        foreach ($_FILES['work_file']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['work_file']['name'][$key];
            $file_tmp = $_FILES['work_file']['tmp_name'][$key];
            $file_type = $_FILES['work_file']['type'][$key];
            $file_size = $_FILES['work_file']['size'][$key];
            $file_error = $_FILES['work_file']['error'][$key];

            // Check if file is uploaded successfully
            if ($file_error === 0) {
                // Generate unique file name
                $file_new_name = uniqid('file_') . '_' . $file_name;
                $target_path = $target_directory . $file_new_name;

                // Move file to target directory
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $file_paths[] = $target_path; // Store file path
                }
            }
        }
    }

    if (!empty($file_paths) || !empty($ann_text)) {
        // Convert file paths array to JSON string
        $media_files_json = json_encode($file_paths);

        // Store announcement text and media file paths into the database
        $sql = "INSERT INTO announcement (ann_text, ann_media, class_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $ann_text, $media_files_json, $class_id);

        if ($stmt->execute()) {
            $succMsg = "Announcement made successfully...";
            echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id= $class_id  '; }, 2000);</script>";
        } else {
            $errorMsg = "Failed to store announcement.";
        }
    } else {
        $errorMsg = "Please provide announcement text or upload files.";
    }
}
if (isset($_POST["box2"])) {

    $ass_name = $conn->real_escape_string($_POST['ass_name']);

    // Process uploaded files
    $file_paths = [];
    $target_directory = "../images/"; // Directory to store uploaded files

    if (!empty($_FILES['work_file']['name'][0])) {
        foreach ($_FILES['work_file']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['work_file']['name'][$key];
            $file_tmp = $_FILES['work_file']['tmp_name'][$key];
            $file_type = $_FILES['work_file']['type'][$key];
            $file_size = $_FILES['work_file']['size'][$key];
            $file_error = $_FILES['work_file']['error'][$key];

            // Check if file is uploaded successfully
            if ($file_error === 0) {
                // Generate unique file name
                $file_new_name = uniqid('file_') . '_' . $file_name;
                $target_path = $target_directory . $file_new_name;

                // Move file to target directory
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $file_paths[] = $target_path; // Store file path
                }
            }
        }
    }

    // Convert file paths array to JSON string
    $media_files_json = json_encode($file_paths);

    // Get other form data
    $ass_description = $conn->real_escape_string($_POST['ass_description']);
    $marks = !empty($_POST['marks']) ? intval($_POST['marks']) : null;
    $ass_date = $_POST['due_date'];
    $ass_time = $_POST['time'];
    $due_date = $ass_date ." ".$ass_time;
    $class_id = $_GET['class_id'];

    // Store assignment details into the database
    $sql = "INSERT INTO assignmnet (ass_name, ass_description, ass_media, due_date, marks, class_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $ass_name, $ass_description, $media_files_json, $due_date, $marks, $class_id);

    if ($stmt->execute()) {
        $succMsg = "Assignment added successfully...";
       echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id= $class_id  '; }, 2000);</script>";
    } else {
        $errorMsg = "Failed to add assignment.";
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
            padding-left: 70px;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .class_code {
            padding: 15px;
            border-radius: 10px;
            background-color: white;
            margin: 15px 0;
            box-shadow: 2px 2px 5px lightgray;

        }

        .code {
            list-style: none;
            padding: 0;
            display: flex;
            flex-direction: row;
            color: rgb(58, 58, 70);
            justify-content: space-between;
        }

        .code a {
            color: rgb(74, 74, 81);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
        }

        .box-content {
            position: relative;
            width: 60%;
            background-color: white !important;
            margin: 7% auto;
            border-radius: 10px;
            height: auto;
        }

        .close {
            color: #aaa;
            float: left;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
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

        .row1 {
            display: flex;
            justify-content: space-between;
        }

        .code-alert {
            position: fixed;
            bottom: .7em;
            left: 5em;
            background-color: #6c757d;
            padding: 15px;
            border-radius: 10px;
            z-index: 9999;
            color: #fff;
            animation: slideInLeft 0.5s forwards;
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
                        <?php if ($row['archive'] == 0) : ?>
                            <a href="class.php?class_id=<?php echo $class_id; ?>">
                                <li class="li"> Classwork</li>
                            </a>
                            <a href="studentinfo.php?class_id=<?php echo $class_id; ?>">
                                <li class="li"> Students</li>
                            </a>
                            <li class="li">
                                <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v px-3"></i>
                                </a>
                                <ul class="dropdown-menu mx-5 py-0" style=" width: 6vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                    <li class="option"><a class="tools" href="ClassEdit.php?class_id=<?php echo $class_id; ?>"> Edit</a></li>
                                    <li class="option"><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?archive_id=<?php echo $row['course_id']; ?>&class_id=<?php echo $class_id; ?>">Archive</a></li>
                                    <li class="option"><a class=" delete tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['course_id']; ?>&class_id=<?php echo $class_id; ?>"> Delete </a></li>
                                </ul>
                            </li>
                        <?php else : ?>
                            <li class="li">
                                <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v px-3"></i>
                                </a>
                                <ul class="dropdown-menu mx-5 py-0" style=" width: 10vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                    <li><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?restore_id=<?php echo $row['course_id']; ?>&class_id=<?php echo $class_id; ?>">Restore</a></li>
                                    <li><a class=" delete tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?delete_id=<?php echo $row['course_id']; ?>&class_id=<?php echo $class_id; ?>"> Delete </a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <h1><?php echo $row['course_name']; ?></h1>
                <p><?php echo $row['description']; ?></p>
            </div>
            <?php if ($row['archive'] == 0) : ?>
                <section class="p-1">
                    <aside>
                        <div class="class_code">
                            <div class="code">
                                <div>
                                    <h6>class code</h6>
                                </div>
                                <div> <a href="#" id="dropdownMenuButton2" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v px-3"></i>
                                    </a>
                                    <ul class="dropdown-menu mx-5 py-0" style=" width: 35vh !important; padding: 10px !important;" aria-labelledby="dropdownMenuButton2">
                                        <li><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?reset_id=<?php echo $row['course_id']; ?>&class_id=<?php echo $class_id; ?>"><i class="fa-solid fa-arrow-rotate-left"></i>&nbsp; Reset Class Code</a></li>
                                        <li><a class="tools" href="#" onclick="copyClassCode('<?php echo $row['class_code']; ?>')"><i class="fa-regular fa-clone"></i>&nbsp; Copy Class Code</a></li>
                                        <li><a class="tools" href="#" onclick="copyLinkToClipboard()"> <i class="fa-solid fa-link"></i> &nbsp;Copy Class Link </a></li>
                                    </ul>
                                </div>
                            </div>
                            <h2 class="text" id="class_code"><?php echo $row['class_code']; ?></h2>
                        </div>
                        <div class="class_work" onclick="showBox2()">
                            <h5><i class="fa fa-plus" aria-hidden="true"></i> &nbsp;Add Assignment</h5>
                        </div>

                        <div class="class_work" onclick="window.location.href = 'TakeAttendance.php?class_id=<?php echo $class_id; ?>';">
                            <h5><i class="fa fa-bullhorn" aria-hidden="true"></i> &nbsp;Take Attendance</h5>
                        </div>
                    </aside>
                    <div class="class_box" id="box1">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo  $class_id  ?>" method="POST" enctype="multipart/form-data">
                            <div class="form-group mb-2">
                                <textarea class="form-control" id="classDescription" name="ann_text" rows="3" style="resize: none;" placeholder="Announce something to your class..."></textarea>
                            </div>
                            <div class="outer" id="selected_files_container"></div>
                            <div class="form-group my-2 ">
                                <label for="2" class="form-label">Upload Work</label>
                                <input type="file" id="work_file" name="work_file[]" style="display: none;" multiple>
                                <button type="button" class="btn_choose" onclick="document.getElementById('work_file').click();">Upload File</button>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn1" name="box1"><i class="bi bi-plus-lg"></i> &nbsp;Post</button>
                            </div>
                        </form>
                    </div>


                </section>
            <?php endif; ?>
            <section>
                <?php if ($assResult->num_rows === 0 && $annResult->num_rows === 0 && $attResult->num_rows === 0) : ?>
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
                                <!-- Display assignment -->
                                <div class="class-activity" onclick=" window.location.href = 'ViewAssignment.php?class_id=<?php echo $class_id; ?>&ass_id=<?php echo $post2_row['ass_id'] ?>';">
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

                                            <li><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo  $class_id; ?>&delete_ass_id=<?php echo $post2_row['ass_id']; ?>"> Delete </a></li>
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
                                                <li><a class="tools" href="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo  $class_id; ?>&delete_ann_id=<?php echo $post2_row['ann_id']; ?>"> Delete </a></li>
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
    <div class="modal" id="box2">
        <div class="box-content">
            <h2 class="h2"> <span class="close" onclick="hideBox2()"><i class="fa-solid fa-arrow-left-long"></i></span><i class="fa-regular fa-clipboard"></i>&nbsp; ASSIGNMENT </h2>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo $class_id ?>" method="POST" enctype="multipart/form-data" class="col-sm-10 mx-auto p-3">
                <div class="form-group mb-2">
                    <label for="name" class="file-upload">Assignment Title</label><br>
                    <input type="text" class="form-control" id="name" name="ass_name" placeholder="Enter title" required />
                </div>
                <div class="row1 mb-2">
                    <div class="form-group col-md-5 ">
                        <label for="due-date" class="file-upload">Due Date</label><br>
                        <input type="date" class="form-control" id="due-date" name="due_date" />
                    </div>
                    <div class="form-group col-md-6 ">
                        <label for="time" class="file-upload">Time</label><br>
                        <input type="time" class="form-control" id="time" name="time" />
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="marks" class="file-upload">Marks</label><br>
                    <input type="number" class="form-control" id="marks" name="marks" placeholder="Enter marks (optional)" />
                </div>
                <div class="form-group mb-2">
                    <textarea class="form-control" id="classDescription" name="ass_description" rows="3" style="resize: none;" placeholder="Description (optional)"></textarea>
                </div>
                <div class="outer" id="selected_files_container2"></div>
                <div class="form-group my-2 ">
                    <label for="work_file2" class="form-label">Upload Files</label>
                    <input type="file" id="work_file2" name="work_file[]" style="display: none;" multiple required>
                    <button type="button" class="btn_choose" onclick="document.getElementById('work_file2').click();">Choose File</button>
                </div>
                <div class="text-center mt-4">
                    <button class="btn1" type="submit" name="box2"><i class="bi bi-plus-lg"></i> &nbsp;Post</button>
                </div>
            </form>
        </div>
    </div>
   
    <div id="assignmentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="hideModal()"><i class="fa-solid fa-arrow-left-long"></i></span>
            <div class="dropdown-divider my-0 mx-1"></div>
            <h2 id="assignmentTitle" class="text-center h2"></h2>
            <div class="dropdown-divider my-0 mx-1"></div>
            <p class="p-2" id="assignmentDescription"></p>
            <div class="mx-auto" id="assignmentAttachment"></div>
        </div>
    </div>
    <script>
        function showBox2() {
            var modal = document.getElementById("box2");
            modal.style.display = "block";
        }

        function hideBox2() {
            var modal = document.getElementById("box2");
            modal.style.display = "none";
        }
        window.onclick = function(event) {
            var modal = document.getElementById("box2");
            if (event.target == modal) {
                hideBox2();
            }
        };

        function showBox3() {
            var modal = document.getElementById("box3");
            modal.style.display = "block";
        }


        function hideBox3() {
            var modal = document.getElementById("box3");
            modal.style.display = "none";
        }


        window.onclick = function(event) {
            var modal = document.getElementById("box3");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };


        setTimeout(function() {
            document.getElementsByClassName('alert-success')[0].remove();
        }, 2000);

        function showModal(title, description, attachment) {
            var modal = document.getElementById("assignmentModal");
            var titleElem = document.getElementById("assignmentTitle");
            var descriptionElem = document.getElementById("assignmentDescription");
            var attachmentElem = document.getElementById("assignmentAttachment");

            titleElem.innerText = title;
            descriptionElem.innerText = description;

            if (attachment !== "") {
                var attachmentHtml = "";
                if (attachment.includes(".pdf")) {
                    attachmentHtml = '<a href="' + attachment + '" target="_blank" class="btn1"><i class="fa-regular fa-file-pdf"></i>&nbsp;View Attachment</a>';
                } else {
                    attachmentHtml = '<a href="' + attachment + '" target="_blank"><img src="' + attachment + '" alt="Attachment" width="100" height="100"></a>';
                }
                attachmentElem.innerHTML = attachmentHtml;
            } else {
                attachmentElem.innerHTML = "";
            }

            modal.style.display = "block";
        }

        function hideModal() {
            var modal = document.getElementById("assignmentModal");
            modal.style.display = "none";
        }


        window.onclick = function(event) {
            var modal = document.getElementById("assignmentModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        //Copy link and class code
        function copyClassCode(code) {
            navigator.clipboard.writeText(code)
                .then(() => {
                    var alertDiv = document.createElement("div");
                    alertDiv.innerHTML = "Class code copied.";
                    alertDiv.classList.add("code-alert");
                    document.body.appendChild(alertDiv);
                    setTimeout(function() {
                        alertDiv.remove();
                    }, 2000);
                })

                .catch(err => {
                    var alertDiv = document.createElement("div");
                    alertDiv.innerHTML = "Couldn't copy the code. ";
                    alertDiv.classList.add("code-alert");
                    document.body.appendChild(alertDiv);
                    setTimeout(function() {
                        alertDiv.remove();
                    }, 2000);
                });

        }

        function copyLinkToClipboard() {
            var class_code = document.getElementById("class_code").innerText;
            var joinClassLink = "http://localhost/digitalClassroom/Student/JoinClass.php?linkcode=" + class_code;
            navigator.clipboard.writeText(joinClassLink)
                .then(() => {
                    var alertDiv = document.createElement("div");
                    alertDiv.innerHTML = "Link copied";
                    alertDiv.classList.add("code-alert");
                    document.body.appendChild(alertDiv);
                    setTimeout(function() {
                        alertDiv.remove();
                    }, 2000);
                })
                .catch(err => {
                    var alertDiv = document.createElement("div");
                    alertDiv.innerHTML = "Couldn't copy the link. ";
                    alertDiv.classList.add("code-alert");
                    document.body.appendChild(alertDiv);
                    setTimeout(function() {
                        alertDiv.remove();
                    }, 2000);
                });
        }
        //checkbox
        
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

        document.getElementById('work_file').addEventListener('change', handleFileSelect1);

        function handleFileSelect1(event) {
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

        document.getElementById('work_file2').addEventListener('change', handleFileSelect2);

        function handleFileSelect2(event) {
            const files = event.target.files;
            const selectedFilesContainer = document.getElementById('selected_files_container2');


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
        //current time
        function setCurrentTime() {
            var now = new Date();
            var hours = now.getHours();
            var minutes = now.getMinutes();
            var timeString = ('0' + hours).slice(-2) + ':' + ('0' + minutes).slice(-2);
            document.getElementById('atttime').value = timeString;
        }
        window.addEventListener('load', setCurrentTime);
    </script>
</body>

</html>