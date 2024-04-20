<?php

include '../db_connect.php';
include 'function2.php';

if(isset($_SESSION['usertype'])) {
    if($_SESSION['usertype'] != "Admin" || $_SESSION['usertype'] != "Provider" || $_SESSION['usertype'] != "Instructor" || $_SESSION['usertype'] != "Student"){
    header("Location: ../login.php");
    }
}

function registerCourseDashboard(){
    createCourseDashboard(false);
}

function completedCourseDashboard(){
    createCourseDashboard(true,true);
}

function createCourseDashboard($myCoursePage = true,$completed = false){
    global $connect;
    echo "<h1>";
    if ($_SESSION["usertype"]  == "Student"){
        if(!$myCoursePage){
            echo "Register For Course";
            // rules is set that only course that student have not register will be displayed and the course (any section in the course) must be open in order to be  display
            // for session testing
            $student_training_provider_sql = "SELECT * FROM student WHERE username = '".$_SESSION['username']."'";
            $student_training_provider = mysqli_fetch_assoc(mysqli_query($connect,$student_training_provider_sql));
            
            $course_sql = "SELECT DISTINCT * FROM course 
                            WHERE provider_username = '".$student_training_provider['provider_username']."' 
                            AND course_id IN (
                                SELECT course_id 
                                FROM course_section 
                                WHERE status = 'Open' 
                                AND course_section_id NOT IN (
                                    SELECT course_section_id 
                                    FROM course_student 
                                    WHERE username = '".$_SESSION['username']."'
                                )
                                GROUP BY course_id
                                HAVING COUNT(course_section_id) = (
                                    SELECT COUNT(*) 
                                    FROM course_section 
                                    WHERE course_section.course_id = course.course_id
                                )
                            )
                            ORDER BY course_title";


        }
        else{
            if($completed){
                echo "Completed Course";
                $course_sql = "SELECT * FROM course_student AS cst
                                JOIN course_section AS csc ON csc.course_section_id = cst.course_section_id
                                JOIN course AS c ON c.course_id = csc.course_id
                                WHERE cst.username = '{$_SESSION['username']}' 
                                    AND course_completed = 1";
            }
            else{
                echo "My Course";
                $course_sql = "SELECT * FROM course_student AS cst
                                JOIN course_section AS csc ON csc.course_section_id = cst.course_section_id
                                JOIN course AS c ON c.course_id = csc.course_id
                                WHERE cst.username = '{$_SESSION['username']}' 
                                    AND course_completed = 0";
            }
        }
    }
    else if ($_SESSION["usertype"]  == "Instructor"){
        echo "My Course";
        $course_sql = "SELECT * FROM course_section AS csc
                        JOIN course AS c ON c.course_id = csc.course_id
                        WHERE csc.username = '{$_SESSION['username']}'";
    }

    $result = mysqli_query($connect,$course_sql);
    echo "</h1>";
    echo '<div class="class-container">';
    retrieveEachDashboardCourse($result,$myCoursePage);
    echo '</div>';
}

function retrieveEachDashboardCourse($result,$myCoursePage){
    while($eachCourse = mysqli_fetch_array($result)){
        echo '<div class="class-child">';
            echo '<a href = "'.generateDashboardCourseLink($eachCourse,$myCoursePage).'">';
                if($eachCourse["course_image_path"]==null){
                    echo '<div class="courseImage"><img src = "../files/tpms.png" alt = "'.$eachCourse["course_title"].'"/></div>';
                }
                else{
                    echo '<div class="courseImage"><img src = "'.$eachCourse["course_image_path"].'" alt = "'.$eachCourse["course_title"].'"/></div>';
                }
                echo '<h2>';
                    if($myCoursePage){
                        echo $eachCourse["course_section_name"]." ";
                    }
                    echo $eachCourse["course_title"];
                echo '</h2>';
            echo '</a>';
        echo '</div>';
    }
}

function generateDashboardCourseLink($eachCourse,$myCoursePage){
    global $connect;
    if(!$myCoursePage){
        return 'registerCourse.php?course='.$eachCourse['course_id'];
    }
    else {
        return 'courseDetail.php?course='.$eachCourse['course_id'].'&section='.$eachCourse["course_section_id"];
    }
}

function generateHTMLDetail($title,$variable){
    echo '<p><span class="title">'.$title.'</span><span class="colon">:</span>'.$variable.'</p>';
}

function generateHTMLFeedbackDetail($variable){
    echo '<h3>Feedback:</h3><p>'.$variable.'</p>';
}
function generateHTMLStarDetail($variable){
    echo '<h3>Rating:</h3><p>';
    $count = 5;
    while($count!=0){
        if($variable > 0){
            echo '<img src="../files/yellow-star.png" alt="yellow-star">';
        }
        else{
            echo '<img src="../files/blank-star.png" alt="blank-star">';  
        }
        $variable = $variable-1;
        $count = $count - 1;
    }
    echo '</p>';
}

function generateHTMLEditProfile($title,$inputName,$value,$inputType = "text"){
    echo 
    '<p> 
        <span class="title">'.$title.'</span>
        <span class="colon">:</span>
        <input type="'.$inputType.'" name="'.$inputName.'" value = "'.$value.'" required>
    </p>';
}

function generateHTMLChangePassword($title,$inputName){
    echo 
    '<p> 
        <span class="title">'.$title.'</span>
        <span class="colon">:</span>
        <input id="'.$inputName.'" name="'.$inputName.'" placeholder = "'.$title.'" type="password" required>
    </p>';
}

function generateHTMLEditCourseSection($title,$inputName,$value,$inputType = "text",$attribute1 = "",$attribute2="",$attribute3=""){
    echo 
    '<h3> 
        <span class="title">'.$title.'</span>
        <span class="colon">:</span>
        <input type="'.$inputType.'" name="'.$inputName.'" value = "'.$value.'" required ' .$attribute1.' ' .$attribute1.' ' .$attribute1.'>
    </h3>';
}
function generateOptionHTMLEditCourseSection($title,$inputName,$options){
    echo 
    '<h3> 
        <span class="title">'.$title.'</span>
        <span class="colon">:</span>
        <select name = "'.$inputName.'" id="'.$inputName.'" >'.$options.'</select>
    </h3>';
}

function generateFeedbackForm($formName,$title1,$inputName1,$title2,$inputName2,$submitForm){
    echo<<<HTML
        <form name="{$formName}" method="post" action="">
            <h3 class="title">{$title1}:</h3>
            <p> 
                <div class="{$inputName1}">
                    <input type="radio" id="{$inputName1}5" name="{$inputName1}" value="5" />
                    <label for="{$inputName1}5" title="text">5</label>

                    <input type="radio" id="{$inputName1}4" name="{$inputName1}" value="4" />
                    <label for="{$inputName1}4" title="text"></label>

                    <input type="radio" id="{$inputName1}3" name="{$inputName1}" value="3" />
                    <label for="{$inputName1}3" title="text"></label>

                    <input type="radio" id="{$inputName1}2" name="{$inputName1}" value="2" />
                    <label for="{$inputName1}2" title="text"></label>

                    <input type="radio" id="{$inputName1}1" name="{$inputName1}" value="1" />
                    <label for="{$inputName1}1" title="text"></label>
                </div>
            </p>
            <br>
            <h3 class="title">{$title2}:</h3>
            <p> 
                <textarea name={$inputName2} maxlength ='100' required></textarea>
            </p>

            <button type="submit" name={$submitForm}>Submit</button>
        </form>
    HTML;
}

function generateCertificate($studentName,$courseTitle,$completeDate) {
    echo<<<HTML
    <script>
        *{ color-adjust: exact;
            -webkit-print-color-adjust: exact; 
            print-color-adjust: exact; 
        }
    </script>
    <div style="width:800px; height:auto;text-align:center;margin:10px;padding:20px; border: 5px solid black;background-color: #618597;">
        <div style="width:750px; height:auto;padding:20px; text-align:center; border: 3px solid black;background-color:white;">
            <span><img style="width:280px;height:auto;"src="../files/tpms.png"></span><br><br>
            <span style="font-size:55px; font-weight:bold;">Certificate of Completion</span>
            <br><br>
            <span style="font-size:25px"><i>This is to certify that</i></span>
            <br><br>
            <span style="font-size:30px"><b>{$studentName}</b></span><br/><br/>
            <span style="font-size:25px"><i>has completed the course</i></span> <br/><br/>
            <span style="font-size:30px">$courseTitle</span> <br/><br/>
            <span style="font-size:25px"><i>on</i></span><br><br>
            <span style="font-size:30px">$completeDate</span>
        </div>
    </div>
    HTML;
}

function createProfilePage(){
    global $connect;
    if(array_key_exists('postdata',$_SESSION)){
        unset($_SESSION['postdata']);
    }
    if(isset($_POST['submitEdit'])){
        $contactNumber = $_POST["contactNumber"];
        $emailAddress = $_POST["emailAddress"];
        if($_SESSION['usertype'] == "Student"){
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $dateOfBirth = $_POST["dateOfBirth"];

            $edit_profile_sql = "UPDATE student
                                    SET first_name = '$firstName', last_name = '$lastName', date_of_birth = '$dateOfBirth', contact_number = '$contactNumber', email = '$emailAddress' 
                                    WHERE username = '".$_SESSION['username']."'";
        }

        else if($_SESSION['usertype'] == "Instructor"){
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            
            $edit_profile_sql = "UPDATE instructor
                                    SET first_name = '$firstName', last_name = '$lastName', contact_number = '$contactNumber', email = '$emailAddress' 
                                    WHERE username = '".$_SESSION['username']."'";
        }

        else if($_SESSION['usertype'] == "Provider"){
            $providerName = $_POST["providerName"];
            
            $edit_profile_sql = "UPDATE training_provider
                                    SET provider_name = '$providerName', contact_number = '$contactNumber', email = '$emailAddress' 
                                    WHERE username = '".$_SESSION['username']."'";
        }
 

        mysqli_query($connect, $edit_profile_sql); 
        generateJavaScriptAlert("Successfully Edited Profile");
        header("Refresh:0");
        exit;
    }
    if (isset($_POST["submitChangePWD"])){
        $oldPassword = $_POST["oldPassword"];
        $old_pass = mysqli_fetch_assoc(mysqli_query($connect,"SELECT password_hash FROM user WHERE username = '".$_SESSION['username']."'"));

        if(password_verify($oldPassword,$old_pass["password_hash"])){
            $newPassword = $_POST["newPassword"];
            $hashed_password = password_hash($newPassword,PASSWORD_DEFAULT);
            $edit_password_sql = "UPDATE user SET password_hash = '" .$hashed_password ."' WHERE username = '".$_SESSION['username']."'";
            mysqli_query($connect, $edit_password_sql); 
            header("Refresh:0");
            exit;
        }
        else{
            generateJavaScriptAlert("Old password is wrong");
        }
    }

    if (isset($_FILES["uploadedPicture"])){

        $imageFolderPath = "../files/profile_picture/";

        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "png" => "image/png");
        $filename = $_FILES["uploadedPicture"]["name"];
        $filetype = $_FILES["uploadedPicture"]["type"];
        $filesize = $_FILES["uploadedPicture"]["size"];
    
        // better handle error in javascript ??
        // // Verify file extension
        if(!array_key_exists(pathinfo($filename, PATHINFO_EXTENSION), $allowed)) 
            generateJavaScriptAlert("Error: Please select a valid file format.");
    
        else{
            // // Verify file size - 5MB maximum
            $maxsize = 5 * 1024 * 1024;
            if($filesize > $maxsize) 
                generateJavaScriptAlert("Error: File size is larger than the allowed limit.");
        
            else{
                // Verify MYME type of the file
                if(in_array($filetype, $allowed)){
                    $newFileName = $_SESSION["username"];
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
                    if(file_exists($imagePath)){
                        echo $filename . " is already exists.";  
                        chmod($imagePath,0755);
                        unlink($imagePath);
                    }
                    move_uploaded_file($_FILES["uploadedPicture"]["tmp_name"], $imagePath);
                    $insertpath_sql = "UPDATE user SET profile_image_path = '$imagePath' WHERE username = '".$_SESSION['username']."'";
                    mysqli_query($connect,$insertpath_sql); 
                    header("Location: " . $_SERVER['PHP_SELF']);
                }
                else{
                    generateJavaScriptAlert("Invalid FileType. Please try again."); 
                }
            }
        }
    }

    
    if($_SESSION['usertype'] == "Student"){
        $profile_sql = "SELECT * FROM user as u 
                        JOIN student as s ON s.username = u.username
                        WHERE u.username = '".$_SESSION['username']."'";
    }

    else if($_SESSION['usertype'] == "Instructor"){
        $profile_sql = "SELECT * FROM user as u 
                        JOIN instructor as i ON i.username = u.username
                        WHERE u.username = '".$_SESSION['username']."'";
    }

    else if($_SESSION['usertype'] == "Provider"){
        $profile_sql = "SELECT * FROM user as u 
                        JOIN training_provider as tp ON tp.username = u.username
                        WHERE u.username = '".$_SESSION['username']."'";
    }
    else if($_SESSION['usertype'] == "Admin") {
        $profile_sql = "SELECT * FROM user WHERE username = '".$_SESSION['username']."'";
    }


    $profile = mysqli_fetch_assoc(mysqli_query($connect, $profile_sql)); 

    echo<<<HTML
    <div class = "container">
        <div class = "left-side">
            <form id="changeProfilePictureForm" method="POST" action="" enctype="multipart/form-data">
                <label for="uploadPicture">
    HTML;
                    if ($profile['profile_image_path'] == null) {
                        echo "<img src='../files/defaultProfileImage.jpg' alt='Profile Image'>";
                    }
                    else {
                        echo "<img src='".$profile['profile_image_path']."' alt='Profile Image'/>";
                    }
    echo<<<HTML
                    <div class="image_overlay image_overlay_blur">
                        <h3>Change</h3>
                        <p>Profile Picture</p>
                    </div>
                </label>
                <input type="file" id="uploadPicture" name="uploadedPicture" style="display: none;"/>
            </form>

        </div>

        <div class = "right-side">

            <div id="displayDIV" style = "display:block;">

                <h1>
    HTML;
                    if($_SESSION['usertype'] == "Student" || $_SESSION['usertype'] == "Instructor"){
                        echo $profile["first_name"]." ".$profile["last_name"];
                    }
                    else if($_SESSION['usertype'] == "Provider"){
                        echo $profile["provider_name"];
                    }
                    else if ($_SESSION['usertype'] == "Admin"){
                        echo $profile["username"];
                    }
    echo<<<HTML
                </h1>
    HTML;
                    if ($_SESSION['usertype'] != "Admin") {
                        if($_SESSION['usertype'] == "Student" || $_SESSION['usertype'] == "Instructor"){
                            generateHTMLDetail("Training Provider",$profile["provider_username"]);
                        }

                        generateHTMLDetail("Join on",$profile["joined_date"]);
                        generateHTMLDetail("Contact",$profile["contact_number"]);
                        generateHTMLDetail("Email",$profile["email"]);

                        if($_SESSION['usertype'] == "Student"){
                            generateHTMLDetail("Date Of Birth",$profile["date_of_birth"]);
                            generateHTMLDetail("Academic Program",$profile["academic_program"]);
                        }
                    }
                    if ($_SESSION['usertype'] != "Admin") {
                        echo "<button id='startEditButton'>Edit Profile</button>";
                    }
    echo<<<HTML
                <button id="changePasswordButton">Change Password</button>
            </div>

            <div id="editDIV" style="display:none;">
                <form id="editProfileForm" method="POST" action="">
                    <h1>
                        Edit Profile
                    </h1>

    HTML;
                        if($_SESSION['usertype'] == "Student" || $_SESSION['usertype'] == "Instructor"){
                            generateHTMLEditProfile("First Name","firstName",$profile["first_name"]);
                            generateHTMLEditProfile("Last Name","lastName",$profile["last_name"]);
                        }
                        else if($_SESSION['usertype'] == "Provider"){
                            generateHTMLEditProfile("Provider Name","providerName",$profile["provider_name"]);
                        }

                        generateHTMLEditProfile("Contact","contactNumber",$profile["contact_number"],"tel");
                        generateHTMLEditProfile("Email","emailAddress",$profile["email"],"email");

                        if($_SESSION['usertype'] == "Student"){
                            generateHTMLEditProfile("Date Of Birth","dateOfBirth",$profile["date_of_birth"],"date");
                        }
    echo<<<HTML

                    <button type="button" id = "cancelEdit" style="background-color:red;">Cancel</button>
                    <button type="submit" id = "submitEdit" name = "submitEdit" style="background-color:#3498db;">Edit</button>

                </form>
            </div>


            <div id="newPasswordDIV" style="display:none;">
                <form id="newPasswordForm" method="POST" action="">
                    <h1>Change to New Password</h1>
    HTML;
                        
                        generateHTMLChangePassword("Old Password","oldPassword");
                        generateHTMLChangePassword("New Password","newPassword");
                        generateHTMLChangePassword("Re-enter New Password","newPasswordConfirm");
    echo<<<HTML
                    <button type="button" id = "cancelChangePWD" style="background-color:red;">Cancel</button>
                    <button type="submit" id = "submitChangePWD" name = "submitChangePWD" style="background-color:#3498db;">Edit</button>

                </form>
            </div>
        </div>
    </div>
    HTML;
}

function returnOption($data,$value){
    if($data==$value) {
        return "<option value='".$value."' selected>".$value."</option> ";
    }
    else{
        return "<option value='".$value."' >".$value."</option> ";
    }
}

function createCourseDetailPage(){
    global $connect;
    $course_section_id = $_GET['section'];
    $course_id = $_GET['course'];
    $course_sql = "SELECT * FROM course AS c
                    JOIN course_section AS cs ON cs.course_section_id = $course_section_id
                    WHERE c.course_id = $course_id";
    $course = mysqli_fetch_assoc(mysqli_query($connect,$course_sql));


    if ($_SESSION["usertype"]  == "Student"){
        $check_course_complete_sql = "SELECT * FROM course_student WHERE username = '".$_SESSION['username']."' AND course_section_id = '".$course_section_id."'";
        $check_course_complete = mysqli_fetch_assoc(mysqli_query($connect,$check_course_complete_sql));
    }
    
    if(isset($_POST['makeAnnouncement'])){
        $datetime = new Datetime();
        $formattedDateTime = $datetime->format('Y-m-d H:i:s');

        $title = $_POST['title'];
        $content = $_POST['content'];
        $username = $_SESSION['username'];
        $sql = "INSERT INTO announcement (course_section_id,username,title,content,upload_date_time) VALUES ('$course_section_id','$username','$title','$content','$formattedDateTime')";
        mysqli_query($connect,$sql);
        $_POST = array();
        header("Refresh:0");
        exit;
    }
    else if (isset($_POST['submitEditSectionForm'])){
        $edited_course_section_name = $_POST['edited_course_section_name'];
        $edited_description = $_POST['edited_description'];
        $edited_day = $_POST['edited_day'];
        $edited_start_time = $_POST['edited_start_time'];
        $edited_end_time = $_POST['edited_end_time'];
        $edited_max_student_num = $_POST['edited_max_student_num'];
        $edited_status = $_POST['edited_status'];

        $sql = "UPDATE course_section 
                SET course_section_name = '$edited_course_section_name', description = '$edited_description',
                    day = '$edited_day', start_time = '$edited_start_time', end_time = '$edited_end_time',
                    max_student_num = '$edited_max_student_num', status = '$edited_status' 
                WHERE course_section_id = $course_section_id";
        mysqli_query($connect,$sql);
        generateJavaScriptAlert("Successfully Edited Section Information");
        $_POST = array();
        header("Refresh:0");
        exit;
    }
    else if(isset($_POST['submitCourseFeedbackForm'])){
        $courseRating = $_POST['courseRating'];
        $courseFeedback = $_POST['courseFeedback'];
        $insert_sql = "INSERT INTO course_feedback (course_section_id,username,feedback,rating,date)
                        VALUE ('$course_section_id', '".$_SESSION["username"]."','$courseFeedback','$courseRating',CURDATE())";
        mysqli_query($connect,$insert_sql);
        generateJavaScriptAlert("Successfully Made Feedback to Course");
        $_POST = array();
        header("Refresh:0");
        exit;
    }
    else if(isset($_POST['submitInstructorFeedbackForm'])){
        $instructor_username = $course['username'];
        $instructorRating = $_POST['instructorRating'];
        $instructorFeedback = $_POST['instructorFeedback'];
        $insert_sql = "INSERT INTO instructor_feedback (course_section_id,instructor_username,student_username,feedback,rating,date)
                        VALUE ('$course_section_id','$instructor_username', '".$_SESSION["username"]."','$instructorFeedback','$instructorRating',CURDATE())";
        mysqli_query($connect,$insert_sql);
        generateJavaScriptAlert("Successfully Made Feedback to Instructor");
        $_POST = array();
        header("Refresh:0");
        exit;
    }
    else if(isset($_POST['submitStudentCompleteCourse'])){
        $update_sql = "UPDATE course_student SET course_completed = '1', course_completed_date='".date("Y-m-d")."' WHERE username = '".$_POST['submitStudentCompleteCourse']."' AND course_section_id = $course_section_id";
        mysqli_query($connect,$update_sql);
        generateJavaScriptAlert("Successfully Updated");
        $_POST = array();
        header("Refresh:0");
        exit;
    }

    echo<<<HTML
        <div class="banner">
        <div class="left-panel">
            <div class="image-container">
    HTML;
                if($course['course_image_path'] == null){
                    echo<<<HTML
                        <img src="../files/tpms.png" alt="Course img"/>
                    HTML;
                }
                else{
                echo<<<HTML
                    <img src="{$course['course_image_path']}" alt="Course img"/>
                HTML;
                }
    echo<<<HTML
            </div>

            <div class="left-right-panel">
                <h1>
                    {$course['course_title']} ({$course['course_section_name']})
                </h1>
                <p>
                    {$course['start_date']} - {$course['end_date']}
                </p>
            </div>
        </div>

        <div class="right-panel">
            <div>
                <button id="toggleDetail" style = "display:block;">Course Description</button>
            </div>
    HTML;
                    if ($_SESSION["usertype"]  == "Instructor"){
    echo<<<HTML
                    <div>
                        <button type='submit' id="toggleStudentList" style = "display:block;"> Student List</button>
                    </div>
                    <div>
                        <button type='submit' id="toggleUpdateSection" style = "display:block;"> Update Section</button>
                    </div>
    HTML;
                    }
                    if($_SESSION["usertype"] == "Student"){
                        if($check_course_complete["course_completed"]){
                            echo<<<HTML
                                <div>
                                    <button type='submit' id="toggleFeedback" style = "display:block;">Feedback</button>
                                </div>
                                <div>
                                    <button type='submit' id="toggleCertificate" style = "display:block;">Certificate</button>
                                </div>
                            HTML;
                        }
                    }

                    
    echo<<<HTML
            </div>
        </div>

        <!-- Course Description (in hidden form trigger when user click on button) -->
        <div id = "hiddenDetail" class=""  style="display: none;">
            <h3>Course Description</h3>
            <p>{$course["course_description"]}</p> 
            <h3>Section Description</h3>
            <p>{$course["description"]}</p>
        </div>

    HTML;
        // Two button section to view student list and update course for instructor only
        if ($_SESSION["usertype"]  == "Instructor"){
            echo<<<HTML
                <div id="hiddenStudentList" style="display:none;">
                    <h1> Students <h1>
                HTML;
                        $student_list_sql = "   SELECT * FROM course_student as a 
                                                JOIN student as s ON a.username = s.username 
                                                JOIN user as u ON a.username = u.username 
                                                WHERE course_section_id = $course_section_id 
                                                ORDER BY a.course_completed,s.last_name, s.first_name";
                        $student_list = mysqli_query($connect,$student_list_sql);
                        $student_count = mysqli_num_rows($student_list);

                        if($student_count == 0){
                echo<<<HTML
                            <p>No student found</p>
                HTML;
                        }
                        else {
                            while($each_student = mysqli_fetch_array($student_list)){
                echo<<<HTML
                        <div class="each-student">
                            <div id="leftOnEachStudent">
                HTML;
                                    if ($each_student['profile_image_path'] == null) {
                                        echo "<img src='../files/defaultProfileImage.jpg' alt='Profile Image'>";
                                    }
                                    else {
                echo<<<HTML
                                        <img src="{$each_student['profile_image_path']}" alt="{$each_student['first_name']} {$each_student['last_name']}"/>       
                HTML;
                                    }
                echo<<<HTML
                                <p>
                                    {$each_student['first_name']} {$each_student['last_name']}
                                </p>
                            </div>
                HTML;
                            if($each_student['course_completed']==1){
                
                    echo<<<HTML
                                <div id="rightOnEachStudent">
                                    <p> Completed</p>
                                </div>
                    HTML;
                            }
                            else{
                    echo<<<HTML
                                <div id="rightOnEachStudent">
                                    <form method="post" action="">
                                        <button type="submit" name="submitStudentCompleteCourse" value="{$each_student['username']}">Complete</button>
                                    </form>
                                </div>
                    HTML;
                                }
                    echo<<<HTML
                        </div>
                    HTML;
                            }
                        }
            echo<<<HTML
                </div>

            HTML;
            echo<<<HTML

            <div id="hiddenUpdateSection" style="display:none;">
                <h1> Edit Section Information </h1>
                    <form name= "editSectionForm" method="post" action="">
            HTML;
                    $course_section_detail_sql = "SELECT * FROM course_section
                                                WHERE course_section_id = $course_section_id"; 
                    $course_section_detail = mysqli_fetch_assoc(mysqli_query($connect, $course_section_detail_sql));

                        generateHTMLEditCourseSection("Name","edited_course_section_name",$course_section_detail["course_section_name"]);
                        generateHTMLEditCourseSection("Description","edited_description",$course_section_detail["description"]);
                        generateOptionHTMLEditCourseSection("Day","edited_day",
                                            returnOption($course_section_detail["day"],'Monday')
                                            .returnOption($course_section_detail["day"],'Tuesday')
                                            .returnOption($course_section_detail["day"],'Wednesday')
                                            .returnOption($course_section_detail["day"],'Thursday')
                                            .returnOption($course_section_detail["day"],'Friday')
                                            .returnOption($course_section_detail["day"],'Saturday')
                                            .returnOption($course_section_detail["day"],'Sunday'));
                        generateHTMLEditCourseSection("Start Time","edited_start_time",$course_section_detail["start_time"],"time");
                        generateHTMLEditCourseSection("End Time","edited_end_time",$course_section_detail["end_time"],"time");
                        generateHTMLEditCourseSection("Maximum Student Number","edited_max_student_num",$course_section_detail["max_student_num"],"number",'min = "1"');
                        $options = returnOption($course_section_detail["status"],'Open').returnOption($course_section_detail["status"],'Closed');
                        generateOptionHTMLEditCourseSection("Section Status","edited_status",$options);
            echo<<<HTML
                        <input type="submit" name="submitEditSectionForm"/>
                    </form>
            </div>
            HTML;
        }

        if($_SESSION["usertype"] == "Student"){
            if($check_course_complete["course_completed"]){
                $course_feedback_sql = "SELECT * FROM course_feedback 
                                WHERE username = '".$_SESSION["username"]."'
                                AND course_section_id = $course_section_id";
                $course_feedback_all = mysqli_query($connect,$course_feedback_sql);
                $course_feedback_count = mysqli_num_rows($course_feedback_all);
                $course_feedback = mysqli_fetch_assoc($course_feedback_all);
                $student_detail_sql = "SELECT * FROM student AS s
                                                WHERE username = '".$_SESSION['username']."'";
                $student_detail = mysqli_fetch_assoc(mysqli_query($connect,$student_detail_sql));
                echo<<<HTML
                    <div id="hiddenFeedback" style="display:none;">
                        <div id="allFeedback">
                        <div id="courseFeedback">
                        <h1>Course Feedback </h1>
                HTML;
                        if($course_feedback_count == 0){
                            // no course feedback made yet
                            generateFeedbackForm('courseFeedbackForm','Rating','courseRating','Feedback','courseFeedback','submitCourseFeedbackForm');
                            }
                        else {
                            generateHTMLStarDetail($course_feedback['rating']);
                            generateHTMLFeedbackDetail($course_feedback['feedback']);
                            }
                echo<<<HTML
                        </div>
                        <div id="instructorFeedback">
                        <h1>Instructor Feedback </h1>
                HTML;
                        $instructor_feedback_sql = "SELECT * FROM instructor_feedback 
                                                WHERE student_username = '".$_SESSION["username"]."' 
                                                AND course_section_id = $course_section_id";
                        $instructor_feedback_all = mysqli_query($connect,$instructor_feedback_sql);
                        $instructor_feedback_count = mysqli_num_rows($instructor_feedback_all);
                        $instructor_feedback = mysqli_fetch_assoc($instructor_feedback_all);
                        // no instructor feedback made yet
                        if($instructor_feedback_count == 0){
                            generateFeedbackForm('instructorFeedbackForm','Rating','instructorRating','Feedback','instructorFeedback','submitInstructorFeedbackForm');
                        }
                        else {
                            generateHTMLStarDetail($instructor_feedback['rating']);
                            generateHTMLFeedbackDetail($instructor_feedback['feedback']);
                            }
                echo<<<HTML
                    </div>
                        </div>
                    </div>

                <div id="hiddenCertificate" style="display:none;">
                    <h1> Certificate </h1>
                    <div id="certificateContent">
                        <div class="divShowingCertificate">
                HTML;
                            generateCertificate($student_detail['first_name']." ".$student_detail['last_name'],$course['course_title'],$check_course_complete['course_completed_date']); 
                echo<<<HTML
                        </div>
                        <button type="button" id="printCertificate">Print Certificate</button>
                    </div>
                    
                </div>
                HTML;
            }
        }
        echo<<<HTML


        <div class="announcement" style="display: block;"  id="hiddenAnnouncement">
        <h1> Announcements </h1>

        <div id ="new-announcement-container">
    HTML;
            if($_SESSION["usertype"] == "Instructor"){
    echo<<<HTML
            <form id = "newAnnouncementForm" method="POST" action="">
                <input id="newAnnouncementInput" placeholder="New announcement Title" name= "title" required> </input>
                <textarea id="contentTextArea" class="hiddenAttributeNewAnnouncement" style="display: none;" oninput="autoExpand(this)" name="content" placeholder="New announcement Content" required></textarea>
                <div class="hiddenAttributeNewAnnouncement" style="display: none;" >
                    <div id="hidden-right-left">
                        <button type="button" id =  "cancelAnnouncement">Cancel</button>
                        <button type="submit" name = "makeAnnouncement">Submit</button>
                    </div>  
                </div>
            </form>

        </div>
    HTML;
            }
            $announcement_sql = "SELECT * FROM announcement WHERE course_section_id = {$course_section_id} ORDER BY upload_date_time DESC";
            $announcement_result = mysqli_query($connect, $announcement_sql);	
            $count = mysqli_num_rows($announcement_result);

            if($count == 0){
    echo<<<HTML
            <p> No announcement found </p>
    HTML;
            }

            else{
                while($row = mysqli_fetch_assoc($announcement_result)){
                        $profile_image_sql = "SELECT * FROM user WHERE username='".$row['username']."'";
                        $profile_image = mysqli_fetch_assoc(mysqli_query($connect,$profile_image_sql));
                        $author_name_sql = "SELECT * FROM instructor WHERE username='".$row['username']."'";
                        $author_name = mysqli_fetch_assoc(mysqli_query($connect,$author_name_sql));
    echo<<<HTML
            <div class="each-announcement">
                <div class="upper">
                    <div class="upper-left">
    HTML;
                    if ($profile_image['profile_image_path'] == null) {
                        echo "<img src='../files/defaultProfileImage.jpg' alt='Profile Image'>";
                    }
                    else {
    echo<<<HTML
                        <img src="{$profile_image['profile_image_path']}" alt="{$author_name['first_name']} {$author_name['last_name']}">
    HTML;
                    }
    echo<<<HTML
                        <h3>
                            {$author_name['first_name']} {$author_name['last_name']}
                        </h3>
                    </div>
                    <h3>
                        {$row['upload_date_time']}
                    </h3>

                </div>

                <div class="bottom">
                    <h3>
                        {$row['title']} 
                    </h3>
                    <p>
    HTML;
                        echo nl2br($row['content']);
    echo<<<HTML
                    </p>
                </div>
            </div>

    HTML;
                }
            }
    echo<<<HTML
        </div>
    HTML;
}

function createRegisterCoursePage(){
    global $connect;
    $course_id = $_GET['course'];
    $course_sql = "SELECT * FROM course WHERE course_id = $course_id";
    $course = mysqli_fetch_assoc(mysqli_query($connect,$course_sql));

    $unique_instructor_sql = "SELECT DISTINCT (cs.username) 
                            FROM course_section AS cs
                            where cs.course_id = $course_id";
    $unique_instructor = mysqli_query($connect,$unique_instructor_sql); 

    echo<<<HTML
    <div class="banner">
        <div class="left-panel">
            <div class="image-container">
    HTML;
                if($course['course_image_path'] == null){
                    echo<<<HTML
                        <img src="../files/tpms.png" alt="Course img"/>
                    HTML;
                }
                else{
    echo<<<HTML
                <img src="{$course['course_image_path']}" alt="Course img"/>
    HTML;
                }
    echo<<<HTML
            </div>

            <div class="left-right-panel">
                <h1>
                    {$course['course_title']}
                </h1>
                <p>
                    {$course["start_date"]} - {$course["end_date"]}
                </p>
            </div>
        </div>

        <div class="right-panel">
            <form action="enrollment.php" method="post" >
                <button type='submit' name = "Enroll" value="{$course_id}">Enroll</button>
            </form>
        </div>
    </div>

    <div class='courseIntroSection'>
        <h2>Course Introduction</h2>
        <p>
            {$course['course_description']}
        </p>
    </div>


    <div class= "instructor-list">
    <h2>Course Instructor</h2>
    HTML;
            while($unique = mysqli_fetch_assoc($unique_instructor)){
                $section_sql = "SELECT *
                                FROM course_section AS cs
                                JOIN instructor AS i ON i.username = cs.username
                                JOIN user AS u ON u.username = cs.username
                                where cs.username = '".$unique['username']."'";
                $section = mysqli_fetch_assoc(mysqli_query($connect,$section_sql)); 
    echo<<<HTML
            <div class = "instructor">
    HTML;
                    if($section['profile_image_path'] == null){
                        echo<<<HTML
                            <img src="../files/defaultProfileImage.jpg" alt="{$section['first_name']} {$section['last_name']}"/>
                        HTML;
                    }
                    else{
                    echo<<<HTML
                        <img src="{$section['profile_image_path']}" alt = "{$section['first_name']} {$section['last_name']}">
                    HTML;
                    }
    echo<<<HTML
                    <p>
                        {$section["first_name"]} {$section["last_name"]}
                    </p>
            </div>
    HTML;
            }
    echo<<<HTML
    </div>
    HTML;
}

function createEnrollmentPage(){
    global $connect;
    if(isset($_POST['confirmation'])){
        $course_section_id = $_POST["confirmation"];
        $insert_sql = "INSERT INTO course_student (course_section_id, username) VALUES ('$course_section_id', '".$_SESSION["username"]."');";
        $output = mysqli_query($connect,$insert_sql);
        if($output){
            $count_all_student_in_section_sql = "SELECT * FROM course_student WHERE course_section_id = $course_section_id";
            $all_student_in_section = mysqli_query($connect,$count_all_student_in_section_sql);
            $count_all_student_in_section = mysqli_num_rows($all_student_in_section);

            $max_student_sql = "SELECT max_student_num FROM course_section WHERE course_section_id = $course_section_id";
            $max_student = mysqli_fetch_assoc(mysqli_query($connect,$max_student_sql));
            if($count_all_student_in_section>=$max_student['max_student_num']){
                $update_sql = "UPDATE course_section SET status = 'Closed' WHERE course_section_id = $course_section_id";
                mysqli_query($connect,$update_sql);
                generateJavaScriptAlert("Successfully Enrolled");
            }
            header("Location: dashboard.php");
        }
    }

    if(isset($_POST["Enroll"])){
        $course_id = $_POST['Enroll'];
        $course_sql = "SELECT * FROM course WHERE course_id = $course_id";
        $course = mysqli_fetch_assoc(mysqli_query($connect,$course_sql));

        $course_section_sql = "SELECT * FROM course_section AS cs
                                JOIN instructor AS i ON i.username = cs.username
                                JOIN user AS u ON u.username = cs.username
                                where cs.course_id = $course_id";
        $course_section = mysqli_query($connect,$course_section_sql); 
    
        echo<<<HTML

            
        <div class="banner">
            <div class="left-panel">
                <div class="image-container">

        HTML;
                    if($course['course_image_path'] == null){
                        echo<<<HTML
                            <img src="../files/tpms.png" alt="Course img"/>
                        HTML;
                    }
                    else{
                    echo<<<HTML
                        <img src="{$course['course_image_path']}" alt="Course img"/>
                    HTML;
                    }
        echo<<<HTML
                </div>

                <div class="left-right-panel">
                    <h1>
                        {$course['course_title']}
                    </h1>
                    <p>
                        {$course["start_date"]} - {$course["end_date"]}
                    </p>
                </div>
            </div>

        </div>

        <div clsss="sectionDetails">
            <h1 id="specialH1">
                Course Section
            </h1>
            <div id="allInstructors">
        HTML;
                while($each_section = mysqli_fetch_assoc($course_section)){
        echo<<<HTML
            <div class="instructor-list">
        HTML;
                    if ($each_section['profile_image_path'] == null) {
                        echo "<img src='../files/defaultProfileImage.jpg' alt='Profile Image'>";
                    }
                    else {
                echo<<<HTML
                        <img src="{$each_section['profile_image_path']}" alt = "{$each_section['first_name']} {$each_section['last_name']}">
                HTML;
                    }
        echo<<<HTML
                <h3 id="instructorName">
                    {$each_section['first_name']} {$each_section['last_name']}
                </h3>

                <h3>   
                    {$each_section['course_section_name']}
                </h3>
                <h3>   
                    {$each_section['day']}
                </h3>
                <h3>   
                    {$each_section['start_time']} - {$each_section['end_time']}
                </h3>
        HTML;
                    if($each_section["status"] == "Open"){
        echo<<<HTML
                    <button open-modal="{$each_section['course_section_id']}">Enroll</button>

                    <dialog dialog-modal id="dialog-box" class="dialog-container dialog-{$each_section['course_section_id']}">
                        <form method="post" action="">
                            <h2>Enrollment Confirmation</h2>
                            <p>
                                <span class="title">Course</span>
                                <span class="colon">:</span>
                                {$course['course_title']}
                            </p>

                            <p>
                                <span class="title">Section</span>
                                <span class="colon">:</span>
                                {$each_section['course_section_name']}
                            </p>

                            <p>
                                <span class="title">Lecturer</span>
                                <span class="colon">:</span>
                                {$each_section['first_name']} {$each_section['last_name']}
                            </p>

                            <p>
                                <span class="title">Duration</span>
                                <span class="colon">:</span>
                                {$course['start_date']} - {$course['end_date']}
                            </p>

                            <p>
                                <span class="title">Day</span>
                                <span class="colon">:</span>
                                {$each_section['day']}
                            </p>

                            <p>
                                <span class="title">Time</span>
                                <span class="colon">:</span>
                                {$each_section['start_time']} - {$each_section['end_time']}
                            </p>

                            <br>

                            <p>
                                Do you really want to enroll this course?
                            </p>
                            <br>

                            <button type="submit" formmethod="dialog">Cancel </button>
                            <button type="submit" method = "post" name='confirmation' value= '{$each_section["course_section_id"]}'>Confirm</button>
                        </form>
                    </dialog>
        HTML;
                    }
                    if($each_section["status"] == "Closed"){
        echo<<<HTML
                    <h3> Closed </h3>
        HTML;
                    }

        echo<<<HTML
            </div>                        
        HTML;
        }

        echo<<<HTML
        </div>
        </div>            
        HTML;
    }

}

function generatePage($title,$function,$anyCodeOnHead="",$anyCodeInsideBody=""){
    session_start();
    echo <<<HTML
        <!DOCTYPE html>
        <html>
        <head> 
        <title>$title</title>
        <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src ="../js/navbar.js"></script>
        $anyCodeOnHead
        </head>
        <body>
        <div class="Container">
            <div class="sidebar">
    HTML;
                include '../navBar/navBar.php';
    echo <<<HTML
            </div>
            <div class="content" id="content"> 
    HTML;
                $function();

    echo <<<HTML
            </div>
        </div>
        $anyCodeInsideBody
        </body>
        </html>
    HTML;
}