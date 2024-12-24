<?php
session_unset();
session_destroy();
header("Location: ../Home/Login.php"); 
exit(); 
?>