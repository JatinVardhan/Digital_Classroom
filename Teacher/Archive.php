<?php
session_start();
if(strlen($_SESSION['login'])!=1)
    {   
        echo "<script> window.location.href = '../Home/Login.php'; </script>";
}
include "navbar.php";
include '../Home/Connection.php';
$id = $_SESSION['id'];
$row = $errorMsg = $succMsg = "";

$classQuery = "SELECT * FROM classes WHERE teacher_id = $id AND archive = 1";
$classResult = $conn->query($classQuery);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css?<?php echo time(); ?>">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
  <Style>
    .container {
      background-color: rgb(232, 237, 216) !important;
    }

    .dlt {
      background-color: rgb(228, 50, 50);
    }

    .btn1 {
      height: 30px;
    }

    .dlt:focus {
      box-shadow: rgb(179, 32, 32) 0 0 0 1.5px inset, rgba(45, 35, 66, 0.4) 0 2px 4px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(179, 32, 32) 0 -3px 0 inset;
    }

    .dlt:hover {
      box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px, rgb(179, 32, 32) 0 -3px 0 inset;
      transform: translateY(-2px);
    }

    .dlt:active {
      box-shadow: rgb(179, 32, 32) 0 3px 7px inset;
      transform: translateY(2px);
    }

    .class-container {
      position: relative;
      display: grid;
      grid-template-columns: 44% 45% !important;
      gap: 30px;
      /* background-color: antiquewhite; */
      padding: 2% 0;
      justify-content: center;
      align-items: center;
    }

    .class-card {
      box-shadow: 3px 3px 5px lightgray;
      padding: 10px;
      border: none;
      border-radius: 10px;
      transition: ease-in-out .3s;
    }

    .class-container p {
      padding: 0 10px;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis !important;
    }

    .class-card:hover {
      box-shadow: rgba(45, 35, 66, 0.4) 0 4px 8px,
        rgba(45, 35, 66, 0.3) 0 7px 13px -3px !important;
      transform: translateY(-2px) !important;
    }

    .class-card h2 {
      margin-bottom: 5px;
      text-align: center;
      color: white !important;
    }

    .class-card p {
      margin-bottom: 10px;
      color: white;
    }
  </Style>
  <title>Archive Classes</title>
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
    <div class="container col-10 py-2">
      <h2 class="h2">Archive Classes</h2>

      <div class="class-container col-sm-11">
        <?php if ($classResult->num_rows > 0) : ?>
          <?php while ($row = $classResult->fetch_assoc()) : ?>
            <a href="class.php?class_id=<?php echo $row['course_id']; ?>">
              <div class="class-card" style=" background-color: <?php echo $row['background']; ?>;">
                <h2><?php echo $row['course_name']; ?> </h2>
                <!-- <a href="#" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-ellipsis-v"></i> 
          </a>
          <ul class="dropdown-menu mx-5 py-0" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item py-2" href="ProfileEdit.php"> Edit</a></li>
            <li><a class="dropdown-item py-2" href="ManagePassword.php">Archive</a></li>
            <li><a class="dropdown-item py-2" href="DeleteProfile.php"> Delete </a></li>
          </ul> -->

                <p><?php echo $row['description']; ?></p>

              </div>
            </a>
          <?php endwhile; ?>
        <?php else : ?>
          <p>No classes created !</p>
        <?php endif; ?>
      </div>
      <div class="my-4 text-center">
        <button type="button" class="btn1 dlt" onclick="goBack()" style="height: 40px;"><i class="fa-solid fa-arrow-left"></i>&nbsp; Back</button>
      </div>
    </div>
  </main>
</body>


</html>