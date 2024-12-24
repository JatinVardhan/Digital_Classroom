<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="../media/images/icon1.png">

  <link rel="stylesheet" href=" https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --header-height: 3rem;
      --nav-width: 68px;
      --first-color: #3d8def;
      --first-color-light: rgb(214, 215, 215);
      --white-color: white;
      --body-font: "Nunito", sans-serif;
      --normal-font-size: 1rem;
      --z-fixed: 100;
    }

    *,
    ::before,
    ::after {
      box-sizing: border-box;
    }

    body {
      margin: var(--header-height) 0 0 0;
      font-family: 'Roboto', sans-serif !important;
      font-size: var(--normal-font-size);
      transition: 0.5s;
    }

    .header {
      width: 100%;
      height: var(--header-height);
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      align-items: center;
      padding: 0 1rem;
      background-color: rgb(58, 58, 70);
      z-index: var(--z-fixed);
      transition: 0.5s;
    }

    .header_toggle {
      color: var(--white-color);
      font-size: 1.5rem;
      cursor: pointer;
    }

    .header_logo {
      font-family: 'Sriracha', cursive;
      color: var(--white-color);
    }

    .l-navbar {
      position: fixed;
      top: 0;
      left: -30%;
      width: var(--nav-width);
      height: 100vh;
      background-color: var(--first-color);
      padding: 0.5rem 0.5rem 0 0;
      transition: 0.5s;
      z-index: var(--z-fixed);
    }

    .nav {
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      overflow: hidden;
    }

    .nav_logo,
    .nav_link {
      display: grid;
      grid-template-columns: max-content max-content;
      align-items: center;
      column-gap: 1rem;
      padding: 0.5rem 0 0.5rem 1.5rem;
      text-decoration: none;
    }

    .nav_logo {
      margin-bottom: 2rem;
    }

    .nav_logo-icon {
      font-size: 1.25rem;
      color: var(--white-color);
    }

    .nav_logo-name {
      color: var(--white-color);
      font-weight: 700;
    }

    .nav_link {
      position: relative;
      color: var(--first-color-light);
      margin-bottom: 1.5rem;
      transition: 0.3s;
    }

    .nav_link:hover {
      color: var(--white-color);
    }

    .nav_icon {
      font-size: 1.25rem;
    }

    .show {
      left: 0;
    }

    .body-pd {
      padding-left: calc(var(--nav-width) + 1rem);
    }

    .active {
      color: var(--white-color);
    }

    .active::before {
      content: "";
      position: absolute;
      left: 0;
      width: 2px;
      height: 32px;
      background-color: var(--white-color);
    }

    .dropdown-divider {
      padding: 1px;
      background-color: var(--first-color);
      border-radius: 5px;

    }

    .dropdown-menu {
      box-shadow: rgba(0, 0, 0, 0.3) 0 0 4px 2px;
      width: 31vh !important;
    }

    .dropdown-item {
      transition: ease 0.2s;
      font-size: 14px;
    }
    .dropdown-item i {
      font-size: larger;
    }

    .dropdown-item:hover {
      font-weight: 550;
      font-size: 14px;
      background-color: var(--first-color) !important;
      color: white !important;
      transform: scaleY(1.15);
    }

    @media screen and (min-width: 300px) {
      body {
        margin: calc(var(--header-height) + 1rem) 0 0 0;
        padding-left: calc(var(--nav-width) + 2rem);
        position: fixed;
        z-index: -1;
        width: 100%;
      }

      .header {
        height: calc(var(--header-height) + 1rem);
        padding: 0 2rem 0 calc(var(--nav-width) + 2rem);
      }

      .l-navbar {
        left: 0;
        padding: 1rem .5rem 0 0;
      }

      .show {
        width: calc(var(--nav-width) + 172px);
      }

      .body-pd {
        padding-left: calc(var(--nav-width) + 188px);
      }
    }
  </style>
</head>

<body id="body-pd">
  <header class="header" id="header">
    <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
    <div class="header_logo mx-auto ">
      <h1>DIGITAL CLASSROOM</h1>
    </div>
  </header>
  <div class="l-navbar" id="nav-bar">
    <nav class="nav">
      <div>

        <div class="nav_list">
          <a href="SADashboard.php" class="nav_link ">
            <i class="bx bx-grid-alt nav_icon"></i>
            <span class="nav_name">Dashboard</span>
          </a>
          <a href="#" class="nav_link" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class='bx bx-spreadsheet nav_icon'></i>
            <span class="nav_name">Manage Departments <i class='bx bx-chevron-down nav_icon'></i></span>
          </a>
          <ul class="dropdown-menu mx-5 py-0" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item py-2" href="ManageAdmins.php"><i class="fa-solid fa-users-viewfinder"></i> View Admins</a></li>
            <div class="dropdown-divider my-0 mx-1"></div>
            <li><a class="dropdown-item py-2" href="AddDepartment.php"><i class="fa-regular fa-square-plus"></i> Add Departments</a></li>
            <div class="dropdown-divider my-0  mx-1"></div>
            <li><a class="dropdown-item py-2" href="DeleteDepartment.php"><i class="fa-regular fa-square-minus"></i> Delete Departments</a></li>
          </ul>
          <a href="manageTeachers.php" class="nav_link">
            <i class="bx bx-group nav_icon"></i>
            <span class="nav_name">Manage Teachers</span>
          </a>
          <a href="manageClasses.php" class="nav_link">
            <i class="bx bx-book-open nav_icon"></i>
            <span class="nav_name">Manage Classes</span>
          </a>
          <a href="manageAttendance.php" class="nav_link">
          <i class='bx bx-calendar-check nav_icon'></i>
            <span class="nav_name">Attendance Alert</span>
          </a>
          <a href="#" class="nav_link" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bx bx-user nav_icon"></i>
            <span class="nav_name">Edit Profile <i class='bx bx-chevron-down nav_icon'></i></span>
          </a>
          <ul class="dropdown-menu mx-5 py-0" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item py-2" href="ProfileEdit.php"><i class="fa-regular fa-pen-to-square"></i> Edit Information</a></li>
            <div class="dropdown-divider my-0 mx-1"></div>
            <li><a class="dropdown-item py-2" href="ManagePassword.php"><i class="fa fa-key" ></i> Manage Password</a></li>
            <div class="dropdown-divider my-0  mx-1"></div>
            <li><a class="dropdown-item py-2" href="DeleteProfile.php"><i class="fa-regular fa-trash-can"></i> Delete Profile</a></li>
          </ul>
        </div>
      </div>
      <a href="LogOut.php" class="nav_link">
        <i class="bx bx-log-out nav_icon"></i>
        <span class="nav_name">SignOut</span>
      </a>
    </nav>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function(event) {
      const showNavbar = (toggleId, navId, bodyId, headerId) => {
        const toggle = document.getElementById(toggleId),
          nav = document.getElementById(navId),
          bodypd = document.getElementById(bodyId),
          headerpd = document.getElementById(headerId),
          mainContent = document.querySelector('main');

        // Function to hide main content
        const hideMainContent = () => {
          mainContent.style.display = 'none';
        };

        // Function to show main content
        const showMainContent = () => {
          mainContent.style.display = 'block';
        };

        // Validate that all variables exist
        if (toggle && nav && bodypd && headerpd) {
          toggle.addEventListener('click', () => {
            // Show/hide navbar
            nav.classList.toggle('show');
            // Change icon
            toggle.classList.toggle('bx-x');
            // Add/remove padding to body
            bodypd.classList.toggle('body-pd');
            // Add/remove padding to header
            headerpd.classList.toggle('body-pd');
            // Toggle main content visibility based on screen size and sidebar toggle
            if (window.innerWidth < 700 && nav.classList.contains('show')) {
              hideMainContent();
            } else {
              showMainContent();
            }
          });
        }
      };

      showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

    });


    document.addEventListener("DOMContentLoaded", function() {
      var deleteButtons = document.getElementsByClassName("delete");

      for (var i = 0; i < deleteButtons.length; i++) {
        deleteButtons[i].addEventListener("click", confirmDelete);
      }

      function confirmDelete() {
        var confirmation = confirm("Are you sure you want to delete this ?");
        if (!confirmation) {
          event.preventDefault();
          return false;
        }
      }
    });

    function goBack() {
      window.history.back();
    }

    setTimeout(function() {
      document.getElementsByClassName('alert-danger')[0].remove();
    }, 2000);
    setTimeout(function() {
      document.getElementsByClassName('alert-warning')[0].remove();
    }, 5000);
  </script>
</body>

</html>