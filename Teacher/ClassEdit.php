<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include '../Home/Connection.php';
include "navbar.php";
// Fetch departments
$deptQuery = "SELECT * FROM departments";
$deptResult = $conn->query($deptQuery);

$row1 = $errorMsg = $succMsg = "";
$nameErr = $departmentErr = $semesterErr = $dscErr = "";
$isValid = true;
if (isset($_GET['class_id'])) {
    $class_id = $conn->real_escape_string($_GET['class_id']);
    $sql = "SELECT * FROM classes WHERE course_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row1 = $result->fetch_assoc();
    } else {
        $errorMsg = "No record found...";
    }
} else {
    $errorMsg = "No id provided...";
}


if (isset($_POST['update'])) {
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

    if (empty($_POST['semester']) || !in_array($_POST['semester'], [1, 2, 3])) {
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
    if ($isValid) {
        $class_id = $_GET['class_id'];
        $updateQuery = "UPDATE classes SET course_name=?, dpt_id=?, semester=?, description=? WHERE course_id=?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("siisi", $className, $department, $semester, $classDescription, $class_id);
        $updateStmt->execute();
        if ($updateStmt->affected_rows > 0) {
            $succMsg = "Information updated successfully.";
            echo "<script>setTimeout(function(){ window.location.href = 'class.php?class_id=$class_id'; }, 2000);</script>";
        } else {
            if ($updateStmt->error) {
                $errorMsg = "Error updating record: " . $updateStmt->error;
            } else {
                $errorMsg = "No changes made to the record...";
            }
        }
        $updateStmt->close();
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
            <script>

            </script>
        <?php endif; ?>
        <div class="container col-6">
            <form class="pt-4 col-sm-7" action="<?php echo $_SERVER['PHP_SELF']; ?>?class_id=<?php echo $class_id; ?>" method="POST">
                <h2 class="h2">Create Class</h2>
                <div class="form-group mb-2">
                    <label for="className">Class Name</label>
                    <input type="text" class="form-control" id="className" name="className" value="<?php echo $row1['course_name']; ?>" placeholder="Enter class name" />
                    <div class="error"><?php echo $nameErr; ?></div>
                </div>
                <div class="form-group">
                    <label for="Department">Department</label><br>
                    <select name="department" class="form-control" id="department-select" name="department">
                        <option value="-1" disabled>--Select your department--</option>
                        <?php
                        if ($deptResult->num_rows > 0) {
                            while ($row = $deptResult->fetch_assoc()) {
                                $selected = ($row1["dpt_id"] == $dpt) ? "selected" : "";
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
                        <option disabled>-- Select semester --</option>
                        <option value="1" <?php if ($row1['semester'] == 1) echo "selected"; ?>>Semester 1</option>
                        <option value="2" <?php if ($row1['semester'] == 2) echo "selected"; ?>>Semester 2</option>
                        <option value="3" <?php if ($row1['semester'] == 3) echo "selected"; ?>>Semester 3</option>
                        <option value="4" <?php if ($row1['semester'] == 4) echo "selected"; ?>>Semester 4</option>
                        <option value="5" <?php if ($row1['semester'] == 5) echo "selected"; ?>>Semester 5</option>
                        <option value="6" <?php if ($row1['semester'] == 6) echo "selected"; ?>>Semester 6</option>
                        <option value="7" <?php if ($row1['semester'] == 7) echo "selected"; ?>>Semester 7</option>
                        <option value="8" <?php if ($row1['semester'] == 8) echo "selected"; ?>>Semester 8</option>
                        <!-- Add more semesters as needed -->
                    </select>
                    <div class="error"><?php echo $semesterErr; ?></div>
                </div>
                <div class="form-group mb-2">
                    <label for="classDescription">Class Description</label>
                    <textarea class="form-control" id="classDescription" name="classDescription" rows="3" placeholder="Enter class description"><?php echo $row1['description']; ?></textarea>
                    <div class="error"><?php echo $dscErr; ?></div>
                </div>
                <div class="text-center my-5">
                    <button type="submit" class="btn1" name="update"><i class="bi bi-arrow-repeat pr"> </i>&nbsp; Update</button>
                    &emsp;&emsp;&emsp;
                    <button type="button" class="btn1 dlt" onclick=" window.location.href = 'class.php?class_id=<?php echo $class_id; ?>'"><i class="bi bi-x-lg pr"></i>&nbsp; Cancel</button>
                </div>
            </form>
        </div>
        <br><br>
    </main>
</body>
<script>
    setTimeout(function() {
        document.getElementsByClassName('alert-danger')[0].remove();
    }, 2000);

    setTimeout(function() {
        document.getElementsByClassName('alert-warning')[0].remove();
    }, 5000);
</script>

</html>