<?php
$st_row = "";
$students = array();
// Function to generate unique random code
function generateUniqueRandomCode()
{
    global $conn;

    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';

    do {
        $randomCode = '';
        for ($i = 0; $i < 7; $i++) {
            $randomCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        $checkQuery = "SELECT * FROM classes WHERE class_code = '$randomCode'";
        $checkResult = $conn->query($checkQuery);
    } while ($checkResult->num_rows > 0);

    return $randomCode;
}
// Fetching enrolled class students
$sql = "SELECT students.* FROM students 
LEFT JOIN join_class ON students.student_rollno = join_class.std_id WHERE join_class.class_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$r1 = $stmt->get_result();

while ($st_row = $r1->fetch_assoc()) {
    $students[] = $st_row;
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

// Count the total number of attendance records for the class
$countQuery = "SELECT COUNT(*) AS total_attendance FROM attendance WHERE class_id=?";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("i", $class_id);
$countStmt->execute();
$countResult = $countStmt->get_result();

// Fetch the total number of attendance records
if ($countRow = $countResult->fetch_assoc()) {
    $totalAttendance = $countRow['total_attendance'];
    $updateQuery = "UPDATE classes SET total_taken_classes=? WHERE course_id=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ii", $totalAttendance, $class_id);
    $updateStmt->execute();
}
 //Calculate the attendance percentage

$att_Percentage = array();
foreach ($students as $student) {
    $studentRollNo = $student['student_rollno'];

    $percnt_Query = "SELECT COUNT(*) AS present_count FROM attendance_details WHERE std_id=? AND att_id IN (SELECT att_id FROM attendance WHERE class_id=?) AND attendance=1";
    $percnt_Stmt = $conn->prepare($percnt_Query);
    $percnt_Stmt->bind_param("ii", $studentRollNo, $class_id);
    $percnt_Stmt->execute();
    $percnt_Result = $percnt_Stmt->get_result();
    if($totalAttendance > 0){
    if ($percnt_Row = $percnt_Result->fetch_assoc() ) {
       $presentCount= $percnt_Row['present_count'];
        $att_Percentage[$studentRollNo] = ($presentCount / $totalAttendance ) * 100;

        $updateTotalAttendanceQuery = "UPDATE join_class SET total_attendance=? WHERE class_id=? AND std_id=?";
        $updateTotalAttendanceStmt = $conn->prepare($updateTotalAttendanceQuery);
        $updateTotalAttendanceStmt->bind_param("iii", $presentCount, $class_id, $studentRollNo);
        $updateTotalAttendanceStmt->execute();
    }

}
}
foreach ($att_Percentage as $rollNo => $percentage) {
    // Update the att_percentage field in the join_class table for each student
    $updateQuery = "UPDATE join_class SET att_percentage=?  WHERE class_id=? AND std_id=?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("dii", $percentage, $class_id, $rollNo);
    $updateStmt->execute();
}
 
?>