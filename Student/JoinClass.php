<?php
session_start();
include '../Home/Connection.php';
include "navbar.php";
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
$errorMsg = $linkcode = "";
if (isset($_GET['linkcode'])){
    $linkcode = $conn->real_escape_string($_GET['linkcode']);
}
if (isset($_POST['join'])) {
    if (empty($_POST['classCode'])) {
        $errorMsg = "Class code is required.";
    } else {
        $classCode = $conn->real_escape_string($_POST['classCode']);

        // Check if the class exists
        $checkClassQuery = "SELECT * FROM classes WHERE class_code = ?";
        $checkClassStmt = $conn->prepare($checkClassQuery);
        $checkClassStmt->bind_param("s", $classCode);
        $checkClassStmt->execute();
        $checkClassResult = $checkClassStmt->get_result();

        if ($checkClassResult->num_rows == 0) {
            $errorMsg = "No class found. Please check your class code.";
        } else {
            // Get the class ID
            $classData = $checkClassResult->fetch_assoc();
            $classId = $classData['course_id'];
            $studentId = $_SESSION['s_id'];

            // Check if the student has already joined the class
            $checkJoinQuery = "SELECT * FROM join_class WHERE class_id = ? AND std_id = ?";
            $checkJoinStmt = $conn->prepare($checkJoinQuery);
            $checkJoinStmt->bind_param("ii", $classId, $studentId);
            $checkJoinStmt->execute();
            $checkJoinResult = $checkJoinStmt->get_result();

            if ($checkJoinResult->num_rows > 0) {
                $errorMsg = "You have already joined this class.";
            } else {
                // Insert into join_class table
                $insertJoinQuery = "INSERT INTO join_class (class_id, std_id) VALUES (?, ?)";
                $insertJoinStmt = $conn->prepare($insertJoinQuery);
                $insertJoinStmt->bind_param("ii", $classId, $studentId);

                if ($insertJoinStmt->execute()) {
                    $succMsg = "Class joined successfully.";
                    echo "<script>setTimeout(function(){ window.location.href = 'class.php?class_id=$classId'; }, 2000);</script>";
                } else {
                    $errorMsg = "Error occurred while joining the class: " . $insertJoinStmt->error;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
    <title>Join Class</title>
    <style>
         .dlt {
      background-color: rgb(228, 50, 50) !important;
    }

    .dlt:focus {
      box-shadow: rgb(216, 40, 40) 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(216, 40, 40) 0 -3px 0 inset !important;
    }

    .dlt:hover {
      box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(216, 40, 40) 0 -3px 0 inset !important;
      transform: translateY(-2px) !important;
    }

    .dlt:active { 
      box-shadow: rgb(216, 40, 40) 0 3px 7px inset !important; 
      transform: translateY(2px) !important;
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
        <div class="container col-6">
            <form class="pt-4 col-sm-7" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <h2 class="h2">Join Class</h2>
                <div class="form-group mb-2">
                    <label for="classCode">Class Code</label>
                    <input type="text" class="form-control" id="classCode" name="classCode" placeholder="Enter class code" value="<?php echo $linkcode?>" />
                </div>
                <div class="text-center my-5">
                    <button type="submit" class="btn1" name="join"><i class="fa-solid fa-plus"></i> &nbsp;Join</button>
                    &emsp;&emsp;&emsp;
                    <button type="button" class="btn1 dlt" onclick="goBack()"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
                </div>
            </form>
        </div>
    </main>
</body>

</html>