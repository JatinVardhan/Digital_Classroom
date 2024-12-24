<?php
// Deleting a class if requested
if (isset($_GET['delete_id'])) {
    $deleteid = $conn->real_escape_string($_GET['delete_id']);
    $checkEnrollmentQuery = "SELECT * FROM join_class WHERE class_id=?";
    $checkEnrollmentStmt = $conn->prepare($checkEnrollmentQuery);
    $checkEnrollmentStmt->bind_param("i", $deleteid);
    $checkEnrollmentStmt->execute();
    $enrollmentResult = $checkEnrollmentStmt->get_result();

    if ($enrollmentResult->num_rows > 0) {
        $errorMsg = "Sorry, you cannot delete this class because students are enrolled in it.";
    } else {
        $dltsql = "DELETE FROM classes WHERE course_id=?";
        $dltstmt = $conn->prepare($dltsql);
        $dltstmt->bind_param("i", $deleteid);
        if ($dltstmt->execute()) {
            if ($row['archive'] == 0) {
                echo "<script>  window.location.href = 'ADashboard.php'; </script>";
            } else {
                echo "<script>  window.location.href = 'Archive.php'; </script>";
            }
        } else {
            $errorMsg = "Error: " . $dltstmt->error;
        }
    }
}

// Archiving a class
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

// Restoring an archived class
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

// Resetting class code
if (isset($_GET['reset_id'])) {
    $reset_id = $conn->real_escape_string($_GET['reset_id']);
    $new_code = generateUniqueRandomCode($conn);
    $updateQuery = "UPDATE classes SET class_code = ? WHERE course_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("si", $new_code, $reset_id);

    if ($updateStmt->execute()) {
        $succMsg = "Class code reset successfully. ";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id= $class_id  '; }, 2000);</script>";
    } else {

        $errorMsg = "Error updating class code: " . $updateStmt->error;
    }
}
if (isset($_GET['delete_ass_id'])) {
    $delete_ass_id = $conn->real_escape_string($_GET['delete_ass_id']);
    
    // Begin a transaction
    $conn->begin_transaction();
    try {
        // Delete from upload_work
        $delete_upload_sql = "DELETE FROM upload_work WHERE ass_id=?";
        $delete_upload_stmt = $conn->prepare($delete_upload_sql);
        $delete_upload_stmt->bind_param("i", $delete_ass_id);
        $delete_upload_stmt->execute();

        // Delete from assignment
        $delete_ass_sql = "DELETE FROM assignmnet WHERE ass_id=?";
        $delete_ass_stmt = $conn->prepare($delete_ass_sql);
        $delete_ass_stmt->bind_param("i", $delete_ass_id);
        $delete_ass_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $succMsg = "Assignment deleted successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id=$class_id'; }, 2000);</script>";
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $errorMsg = "Error: " . $e->getMessage();
    }
}

if (isset($_GET['delete_ann_id'])) {
    $delete_ann_id = $conn->real_escape_string($_GET['delete_ann_id']);
    
    // Begin a transaction
    $conn->begin_transaction();
    try {
        // Delete from announcements
        $delete_ann_sql = "DELETE FROM announcement WHERE ann_id=?";
        $delete_ann_stmt = $conn->prepare($delete_ann_sql);
        $delete_ann_stmt->bind_param("i", $delete_ann_id);
        $delete_ann_stmt->execute();
        
        // Commit transaction
        $conn->commit();
        
        $succMsg = "Announcement deleted successfully.";
        echo "<script>setTimeout(function(){ window.location.href = '{$_SERVER['PHP_SELF']}?class_id=$class_id'; }, 2000);</script>";
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        $errorMsg = "Error: " . $e->getMessage();
    }
}






?>