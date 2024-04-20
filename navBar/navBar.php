<html>
    <head>
        <meta charset="UTF-8"/>
        <title>Side Menu Bar | Web Development Assignment </title>
        <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>
        <nav>
            <ul>
                <?php
                     if($_SESSION["usertype"] == "Admin") {
                        echo "<li>";
                        echo "<a href='../admin/dashboard.php' class='logo'>";
                        echo "<img src='../files/logo1.png' alt=''>";
                        echo "<span class='nav-item'>TPMS</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../admin/dashboard.php'>";
                        echo "<i class='fas fa-home'></i>";
                        echo "<span class='nav-item'>Dashboard</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../admin/tableCourseOverview.php'>";
                        echo "<i class='fas fa-server'></i>";
                        echo "<span class='nav-item'>Course Overview</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../admin/profile.php'>";
                        echo "<i class='fas fa-user'></i>";
                        echo "<span class='nav-item'>Profile</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../logout.php' class='logout'>";
                        echo "<i class='fas fa-sign-out-alt'></i>";
                        echo "<span class='nav-item'>Logout</span>";
                        echo "</a>";
                        echo "</li>";
                     }
                     else if ($_SESSION["usertype"] == "Student"){
                        echo "<li>";
                        echo "<a href='../student/dashboard.php' class='logo'>";
                        echo "<img src='../files/logo1.png' alt=''>";
                        echo "<span class='nav-item'>TPMS</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../student/dashboard.php'>";
                        echo "<i class='fas fa-home'></i>";
                        echo "<span class='nav-item'>Dashboard</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../student/availableCourse.php'>";
                        echo "<i class='fas fa-server'></i>";
                        echo "<span class='nav-item'>Available Course</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../student/registeredCourse.php'>";
                        echo "<i class='fas fa-book'></i>";
                        echo "<span class='nav-item'>Registered Course</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../student/completedCourse.php'>";
                        echo "<i class='fas fa-bullhorn'></i>";
                        echo "<span class='nav-item'>Completed Course</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../student/profile.php'>";
                        echo "<i class='fas fa-user'></i>";
                        echo "<span class='nav-item'>Profile</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../logout.php' class='logout'>";
                        echo "<i class='fas fa-sign-out-alt'></i>";
                        echo "<span class='nav-item'>Logout</span>";
                        echo "</a>";
                        echo "</li>";
                     }
                     else if ($_SESSION["usertype"] == "Instructor"){
                        echo "<li>";
                        echo "<a href='../instructor/dashboard.php' class='logo'>";
                        echo "<img src='../files/logo1.png' alt=''>";
                        echo "<span class='nav-item'>TPMS</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../instructor/dashboard.php'>";
                        echo "<i class='fas fa-home'></i>";
                        echo "<span class='nav-item'>Dashboard</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../instructor/myCourse.php'>";
                        echo "<i class='fas fa-book'></i>";
                        echo "<span class='nav-item'>My Course</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../instructor/feedback.php'>";
                        echo "<i class='fas fa-bullhorn'></i>";
                        echo "<span class='nav-item'>Feedback</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../instructor/profile.php'>";
                        echo "<i class='fas fa-user'></i>";
                        echo "<span class='nav-item'>Profile</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../logout.php' class='logout'>";
                        echo "<i class='fas fa-sign-out-alt'></i>";
                        echo "<span class='nav-item'>Logout</span>";
                        echo "</a>";
                        echo "</li>";
                     }
                     else if ($_SESSION["usertype"] == "Provider"){
                        echo "<li>";
                        echo "<a href='../trainingProvider/dashboard.php' class='logo'>";
                        echo "<img src='../files/logo1.png' alt=''>";
                        echo "<span class='nav-item'>TPMS</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../trainingProvider/dashboard.php'>";
                        echo "<i class='fas fa-home'></i>";
                        echo "<span class='nav-item'>Dashboard</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../admin/dashboard.php'>";
                        echo "<i class='fas fa-book'></i>";
                        echo "<span class='nav-item'>Account</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../admin/courseOverview.php'>";
                        echo "<i class='fas fa-book'></i>";
                        echo "<span class='nav-item'>Courses</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../trainingProvider/feedback.php'>";
                        echo "<i class='fas fa-bullhorn'></i>";
                        echo "<span class='nav-item'>Feedback</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../trainingProvider/profile.php'>";
                        echo "<i class='fas fa-user'></i>";
                        echo "<span class='nav-item'>Profile</span>";
                        echo "</a>";
                        echo "</li>";

                        echo "<li><a href='../logout.php' class='logout'>";
                        echo "<i class='fas fa-sign-out-alt'></i>";
                        echo "<span class='nav-item'>Logout</span>";
                        echo "</a>";
                        echo "</li>";
                     }
                ?>
            </ul>
        </nav>
    </body>

    <script>
        var navItems = document.querySelectorAll('nav ul li a');

        // Add click event listener to each navigation item
        navItems.forEach(function(navItem) {
        navItem.addEventListener('click', function(e) {
            // Remove the 'active' class from all navigation items
            navItems.forEach(function(item) {
            item.classList.remove('active');
            });

            // Add the 'active' class to the clicked navigation item
            this.classList.add('active');
        });
        });


        
    </script>
</html>