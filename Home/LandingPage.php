<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/x-icon" href="../media/images/icon1.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <title>DIGITAL CLASSROOM</title>
  
  <style>
    body {
      background-color: rgb(197, 231, 236);
      font-family: 'Roboto', sans-serif !important;
    }

    .container {
      background-image: url(../media/images/home_bg.png);
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
      width: 500px;
      height: 500px;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      border-radius: 5px;
      box-shadow: rgba(0, 0, 0, .3) 2px 8px 8px;
    }

    .text-container {

      color: white;
      margin-top: 200px;
      margin-bottom: 50px;

    }

    .buttons {
      display: flex;
      flex-direction: column;
    }

    .btn {
      background-image: url(../media/images/button.png);
      background-repeat: no-repeat;
      background-size: cover;
      border: 2px solid black;
      box-shadow: rgba(0, 0, 0, .2) 15px 28px 25px -15px;
      color: #000000;
      cursor: pointer;
      display: inline-block;
      font-family: Neucha, sans-serif;
      font-size: 1rem;
      line-height: 10px;
      padding: .75rem;
      transition: all 235ms ease-in-out;
      border-bottom-left-radius: 15px 255px;
      border-bottom-right-radius: 225px 15px;
      border-top-left-radius: 255px 15px;
      border-top-right-radius: 15px 225px;
      touch-action: manipulation;
      Margin: 5px;
      width: 120px;
    }

    .btn:hover {
      box-shadow: rgba(0, 0, 0, .3) 2px 8px 8px -5px;
      transform: translate3d(0, 2px, 0);
      font-weight: 600;
    }

    .btn:focus {
      box-shadow: rgba(0, 0, 0, .3) 2px 8px 4px -6px;
      font-weight: 600;
    }
  </style>
</head>

<body>
  <div class="container mt-4">
    <div class="text-container text-center">
      <h2 style=" font-family: 'Sriracha', cursive;" >DIGITAL CLASSROOM</h2>
      <p>CONNECTING CLASSES DIGITALLY</p>
    </div>
    <div class="buttons">
      <a href="Login.php"> <button class="btn">LOGIN</button></a>
      <a href="Registration.php"><button class="btn">REGISTER</button></a>
    </div>
  </div>
</body>

</html>