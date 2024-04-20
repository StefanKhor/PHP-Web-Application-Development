<?php include '../db_connect.php';
session_start();
if(isset($_SESSION['usertype']) != "Admin" or isset($_SESSION['usertype']) != "Provider") {
    header("Location: ../login.php");
}
?>

<!DOCTYPE html>
<html>
<head> 
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
    <link rel="stylesheet" href="courses.css"/>
    <link rel="stylesheet" href="dashboard.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src ="../js/navbar.js"></script>    <script src ="../js/navbar.js"></script>
</head>

<body>
<div class="Container">
    <div class="sidebar">
        <?php include '../navBar/navBar.php'?>
    </div>
    <div class="content" id="content"> 
        <header class="header-bar">
            <h1>Courses Overview</h1>
            <?php if($_SESSION["usertype"] == "Admin") { ?>
                <a onclick='selectProvider()' class='add-courses-btn'><button>Add Course</button></a>
                <?php
                }
                else if($_SESSION["usertype"] == "Provider") { ?>
                    <a href="../trainingProvider/createCourse.php" class='add-courses-btn'><button>Add Course</button></a>
                <?php
                } ?>
        </header>

        <!-- Dialog for create course select training provider -->
        <dialog selectProviderModal>
            <?php if($_SESSION["usertype"] == "Admin") { ?>
                <!-- Show the select training provider -->
                <form action='../trainingProvider/createCourse.php' method='POST'>
                <h3>Choose a training provider</h3>
                <div class='input-box'>
                <select name='providerUsername' class="selectProvider" required >
                    <option disabled selected value>Select A Training Provider</option>
                    <?php
                    $sql = 'SELECT * FROM training_provider;';
                    $result = mysqli_query($connect,$sql);
                    $count = mysqli_num_rows($result);
                    if ($count == 0) {
                        ?>
                        <option disabled selected value>No Training Provider Found</option>

                    <?php
                    }
                    while($row = mysqli_fetch_assoc($result)) { ?>
                        <option value="<?php echo $row["username"] ?>"><?php echo $row["username"] . " - " . $row["provider_name"]?></option> 
                    <?php } 
                    ?>
                </select>
                </div>
                <div id='modal-button'>
                    <input class='back-btn' type='button' value='Cancel' onclick='closeModal()'>    
                    <input class='next-btn' type='submit' name='selectProvider' value='Next'>    
                </div>
            </form>
            <?php
            } ?>
        </dialog>

        
        <!-- Display the list of courses -->
        <div class="ongoing-course">
            <h4>Ongoing Courses</h4>
                        <?php
                        //Get the ongoing course
                        if($_SESSION['usertype'] == "Admin") {
                            $sql = "SELECT * FROM course WHERE end_date > '" . date("Y-m-d") . "';";
                        }
                        else if ($_SESSION['usertype'] == "Provider") {
                            $sql = "SELECT * FROM course WHERE provider_username = '" . $_SESSION["username"] . "' and end_date > '" . date("Y-m-d") . "';";
                        }
                        $result = mysqli_query($connect,$sql); 
                ?>
            <div class='table-wrapper table-wrapper-half-height'>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Course ID</th>
                                        <th>Course Title</th>
                                        <th>Course Description</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Provider Username</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($course = mysqli_fetch_assoc($result)) { ?>
                                    <tr>
                                        <td><?php echo $course['course_id'] ?></td>
                                        <td><?php echo $course['course_title'] ?></td>
                                        <td><?php echo $course['course_description'] ?></td>
                                        <td><?php echo $course['start_date'] ?></td>
                                        <td><?php echo $course['end_date'] ?></td>
                                        <td><?php echo $course['provider_username'] ?></td>
                                        <td><a href='../trainingProvider/courseDetail.php?view&course=<?php echo $course['course_id']?>'><button>Details</button></a></td>
                                    </tr> <?php
                                    } ?>
                                </tbody>
                            </table>
            </div>   
        </div>

        <div class="ended-course">
            <h4>Ended Courses</h4>
            <div class="table-wrapper table-wrapper-half-height">
                    <?php 
                    //Get the ended course
                    if($_SESSION['usertype'] == "Admin") { 
                        $sql = "SELECT * FROM course WHERE end_date <= '" . date("Y-m-d") . "';";
                    }
                    else if ($_SESSION['usertype'] == "Provider") {
                        $sql = "SELECT * FROM course WHERE provider_username = '" . $_SESSION["username"] . "' and end_date <= '" . date("Y-m-d") . "';";
                    }
                    $result = mysqli_query($connect,$sql);
                    ?>
                <table>
                    <thead>
                        <tr>
                            <th>Course ID</th>
                            <th>Course Title</th>
                            <th>Course Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Provider Username</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($course = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $course['course_id'] ?></td>
                            <td><?php echo $course['course_title'] ?></td>
                            <td><?php echo $course['course_description'] ?></td>
                            <td><?php echo $course['start_date'] ?></td>
                            <td><?php echo $course['end_date'] ?></td>
                            <td><?php echo $course['provider_username'] ?></td>
                            <td><a href='../trainingProvider/courseDetail.php?view&course=<?php echo $course['course_id']?>'><button>Details</button></a></td>
                        </tr> <?php
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>
</div>
</body>
</html>

<?php
    function displayCourses($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<a href="courseOverview.php?course=' . $row["course_id"] . '">';
            echo '<div class="display-course">';
            echo '<p class="course-id">' . $row["course_id"] . '</p>';
            echo '<p class="course-title">' . $row["course_title"] . '</p>';
            echo '</div>';
            echo '</a>';
        }
    }

?>

<script>
    const selectProviderModal = document.querySelector("[selectProviderModal]");

    function closeModal() {
        selectProviderModal.close();
    }
    function selectProvider() {
        selectProviderModal.showModal();
    }

</script>
