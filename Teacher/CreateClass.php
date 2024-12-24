<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";
// Function to generate random code
function generateUniqueRandomCode($length = 7)
{
    global $conn;

    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

    do {
        $randomCode = '';
        for ($i = 0; $i < $length; $i++) {
            $randomCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        $checkQuery = "SELECT * FROM classes WHERE class_code = '$randomCode'";
        $checkResult = $conn->query($checkQuery);
    } while ($checkResult->num_rows > 0);

    return $randomCode;
}
$backgroundColors = array(
    "#679436", 
    "#A5BE00", 
    "#05668D", 
    "#427AA1", 
    "#2ecc71", 
    "#f1c40f", 
    "#e74c3c"  
);

shuffle($backgroundColors);

$background = $backgroundColors[0];

// Fetch departments
$deptQuery = "SELECT * FROM departments";
$deptResult = $conn->query($deptQuery);

$errorMsg = $succMsg = "";
$nameErr = $departmentErr = $semesterErr = $dscErr = "";
$isValid = true;
$dpt = $_SESSION['dpt_id'];
$teacher_id = $_SESSION['id'];

if (isset($_POST['create'])) {
    if (empty($_POST['className'])) {
        $nameErr = "Class name is required.";
        $isValid = false;
    } elseif (strlen($_POST['className']) > 40) {
        $nameErr = "Class name is too long.";
        $isValid = false;
    } else {
        $className = $conn->real_escape_string($_POST['className']);
        if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $className)) {
            $nameErr = "Only letters, spaces, and dashes are allowed.";
            $isValid = false;
        }
    }
    if (empty($_POST['department']) || $_POST['department'] == -1) {
        $departmentErr = "Please select your department.";
        $isValid = false;
    } else {
        $department = $conn->real_escape_string($_POST['department']);
    }

    if (empty($_POST['semester']) || !in_array($_POST['semester'], [1, 2, 3,4,5,6,7,8])) {
        $semesterErr = "Please select a valid semester.";
        $isValid = false;
    } else {
        $semester = $conn->real_escape_string($_POST['semester']);
    }

    if (empty($_POST['classDescription'])) {
        $dscErr = "Class description is required.";
        $isValid = false;
    } elseif (!preg_match("/^[a-zA-Z0-9\s\W]*$/", $_POST['classDescription'])) {
        $dscErr = "Only letters, numbers, symbols, and spaces are allowed in the class description.";
        $isValid = false;
    } elseif (strlen($_POST['classDescription']) > 150) {
        $dscErr = "Class description is too long.";
        $isValid = false;
    } else {
        $classDescription = $conn->real_escape_string($_POST['classDescription']);
    }
    $classCode = $conn->real_escape_string($_POST['classCode']);
    $checkClassQuery = "SELECT * FROM classes WHERE class_code = ? ";
    $checkClassStmt = $conn->prepare($checkClassQuery);
    $checkClassStmt->bind_param("s", $classCode);
    $checkClassStmt->execute();
    $checkClassResult = $checkClassStmt->get_result();

    if ($checkClassResult->num_rows > 0) {
        $errorMsg = "Class is already exists.";
        $isValid = false;
    }
    if ($isValid) {
        $classCode = $conn->real_escape_string($_POST['classCode']);
        $insertQuery = "INSERT INTO classes (course_name, dpt_id, semester, teacher_id, class_code, description, background) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertQuery);
        $insertStmt->bind_param("siiisss", $className, $department, $semester, $teacher_id, $classCode, $classDescription, $background);

        if ($insertStmt->execute()) {
            $succMsg = "Class created successfully.";
            echo "<script>setTimeout(function(){ window.location.href = 'ADashboard.php'; }, 2000);</script>";
        } else {
            $errorMsg = "Error occurred while creating the class: " . $insertStmt->error;
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
    <title>Create Class</title>
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
                <h2 class="h2">Create Class</h2>
                <div class="form-group mb-2">
                    <label for="className">Class Name</label>
                    <input type="text" class="form-control" id="className" name="className" placeholder="Enter class name" />
                    <div class="error" id="classNameErr"></div>
                    <div class="error"><?php echo $nameErr; ?></div>
                </div>
                <div class="form-group">
                    <label for="Department">Department</label><br>
                    <select name="department" class="form-control" id="department-select" name="department">
                        <option value="-1" disabled>--Select your department--</option>
                        <?php
                        if ($deptResult->num_rows > 0) {
                            while ($row = $deptResult->fetch_assoc()) {
                                $selected = ($row["dpt_id"] == $dpt) ? "selected" : "";
                                echo "<option value='" . $row["dpt_id"] . "'>" . htmlspecialchars($row["dpt_name"]) . "</option>";
                            }
                        } else {
                            echo "<option value=''>No departments available</option>";
                        }
                        ?>
                    </select>
                    <div class="error"><?php echo $departmentErr; ?></div>
                </div>
                <div class="form-group mb-2">
                    <label for="semester">Semester</label>
                    <select class="form-select" id="semester" name="semester">
                        <option selected disabled>-- Select semester --</option>
                        <option value="1">Semester 1</option>
                        <option value="2">Semester 2</option>
                        <option value="3">Semester 3</option>
                        <option value="4">Semester 4</option>
                        <option value="5">Semester 5</option>
                        <option value="6">Semester 6</option>
                        <option value="7">Semester 7</option>
                        <option value="8">Semester 8</option>
                    </select>
                    <div class="error"><?php echo $semesterErr; ?></div>
                </div>
                <div class="form-group mb-2">
                    <label for="classCode">Class Code</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="classCode" name="classCode" placeholder="Enter class code" value="<?php echo generateUniqueRandomCode(); ?>" readonly />

                    </div>

                </div>
                <div class="form-group mb-2">
                    <label for="classDescription">Class Description</label>
                    <textarea class="form-control" id="classDescription" name="classDescription" rows="3" placeholder="Enter class description"></textarea>
                    <div class="error"><?php echo $dscErr; ?></div>
                </div>
                <div class="text-center my-5">
                    <button type="submit" class="btn1" name="create"><i class="fa-solid fa-plus"></i> &nbsp;Create</button>
                    &emsp;&emsp;&emsp;
                    <button type="button" class="btn1 dlt" onclick="goBack()"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
                </div>
            </form>
        </div>
        <br><br>
    </main>
</body>

</html>