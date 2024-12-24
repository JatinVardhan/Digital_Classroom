<?php
session_start();
include '../Home/Connection.php';
include "navbar.php";

if (strlen($_SESSION['login']) != 1) {
  echo "<script> window.location.href = '../Home/Login.php'; </script>";
}

$errorMsg = $succMsg = $edit_id = $ass_id = "";
$assignmentDetails = [];
$assignmentMedia = [];
$attendanceDetails = []; // To store the details from upload_work table

if (isset($_GET['class_id'])) {
  $edit_id = $conn->real_escape_string($_GET['class_id']);
} else {
  $errorMsg = "No class ID provided...";
}

if (isset($_GET['ass_id'])) {
  $ass_id = $conn->real_escape_string($_GET['ass_id']);

  // Fetch assignment details
  $sql = "SELECT * FROM assignmnet WHERE ass_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $ass_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    $assignmentDetails = $result->fetch_assoc();
    $dateTime = new DateTime($assignmentDetails['upload_date']);
    $formattedDateTime = $dateTime->format('d M Y g:i A');
    // Decode JSON string to get media files
    $assignmentMedia = json_decode($assignmentDetails['ass_media'], true);
  } else {
    $errorMsg = "No assignment found with the provided ID.";
  }
} else {
  $errorMsg = "No assignment ID provided.";
}

// Fetch upload work details
if (!empty($ass_id)) {
  $sql = "SELECT upload_work.*, students.student_rollno, students.student_name 
            FROM upload_work 
            JOIN students ON upload_work.std_id = students.student_rollno
            WHERE upload_work.ass_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $ass_id);
  $stmt->execute();
  $result = $stmt->get_result();
  while ($row = $result->fetch_assoc()) {
    $row['formatted_upload_date'] = (new DateTime($row['upload_date']))->format('m/d/Y g:i A');
    $attendanceDetails[] = $row;
  }
}

// Check if 'marks_given' array is set
if (isset($_POST['marks_given']) && is_array($_POST['marks_given'])) {
  $validMarks = true;
  foreach ($_POST['marks_given'] as $student_rollno => $marks_given) {
    if (!is_numeric($marks_given) || $marks_given < 0) {
      $validMarks = false;
      break;
    }
  }

  if ($validMarks) {
    // All marks are valid, proceed with updating the database
    foreach ($_POST['marks_given'] as $student_rollno => $marks_given) {
      $sql = "UPDATE upload_work SET marks_given = ? WHERE ass_id = ? AND std_id = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("iii", $marks_given, $ass_id, $student_rollno);
      $stmt->execute();
    }
    $succMsg = "Marks updated successfully.";
    echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id=$edit_id&ass_id=$ass_id'; }, 2000);</script>";

  } else {
    $errorMsg = "No marks updated. Please ensure all marks are valid numbers greater than or equal to 0.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style1.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <title>View Assignment</title>
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

        .modal-box {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
        }


        body.modal-open {
            filter: blur(5px);
            -webkit-filter: blur(5px);
            pointer-events: none;
        }

        body.modal-open main {
            overflow: hidden;
        }

    .modal-content {
      background-color: #fefefe;
      margin: 8% auto;
      width: 65%;
      border-radius: 10px;
      position: relative;
    }
    .close1 {
      color: var(--first-color-light);
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 10px;
    }

    .close1:hover,
    .close1:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
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
      font-size: small;
    }

    .h2 {
      text-align: left !important;
      background: none !important;
    }

    input[type="number"],
    input[type="text"] {
      width: 100px;
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

    <div class="modal-content">
      <div class="ass_head">
        <div style="width: 5%!important;margin:15px 0;">
          <span class="close" onclick="window.location.href = 'class.php?class_id=<?php echo $edit_id; ?>';"><i class="fa-solid fa-arrow-left-long"></i></span>
        </div>
        <div style="width:95%">
          <h2 class="h2"><?php echo htmlspecialchars($assignmentDetails['ass_name']); ?></h2>
          <p class="mark_due"><?php echo $formattedDateTime; ?></p>
          <p class="marks-due">
            <?php if (!empty($assignmentDetails['marks'])) : ?>
              <span id="assignmentMarks">Marks: <?php echo htmlspecialchars($assignmentDetails['marks']); ?></span>
            <?php endif; ?>
            <?php if (!empty($assignmentDetails['due_date'])) : ?>
              <span id="assignmentDue">Due: <?php echo htmlspecialchars($assignmentDetails['due_date']); ?></span>
            <?php endif; ?>
          </p>
        </div>
      </div>
      <div class="ass_body">
        <p class="p-2" id="assignmentDescription"><?php echo nl2br(htmlspecialchars($assignmentDetails['ass_description'])); ?></p>
        <div class="attachment" id="assignmentAttachment">
          <?php if (!empty($assignmentMedia)) : ?>
            <div class="attachment mx-auto my-3">
              <?php foreach ($assignmentMedia as $media_file) : ?>
                <?php
                $file_name = basename($media_file);
                $file_name_without_unique = preg_replace('/_[^_]+(\.[a-zA-Z0-9]+)$/', '$1', $file_name);
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
        <div class="dropdown-divider m-3"></div>
        <?php if (!empty($attendanceDetails)) : ?>
          <form method="post">
            <table class="table mb-4">
              <thead>
                <tr>
                  <th>Roll Number</th>
                  <th>Name</th>
                  <th>Marks</th>
                  <th>Uploaded Work</th>
                  <th>Uploaded On</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($attendanceDetails as $detail) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($detail['student_rollno']); ?></td>
                    <td><?php echo htmlspecialchars($detail['student_name']); ?></td>
                    <td class="editable">
                      <?php if ($detail['marks_given'] < 0) : ?>
                        <input type="text" name="marks_given[<?php echo $detail['student_rollno']; ?>]" value="Unmarked">
                      <?php else : ?>
                        <input type="number" name="marks_given[<?php echo $detail['student_rollno']; ?>]" value="<?php echo $detail['marks_given']; ?>">
                      <?php endif; ?>
                    </td>
                    <td><a href="#" onclick="showModal('<?php echo htmlspecialchars($detail['ass_file'], ENT_QUOTES, 'UTF-8'); ?>');" class="btn1">View Work</a></td>
                    <td><?php echo htmlspecialchars($detail['formatted_upload_date']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="text-center mt-4">
              <!-- Update button -->
              <button type="submit" class="btn1" name="update"><i class="bi bi-arrow-repeat"></i>&nbsp; Update Marks</button>
            </div>
          </form>
        <?php else : ?>
          <p>No student uploads found for this assignment.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>
  <div id="assignmentModal" class="modal">
        <div class="modal-box">
        <div style="width: 5%!important;">
          <span class="close1" onclick="hideModel();"><i class="fa-solid fa-arrow-left-long"></i></span>
        </div>
        <div id="attachment" class="attachment"></div>


        </div>
    </div>
</body>
<script>
function showModal(assignment) {
    var modal = document.getElementById("assignmentModal");
    var attachmentElem = document.getElementById("attachment");

  console.log(assignment)
    if (typeof assignment === "string" && assignment.trim() !== "") {
        try {
           
            var attachments = JSON.parse(assignment);
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
        } catch (error) {
            console.error("Error parsing assignment JSON:", error);
            attachmentElem.innerHTML = "Error parsing assignment JSON.";
        }
    } else {
        
        attachmentElem.innerHTML = "Invalid assignment data.";
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
</script>


</html>