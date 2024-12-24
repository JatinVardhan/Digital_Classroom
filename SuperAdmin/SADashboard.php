<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include "navbar.php";
include '../Home/Connection.php';
$id = $_SESSION['a_dpt_id'];
$classsql = "SELECT COUNT(*) FROM classes WHERE dpt_id=$id AND archive=0";
$classstmt = $conn->prepare($classsql);
$classstmt->execute();
$classresult = $classstmt->get_result();
$result1 = $classresult->fetch_assoc();

$techersql = "SELECT COUNT(*) FROM teachers WHERE dpt_id=$id AND role=2";
$techerstmt = $conn->prepare($techersql);
$techerstmt->execute();
$techerresult = $techerstmt->get_result();
$result2 = $techerresult->fetch_assoc();

$classsql = "SELECT COUNT(*) FROM classes WHERE dpt_id=$id AND archive=1";
$classstmt = $conn->prepare($classsql);
$classstmt->execute();
$classresult = $classstmt->get_result();
$result3 = $classresult->fetch_assoc();

$classsql = "SELECT COUNT(DISTINCT teacher_id) FROM classes WHERE dpt_id=$id AND archive=0";
$classstmt = $conn->prepare($classsql);
$classstmt->execute();
$classresult = $classstmt->get_result();
$result4 = $classresult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <Style>
    .card {
      width: 310px;
      height: 140px;
      padding: .5em;
      box-shadow: rgba(0, 0, 0, .3) 2px 8px 8px;
    }

    .container {
      background-color: rgb(232, 237, 216) !important;
    }

    .row {
      display: grid;
      grid-template-columns: auto auto;
    }

    h4 {
      text-align: none !important;
      font-family: "Roboto", sans-serif !important;
    }
  </Style>
  <title>Super Admin Dashboard</title>
</head>

<body>
  <main>
    <div class="container col-9 py-2">
      <h2 class="h2"> Welcome Mr. <?php echo $_SESSION['a_name']; ?> </h2>
      <div class="row mx-auto">
        <div class="col-sm-4 my-3">
          <div class="card" style="background-color: #e7df3fe7;">
            <div class="card-body text-white text-center">
              <h4 class="card-text ">Total Active Classes </h4>
              <h1 class="card-title display-5"><b><?php echo $result1['COUNT(*)']; ?></b></h1>
            </div>
          </div>
        </div>
        <div class="col-sm-4 my-3">
          <div class="card" style="background-color:#3fe7d3e7;">
            <div class="card-body text-white text-center ">
              <h4 class="card-text">Total Teachers </h4>
              <h1 class="card-title display-5"><b><?php echo $result2['COUNT(*)']; ?></b></h1>
            </div>
          </div>
        </div>
        <div class="col-sm-4 my-3">
          <div class="card" style="background-color:#c53fe7e7;">
            <div class="card-body text-white text-center">
              <h4 class="card-text ">Total Inactive Classes </h4>
              <h1 class="card-title display-5"><b><?php echo $result3['COUNT(*)']; ?></b></h1>
            </div>
          </div>
        </div>
        <div class="col-sm-4 my-3">
          <div class="card" style="background-color:#5be73fe7;">
            <div class="card-body text-white text-center ">
              <h4 class="card-text">Total Active Teachers </h4>
              <h1 class="card-title display-5"><b><?php echo $result4['COUNT(DISTINCT teacher_id)']; ?></b></h1>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</body>

</html>