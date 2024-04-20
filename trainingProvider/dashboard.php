<?php 
    include "../db_connect.php";
    session_start();
?>

<html>
<head>
<title>Dashboard</title>
    <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
    <link rel="stylesheet" href="../css/tPdashboard.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src ="../js/navbar.js"></script></head>

<body>
<div class="Container">
    <div class="sidebar">
        <?php include '../navBar/navBar.php'?>
    </div>
    <div class="content" id="content"> 
        <header>
            <h1>Training Provider Dashboard</h1>
        </header>
        <?php 
            $sql = "SELECT * FROM user INNER JOIN instructor on user.username = instructor.username WHERE provider_username = '". $_SESSION["username"] . "';";
            $result = mysqli_query($connect,$sql);
            $countInstructor = mysqli_num_rows($result);

            $sql = "SELECT * FROM user INNER JOIN student on user.username = student.username WHERE provider_username = '". $_SESSION["username"] . "';";
            $result = mysqli_query($connect,$sql);
            $countStudent = mysqli_num_rows($result);

            $sql = "SELECT * FROM course where provider_username = '" . $_SESSION["username"] . "';";
            $result = mysqli_query($connect,$sql);
            $countCourse = mysqli_num_rows($result);
            ?>

            <div id="root">
                <div class="main-dashboard-container">
                        <div class="c-dashboardInfo">
                            <div class="wrap">
                                <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Total Student
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                    </path>
                                </h4>
                                    <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $countStudent ?></span>
                            </div>
                        </div>
                        <div class="c-dashboardInfo">
                            <div class="wrap">
                                <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Total Instructor
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                    </path>
                                </h4>
                                    <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $countInstructor ?></span>
                            </div>
                        </div>
                        <div class="c-dashboardInfo">
                            <div class="wrap">
                                <h4 class="heading heading5 hind-font medium-font-weight c-dashboardInfo__title">Total Course
                                    <path fill="none" d="M0 0h24v24H0z"></path>
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z">
                                    </path>
                                </h4>
                                    <span class="hind-font caption-12 c-dashboardInfo__count"><?php echo $countCourse ?></span>
                            </div>
                        </div>
                </div>
            </div>

            <!-- <div class="information">
                <div class="student-card">
                    <h3>Total Student</h3>
                    <h3><?php echo $countStudent ?></h3>
                </div>
                <div class="instructor-card">
                    <h3>Total Instructor</h3>
                    <h3><?php echo $countInstructor ?></h3>
                </div>
                <div class="course-card">
                    <h3>Total Courses</h3>
                    <h3><?php echo $countCourse ?></h3> 
                </div>
            </div> -->

            <hr class="pointy-hr">
            
            <div class="operationsBtn">
                <a href="../admin/courseOverview.php"><button>Manage Courses</button></a>
                <a href="../admin/dashboard.php"><button>Manage Accounts</button></a>
                <a href="feedback.php"><button>View Feedback</button></a>
            </div>
    </div>
</div>
</body>


</html>
