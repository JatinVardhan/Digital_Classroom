<?php
$checkQuery = "SELECT * FROM students WHERE (phone_number = ? OR student_email = ?) ";
  $checkStmt = $conn->prepare($checkQuery);
  $checkStmt->bind_param("ss", $phone_no, $email);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();