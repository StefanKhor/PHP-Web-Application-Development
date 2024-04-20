<?php include "../db_connect.php";
    session_start();
    if(isset($_SESSION['usertype']) != "Admin" or isset($_SESSION['usertype']) != "Provider") {
        header("Location: ../login.php");
    }
?>

<?php 
    if(isset($_POST['submit'])) {

        if($_SESSION['usertype'] == "Admin") {
            if(empty($_POST['providerUsername'])) {
                die("Training Provider username is required");
            }
        }
        if(empty($_POST['courseName'])) {
            die("Course name is required"); 
        }
        
        if(empty($_POST['courseIntro'])) {
            die("Course Introduction is required"); 
        }
        if(empty($_POST['startDate'])) {
            die("Start date is required"); 
        }
        if(empty($_POST['endDate'])) {
            die("End date is required"); 
        }

        $imagePath = "../files/tpms.png";
        // Upload image
        if(isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0){
            // Directory where the images are stored
            $imageFolderPath = "../files/";
            

            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
            $filename = $_FILES["photo"]["name"];
            $filetype = $_FILES["photo"]["type"];
            $filesize = $_FILES["photo"]["size"];
        
            // Verify file extension
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)) die("Error: Please select a valid file format.");
        
            // Verify file size - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if($filesize > $maxsize) die("Error: File size is larger than the allowed limit.");
        
            // Verify MYME type of the file
            if(in_array($filetype, $allowed)){
                $newFileName = uniqid();
                if ($filetype == "image/jpg") {
                    $newFileName .= ".jpg";
                }
                else if ($filetype == "image/jpeg") {
                    $newFileName .= ".jpeg";
                }
                else if ($filetype == "image/png") {
                    $newFileName .= ".png";
                }

                $imagePath = $imageFolderPath . $newFileName;
                move_uploaded_file($_FILES["photo"]["tmp_name"], $imagePath);                
                 
            } else{
                echo "Error: There was a problem uploading your file. Please try again."; 
            }
        }
        if($_SESSION['usertype'] == "Admin") {
            $sql1 = "INSERT into course(provider_username, course_title, course_description, start_date, end_date,course_image_path) values ('" . $_POST['providerUsername'] . "', '" . $_POST['courseName'] . "', '" . $_POST['courseIntro'] . "', '" . $_POST['startDate'] . "', '" . $_POST['endDate'] . "', '" . $imagePath ."');";
        }
        else if ($_SESSION['usertype'] == "Provider") {
            $sql1 = "INSERT into course(provider_username, course_title, course_description, start_date, end_date,course_image_path) values ('" . $_SESSION['username'] . "', '" . $_POST['courseName'] . "', '" . $_POST['courseIntro'] . "', '" . $_POST['startDate'] . "', '" . $_POST['endDate'] . "', '" . $imagePath ."');";
        }
        
        
        $result = mysqli_query($connect,$sql1);
        if(!$result) {
            die('Cannot enter data'.mysqli_error($connect));
        }
        
        $courseID = mysqli_insert_id($connect); // Get the course_ID from the last query
        
        for($i=0; $i<count($_POST['sectionName']); $i++) {
            if(empty($_POST['instructorUsername'][$i])) {
                die("Instructor username is required"); 
            }
            if(empty($_POST['sectionName'][$i])) {
                die("Section name is required"); 
            }
            if(empty($_POST['maxStudentNum'][$i])) {
                die("Maximum Student Number is required"); 
            }
        
            $sql2 = "INSERT into course_section(course_id, username, course_section_name, status, max_student_num)
                 values (" . $courseID . ", '" . $_POST['instructorUsername'][$i] . "', '" . $_POST['sectionName'][$i] . "', " . "'Open'," .$_POST['maxStudentNum'][$i] . ");";
        
            $result = mysqli_query($connect,$sql2);
            if(!$result) {
                die('Cannot enter data'.mysqli_error($connect));
            }
            else {
                if($_SESSION["usertype"] == "Admin") {
                    header("Location: ../admin/tableCourseOverview.php");
                }
                else if ($_SESSION["usertype"] == "Provider"){
                    header("Location: ../admin/courseOverview.php");
                }
            }            
        }

    }
    
?>

<!DOCTYPE html>
<html>
<head> 
    <title>Add Course</title>
    <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
    <link rel="stylesheet" type="text/css" href="../css/createCourse.css">
    <script text="text/javascript" src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src ="../js/navbar.js"></script></head>
</head>

<body>
<div class="Container">
    <div class="sidebar">
        <?php include '../navBar/navBar.php'?>
    </div>
    <div class="content" id="content">
        <header>
            <h1>Create Course</h1>
        </header>
        <div class="Container1">
            <form action="createCourse.php" method="post" id="courseForm" enctype="multipart/form-data">
                <?php if(isset($_POST["selectProvider"])) { ?>
                    <input type="text" name="providerUsername" hidden value="<?php echo $_POST["providerUsername"]?>">
                <?php
                } ?>

                <div class="input-box">
                <label for="courseName">Course Name</label>
                <input type="text" name="courseName" required> 
                </div>

                <div class="input-box">
                <label for="startDate">Start Date</label>
                <input type="date" name="startDate" required> 
                </div>

                <div class="input-box">
                <label for="endDate">End Date</label>
                <input type="date" name="endDate" required> 
                </div>

                <div class="input-box">
                <label for="courseIntro">Course Introduction</label>
                <textarea name="courseIntro" rows="5" cols="40"></textarea>
                </div>

                <div class="input-box" style="margin-right:50px;">
                <label for="Course Image">Upload course image</label><br>
                <input type="file" name="photo">
                </div>

                <div id="courseSection">            
                    <div class="input-box">
                    <label for="sectionName">Section Name</label>
                    <input type="text" name="sectionName[]" required>
                    </div>


                    <div class="input-box">
                    <label for="instructorUsername">Instructor Username</label>
                    <!-- <input type="text" name="instructorUsername[]" list="instructors" autocomplete="off" required/> -->
                    <select name="instructorUsername[]" id="originalSelector" required >
                    <?php 
                            if($_SESSION['usertype'] == 'Admin') {
                                $sql = "SELECT * FROM instructor where provider_username" . "= '" . $_POST['providerUsername'] . "';";
                            }
                            else if($_SESSION['usertype'] == 'Provider') {
                                $sql = "SELECT * FROM instructor where provider_username" . "= '" . $_SESSION['username'] . "';";
                            }
                            $result = mysqli_query($connect,$sql);
                            $count = mysqli_num_rows($result);
                            echo $count;
                            if ($count == 0) {
                                echo $count;
                                ?>
                                <option disabled selected value>No Available Instructors Found</option>

                            <?php
                            }
                            while($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <option value="<?php echo $row["username"] ?>"><?php echo $row["username"] . " - " 
                                . $row["first_name"] . " " . $row["last_name"]?></option> 
                            <?php } 
                        ?>
                    </select>
                    </div>

                    <div class="input-box">
                        <label for="maxStudentNum">Maximum Student Allowed</label>
                        <input type="number" name="maxStudentNum[]" required>
                    </div>
                    <hr>
                </div>

                <div id="additionalSection"></div>
                
                <div class="addSection">
                <input type="button" style="display:inline-block; background-color:lightgreen;" value="Add More Section" onclick="addSection()">
                <input type="submit" style="display:inline-block; background-color:lightgreen;" value="Create Course" name="submit">
                    <?php if ($_SESSION["usertype"] == "Admin" ) {
                        echo "<a href='../admin/tableCourseOverview.php'><input type='button' style='display:inline-block; background-color:#3498db;' value= 'Back'></a><br><br>";
                    }
                    else if ($_SESSION["usertype"] == "Provider"){
                        echo "<a href='../admin/courseOverview.php'><input type='button' style='display:inline-block; background-color:#3498db;' value= 'Back'></a><br><br>";                
                    }?>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

