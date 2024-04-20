<?php include '../db_connect.php';
      include "functions.php";
session_start();
?>

<!DOCTYPE html>
<html>
<head> 
    <title>Course Details</title>
    <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
    <link rel="stylesheet" href="courseDetail.css">
    <link rel="stylesheet" href="../admin/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src ="../js/navbar.js"></script></head>
    <script type="text/javascript">

    //create a javascript function named confirmation()
    function confirmation() {
	    answer = confirm("Do you want to delete this course?");
	    return answer;
    }
    </script>

<body>
<div class="Container">
    <div class="sidebar">
        <?php include '../NavBar/NavBar.php'?>
    </div>
    <div class="content" id="content"> 
        <header>
            <h1>Course Details</h1>
        </header>
        <!-- do course details code here  -->
        <?php 
        if(isset($_GET["view"])) {
            if (isset($_GET['course'])) {
                $course = getCourse($_GET['course'],$connect);
                $courseSectionResult = getCourseSectionsResult($_GET['course'],$connect);
                ?>
                <div class='course-details'>
                    <?php 
                    if ($_SESSION['usertype'] == "Admin") {
                        echo "<a href='../admin/tableCourseOverview.php'><button style='background-color: #3498db;'>Back</button></a>";
                    }
                    if ($_SESSION['usertype'] == "Provider") {
                        echo "<a href='../admin/courseOverview.php'><button style='background-color: #3498db;'>Back</button></a>";
                    }
                        echo "<a href='editCourse.php?edit&course=" .$_GET['course'] ."'><button style='background-color: lightgreen;'>Edit</button></a>";
                        echo "<a href='courseDetail.php?delete&course=" .$_GET['course'] ."' onclick='return confirmation();'><button style='background-color: red;'>Delete Course</button></a>";
                    ?>
                    <div class='table-wrapper'>
                        <table>
                            <thead>
                                <tr>
                                    <th>Course ID</th>
                                    <th>Course Title</th>
                                    <th>Course Description</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Provider Username</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $course['course_id'] ?></td>
                                    <td><?php echo $course['course_title'] ?></td>
                                    <td><?php echo $course['course_description'] ?></td>
                                    <td><?php echo $course['start_date'] ?></td>
                                    <td><?php echo $course['end_date'] ?></td>
                                    <td><?php echo $course['provider_username'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class='course-section-details table-wrapper'>
                <table>
                    <thead>
                        <tr>
                            <th>Course Section ID</th>
                            <th>Course Section Name</th>
                            <th>Instructor Username</th>
                            <th>Instructor Name</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Day</th>
                            <th>Status</th>
                            <th>Enrollment Count</th>
                            <th colspan='2'>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($courseSectionResult)) { 
                    $sql = "SELECT * FROM course_student WHERE course_section_id=" . $row['course_section_id'];
                    $studentCount = (mysqli_num_rows(mysqli_query($connect,$sql)));

                    ?>
                    <tr>
                        
                        <td><?php echo $row['course_section_id']?></td>
                        <td><?php echo $row['course_section_name']?></td>
                        <td><?php echo $row['username']?></td>
                        <td><?php echo $row['first_name'] . " " . $row['last_name']?></td>
                        <td><?php echo $row['start_time']?></td>
                        <td><?php echo $row['end_time']?></td>
                        <td><?php echo $row['day']?></td>
                        <td><?php echo $row['status']?></td>
                        <td><?php echo $studentCount . "/" . $row['max_student_num']?></td>
                        <td><a href='enrollStudentList.php?view&courseSection=<?php echo $row['course_section_id'] ?>'><button style="background-color:lightgreen;">Details</button></a></td>
                        <td><a href='courseDetail.php?delete&courseSection=<?php echo $row['course_section_id'] ?>' onclick='return confirmation();'><button style="background-color:red;">Delete</button></a></td>
                    <?php
                }
            }
        }
        ?>

    </div>
</div>
</body>
</html>

<?php 
if(isset($_GET["delete"])) {
    if(isset($_GET["course"])) {
        $course = $_GET["course"];
        $sql = "DELETE FROM course where course_id = $course";
        $result = mysqli_query($connect,$sql);
        if(!$result) {
            die(mysqli_error($result));
        }

        header("Location: courses.php");
    }
    if(isset($_GET["courseSection"])) {
        $courseSection = $_GET["courseSection"];
        $sql = "DELETE FROM course_section where course_section_id = $courseSection";
        $result = mysqli_query($connect,$sql);
        if(!$result) {
            die(mysqli_error($result));
        }
        echo "Delete";

        header("Location:" . $_SERVER["HTTP_REFERER"] );
        exit;
    }
}
?>