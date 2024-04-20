<?php include '../db_connect.php';
    session_start();
    if(isset($_SESSION['usertype']) != "Admin" or isset($_SESSION['usertype']) != "Provider") {
        header("Location: ../login.php");
    }
    
    // Validate Create Account Form and Edit Account Form
    if(isset($_POST['hiddenCreateAccount'])) { 
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['hiddenCreateAccount'] == "true") {
            $allowSubmission=true;

            if(empty($_POST["username"])) {
                $allowSubmission=false;
            }

            $sql = "SELECT * FROM user where username='" . $_POST["username"] . "';";
            $result = mysqli_query($connect,$sql);
            $count = mysqli_num_rows($result);
            if($count > 0) {
                $allowSubmission=false;
                echo '<script>alert("username taken, try again with another username")</script>';
            }

            if(strlen($_POST["password"]) < 8) {
                $allowSubmission=false;
            }
            if (!preg_match('/[A-Za-z]/', $_POST["password"]) || !preg_match('/[0-9]/', $_POST["password"])) {
                $allowSubmission=false;
            }
            if(empty($_POST["usertype"])) {
                $allowSubmission=false;
            }

            $contactNumber = NULL;
            $email = NULL;            
            if($_POST["usertype"] == "Provider") {
                if(empty($_POST["providerName"])) 
                    $allowSubmission=false;
                if(!empty($_POST["contactNumber"])) {
                $contactNumber = $_POST["contactNumber"];
                }
                if(strlen($_POST['email']) > 0) {
                    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
                        $allowSubmission=false;
                    }
                    else {
                        $email = $_POST["email"];
                    } 
                }            
            }
            if($_POST["usertype"] == "Instructor" || $_POST["usertype"] == "Student") {
                if(empty($_POST["provider"])) // Check the provider username is empty
                    $allowSubmission=false;
                
                // Check if the provider username is valid
                $sql = "SELECT * FROM training_provider where username='" . $_POST["provider"] . "';";
                $result = mysqli_query($connect,$sql);
                $count = mysqli_num_rows($result);
                if($count == 0) {
                    $allowSubmission=false;
                }
                if(empty($_POST["firstName"])) 
                    $allowSubmission=false;
                if(empty($_POST["lastName"])) 
                    $allowSubmission=false;
            }

            if($_POST["usertype"] == "Student") {
                if(empty($_POST["dateOfBirth"])) 
                    $allowSubmission=false;
                if(empty($_POST["academicProgram"])) 
                    $allowSubmission=false;
            }
                

            if($allowSubmission){            
                //print_r($_POST);
                $username = $_POST["username"];
                $password = $_POST["password"];
                $usertype = $_POST["usertype"];

                $hashed_password = password_hash($password,PASSWORD_DEFAULT);
                //echo $hashed_password;

                $sql = "INSERT INTO user (username,password_hash,usertype) values ('$username','$hashed_password','$usertype')";
                $abc = mysqli_query($connect,$sql);
                if(!$abc) {
                    echo mysqli_error($connect);
                    die();
                }
                
                if ($usertype != "Admin") {
                    if ($usertype == "Provider") {
                            $providerName = $_POST["providerName"];
                            $sql2 = "INSERT INTO training_provider(username,provider_name,contact_number,email) values ('$username','$providerName','$contactNumber','$email');";
                    } 
                    else if ($usertype == "Student") {
                        $firstName = $_POST["firstName"];
                        $lastName = $_POST["lastName"];
                        $dateOfBirth = $_POST["dateOfBirth"];
                        $academicProgram = $_POST["academicProgram"];
                        $providerUsername = $_POST["provider"];
                        $contactNumber = $_POST["contactNumber"];
                        $email = $_POST["email"];
                        $sql2 = "INSERT INTO student(username,first_name,last_name,date_of_birth,academic_program,provider_username,contact_number,email) values ('$username','$firstName','$lastName','$dateOfBirth','$academicProgram','$providerUsername','$contactNumber','$email');";
                    }
                    else if ($usertype == "Instructor") {
                        $firstName = $_POST["firstName"];
                        $lastName = $_POST["lastName"];
                        $providerUsername = $_POST["provider"];
                        $contactNumber = $_POST["contactNumber"];
                        $email = $_POST["email"];
                        $sql2 = "INSERT INTO instructor(username,first_name,last_name,provider_username,contact_number,email) values ('$username','$firstName','$lastName','$providerUsername','$contactNumber','$email');";
                    }

                
                    $abc2 = mysqli_query($connect,$sql2);

                    if(!$abc2) {
                        echo mysqli_error($connect);
                        die();
                    }
                    
                }
                
                
                ?>

                    <script type="text/javascript">
                        alert("Successful created a new account");
                    </script>
            <?php
            }
        }
    }
    
    if(isset($_POST['hiddenEditAccount']) && $_POST['hiddenEditAccount'] == "true") {
        $username = $_GET['username'];
        $sql = "SELECT * FROM user where username='$username';";
        $result = mysqli_query($connect,$sql);
        $row = mysqli_fetch_assoc($result);
        $usertype = $row['usertype'];
        // User change password
        if(!empty($_POST["newPassword"])) {
            if(strlen($_POST["newPassword"]) < 8) {
                $allowSubmission=false;
            }
            if (!preg_match('/[A-Za-z]/', $_POST["newPassword"]) || !preg_match('/[0-9]/', $_POST["newPassword"])) {
                $allowSubmission=false;
            }
            
            // Update password in the database
            $password = $_POST["newPassword"];
            $hashed_password = password_hash($password,PASSWORD_DEFAULT);
            $sql = "UPDATE user SET password_hash = '" . $hashed_password . "' where username = " . "'" . $username . "';";
            $result = mysqli_query($connect,$sql);
            if (!$result) {
                echo "Error: " . mysqli_error($connect);
            }
        }
        
    
        // Admin can only edit password
    
    
        if($usertype == 'Instructor' || $usertype == 'Student' || $usertype == 'Provider') {
            $contactNumber = $_POST["contactNumber"];
            $email = $_POST["email"];
        }
    
        if($usertype == 'Instructor') {
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            
            $sql2 = "UPDATE instructor SET first_name = '" . $firstName . "', last_name = '" . $lastName 
                    . "', contact_number = '" . $contactNumber . "', email ='" . $email 
                    . "' WHERE username = '" . $username . "';";
       
            $result = mysqli_query($connect,$sql2);
        }
        else if($usertype == 'Student') {
            $firstName = $_POST["firstName"];
            $lastName = $_POST["lastName"];
            $dateOfBirth = $_POST["dateOfBirth"];
            $academicProgram = $_POST["academicProgram"];
            $sql2 = "UPDATE student SET first_name = '" . $firstName . "', last_name = '" . $lastName 
                    . "', date_of_birth = '" . $dateOfBirth ."', academic_program = '". $academicProgram 
                    . "',contact_number = '" . $contactNumber . "', email ='" . $email 
                    . "' WHERE username = '" . $username . "';";
            $result = mysqli_query($connect,$sql2);
        }
        else if($usertype == 'Provider') {
            $providerName = $_POST['providerName'];
            $sql2 = "UPDATE training_provider SET provider_name = '" . $providerName . "', contact_number ='" 
                    . $contactNumber . "', email='" . $email 
                    . "' WHERE username='" . $username ."';";
            $result = mysqli_query($connect,$sql2);
        }
        ?>
    
        <script>
            alert("User <?php echo $username.' Updated!';?>");
        </script>
    
        <?php
        header( "refresh:0.5; url=dashboard.php" );
    }
?>

<!DOCTYPE html>
<html>
<head> 
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
    <link rel="stylesheet" href="dashboard.css"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src ="../js/navbar.js"></script></head>

<body>
<div class="Container">
    <div class="sidebar">
        <?php include '../navBar/navBar.php'?>
    </div>
    <div class="content" id="content">    
        <header>
            <?php if ($_SESSION["usertype"] == "Admin") {
                echo "<h1>Admin Dashboard</h1>";
            } 
            else if ($_SESSION["usertype"] == "Provider") {
                echo "<h1>Manage Accounts</h1>";
            }
            ?>
            <button create-account-button>Add Account</button>
        </header>

        <!-- Create Account Dialog -->
        <dialog create-account>
            <form action="" method="POST" onsubmit="return mySubmitFunction(event)" id="form">
                <h3>Create New Account</h3>
                <div class="input-box-user">
                    <img src="../files/name.png" alt="UserIcon">
                    <a href="#" title="Username has to be less than 20 character" style='cursor:default;'><img src="../files/questionMark.png" class="question-mark" alt="&quest;"></a>
                    <input type="text" id="username" name="username" placeholder="Username" maxlength="20">
                    <div class="errorMessageBox" id="username-messageBox"></div><br>

                </div>
                <div class="input-box-pw">
                    <img src="../files/password_icon.png" alt="PwIcon">
                    <a href="#" title="Password should be more than 8 characters with letters and numbers" style='cursor:default;'><img src="../files/questionMark.png" class="question-mark" alt="&quest;"></a>
                    <input type="password" id="password" name="password" placeholder="Password">
                    <div class="errorMessageBox" id="password-messageBox"></div><br>
                </div>  
                <div class="input-box">
                    <img src="../files/user.png" alt="UserIcon">
                    <select name="usertype" id="usertype" onchange="updateForm()">
                        <option hidden disabled selected value>Select a usertype</option>
                        ?> 
                        <?php if ($_SESSION['usertype'] == 'Admin') {
                            echo '<option value="Admin">Admin</option>';
                            echo '<option value="Provider">Training Provider</option>';
                        }?>
                        <option value="Instructor">Instructor</option>
                        <option value="Student">Student</option>
                    </select>
                    <div class="errorMessageBox" id="usertype-messageBox"></div><br>
                </div>
                <div id="additionalFields"></div>
                <div  class="operations-button">
                    <input class="back" type="button" value = "Cancel" onclick="createAccountModal.close();">
                    <input class="submit" type="submit" name="createAccount" value="Create Account">
                    <input type="hidden" name="hiddenCreateAccount" id="createAccount" value="false">
                </div>
            </form>
        </dialog>

        <!-- Edit Account Dialog -->
        <?php if(isset($_GET['edit'])) {
            
            $username = $_GET['username'];

            // Check the usertype
            $sql = "SELECT * FROM user where username='$username';";
            $result = mysqli_query($connect,$sql);
            $row = mysqli_fetch_assoc($result);
            $usertype = $row['usertype'];
            ?>
            <dialog edit-account-modal>
                <form action='' method='POST' onsubmit="return mySubmitFunction(event)" id="resetPassword">
                    <h3>Edit Account Details</h3>
                    <?php
                    if($usertype == 'Admin') {
                        // Allow to change password only
                        echo '<input type="hidden" id="formType" value="Admin">';
                    }
                    else if($usertype == 'Instructor') {

                        // Allow to change password, firstname, lastname, contactnumber, email
                        $sql2 = "SELECT * FROM instructor where username='$username';";
                        $result2 = mysqli_query($connect,$sql2);
                        $row2 = mysqli_fetch_assoc($result2);
                        ?>
                        <div class="input-box">
                        <!-- <label>First Name</label> -->
                        <img src="../files/name.png" alt="UserIcon">
                        <input type="text" id="firstName" name="firstName" placeholder="First Name" value="<?php echo $row2['first_name']; ?>"><br>
                        <div class="errorMessageBox" id="firstName-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Last Name</label> -->
                        <img src="../files/name.png" alt="UserIcon">
                        <input type="text" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo $row2['last_name']; ?>"><br>
                        <div class="errorMessageBox" id="lastName-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Contact Number</label> -->
                        <img src="../files/call.png" alt="UserIcon">
                        <input type="tel" id="contactNumber" name="contactNumber" placeholder="Contact Number" value="<?php echo $row2['contact_number']; ?>"><br>
                        <div class="errorMessageBox" id="contactNumber-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Email</label> -->
                        <img src="../files/email.png" alt="UserIcon">
                        <input type="email" id="email" name="email" placeholder="Contact Email" value="<?php echo $row2['email']; ?>"><br>
                        <div class="errorMessageBox" id="email-messageBox"></div>
                        </div>

                        <!-- To let js know which type of user is being edited so it can validate input accordingly -->
                        <input type="hidden" id="formType" value="Instructor">
                        <?php
                    }
                    else if($usertype == 'Student') {
                        // Allow to change password, firstname, lastname, dataofbirth, academicprogram, contactnumber, email
                        $sql2 = "SELECT * FROM student where username='$username';";
                        $result2 = mysqli_query($connect,$sql2);
                        $row2 = mysqli_fetch_assoc($result2);
                        ?>
                        <div class="input-box">
                        <!-- <label>First Name</label> -->
                        <img src="../files/name.png" alt="UserIcon">
                        <input type="text" id="firstName" name="firstName" placeholder="First Name" value="<?php echo $row2['first_name']; ?>"><br>
                        <div class="errorMessageBox" id="firstName-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Last Name</label> -->
                        <img src="../files/name.png" alt="UserIcon">
                        <input type="text" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo $row2['last_name']; ?>"><br>
                        <div class="errorMessageBox" id="lastName-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Date Of Birth</label> -->
                        <img src="../files/calendar.png" alt="UserIcon">
                        <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo $row2['date_of_birth']; ?>"><br>
                        <div class="errorMessageBox" id="dateOfBirth-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Academic Program</label> -->
                        <img src="../files/course.png" alt="UserIcon">
                        <input type="text" id="academicProgram" name="academicProgram" placeholder="Academic Program" value="<?php echo $row2['academic_program']; ?>"><br>
                        <div class="errorMessageBox" id="academicProgram-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Contact Number</label> -->
                        <img src="../files/call.png" alt="UserIcon">
                        <input type="tel" id="contactNumber" name="contactNumber" placeholder="Contact Number" value="<?php echo $row2['contact_number']; ?>"><br>
                        <div class="errorMessageBox" id="contactNumber-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Email</label> -->
                        <img src="../files/email.png" alt="UserIcon">
                        <input type="email" id="email" name="email" placeholder="Contact Email" value="<?php echo $row2['email']; ?>"><br>
                        <div class="errorMessageBox" id="email-messageBox"></div>
                        </div>
                        <!-- To let js know which type of user is being edited so it can validate input accordingly -->
                        <input type="hidden" id="formType" value="Student">
                        <?php
                    }
                    else if($usertype == 'Provider') {
                        // Allow to change password, providername, contactnumber, email
                        $sql2 = "SELECT * FROM training_provider where username='$username';";
                        $result2 = mysqli_query($connect,$sql2);
                        $row2 = mysqli_fetch_assoc($result2);
                        ?>
                        
                        <div class="input-box">
                        <!-- <label>Provider Name</label> -->
                        <img src="../files/name.png" alt="UserIcon">
                        <input type="text" id="providerName" name="providerName" placeholder="Provider Name" value="<?php echo $row2['provider_name']; ?>"><br>
                        <div class="errorMessageBox" id="providerName-messageBox"></div>
                        
                        </div>
                        <div class="input-box">
                        <!-- <label>Contact Number</label> -->
                        <img src="../files/call.png" alt="UserIcon">
                        <input type="tel" id="contactNumber" name="contactNumber" placeholder="Contact Number" value="<?php echo $row2['contact_number']; ?>"><br>
                        <div class="errorMessageBox" id="contactNumber-messageBox"></div>
                        </div>
                        <div class="input-box">
                        <!-- <label>Email</label> -->
                        <img src="../files/email.png" alt="UserIcon">
                        <input type="email" id="email" name="email" placeholder="Email" value="<?php echo $row2['email']; ?>"><br>
                        <div class="errorMessageBox" id="email-messageBox"></div>
                        </div>
                        <!-- To let js know which type of user is being edited so it can validate input accordingly -->
                        <input type="hidden" id="formType" value="Provider">
                        <?php
                    }
                    
                    // Change password
                    ?>
                    <div class="input-box">
                        <a href="#" title="Password should be more than 8 characters with letters and numbers" style='cursor:default;'><img src="../files/password_icon.png" alt="PwIcon"></a>
                        <input type="password" name="newPassword" id="newPassword" placeholder="New Password"><br>
                        <div class="errorMessageBox" id="newPassword-messageBox"></div>
                    </div> 
                    
                    <div class="buttons">
                        <div class='backBtn'>
                            <a href="dashboard.php"><input type='button' name='back' value='Back'></a>
                        </div>
                        <div class='submitBtn'>
                            <input type='submit' name='editAccount' value='Save'><br><br>
                        </div>
                    </div>
                    <input type="hidden" name="hiddenEditAccount" id="editAccount" value="false">

                </form>
            </dialog>
            <?php
        }?>

        <!-- View Account Details Dialog -->
        <?php
        if(isset($_GET['view'])) {
            $username = $_GET['username'];
            $sql = "SELECT * FROM user where username='$username';";
            $result = mysqli_query($connect,$sql);
            $user = mysqli_fetch_assoc($result);
            echo "<dialog view-details-modal class='view-details'>";
            echo "<button id='close-view'>&#10006;</button>";
            echo "<br><b>Profile Image</b><br>";
            if ($user["profile_image_path"] != NULL) {
                ?>
                <!-- Profile image -->
                <img src=<?php echo $user["profile_image_path"] ?> alt = "Profile image">

                <?php
            }
            else {
                ?>
                <!-- <img src="../files/name.png" alt = "Profile image"> -->
                <img src="../files/defaultProfileImage.jpg" alt = "Profile image">
                <?php
            }
            echo "<br><b>Username</b><br>";
            echo $user["username"]; 
            echo "<br><b>Usertype</b><br>";
            echo $user["usertype"]; 
            echo "<br><b>Joined Date</b><br>";
            echo $user["joined_date"]; 

            if($user["usertype"] == "Instructor") {
                
                $sql = "SELECT AVG(rating) as rating, COUNT(rating) as amount FROM instructor_feedback where instructor_username = '$username'";
                $result = mysqli_query($connect,$sql);
                $rating = mysqli_fetch_assoc($result);
                $numOfStars = floor($rating["rating"]);
                echo "<div class=rating>";
                echo "<b>Rating</b>";
                for($i=0; $i<$numOfStars; $i++)
                    echo "<img src='../files/yellow-star.png' alt='star'>";
                for($i=0; $i<(5-$numOfStars); $i++)
                    echo "<img src='../files/blank-star.png' alt='star'>";
            
                echo " (" . $rating["amount"] . ")";
                echo "</div>"; 
                

                $sql = "SELECT * FROM instructor JOIN training_provider ON instructor.provider_username = training_provider.username where instructor.username='$username';";
                $result = mysqli_query($connect,$sql);
                $row = mysqli_fetch_assoc($result);

                echo "<br><b>First Name</b><br>";
                echo $row["first_name"]; 
                echo "<br><b>Last Name</b><br>";
                echo $row["last_name"]; 
                echo "<br><b>Training Provider Username</b><br>";
                echo $row["provider_username"]; 
                echo "<br><b>Training Provider Name</b><br>";
                echo $row["provider_name"];
                
                $sql = "SELECT * FROM instructor where instructor.username='$username';";
                $result = mysqli_query($connect,$sql);
                $row = mysqli_fetch_assoc($result);

                echo "<br><b>Contact Number</b><br>";
                echo $row["contact_number"]; 
                echo "<br><b>Email</b><br>";
                echo $row["email"];
                echo "<br>";
                

                echo "<br><b>Courses</b><br>"; ?>
                
                <?php 
                $sql = "SELECT * FROM instructor JOIN training_provider ON 
                        instructor.provider_username = training_provider.username 
                        JOIN course_section ON course_section.username = instructor.username
                        JOIN course ON course_section.course_id = course.course_id
                        where instructor.username='" . $username ."';";

                $result = mysqli_query($connect,$sql); 
                $numOfRows = mysqli_num_rows($result);
                if($numOfRows == 0) {
                    echo "No Course In Charge";
                }
                else {
                    ?>
                    <table id="instructor-course-table">
                    <thead>
                        <tr>
                            <th>Course ID</th>
                            <th>Course Title</th>
                            <th>Course Section</th>
                            <th>Day</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row["course_id"]. "</td>";
                        echo "<td>" . $row["course_title"]. "</td>";
                        echo "<td>" . $row["course_section_name"] . "</td>";
                        echo "<td>" . $row["day"] . "</td>";
                        if ($row["start_time"] == null) {
                            echo "<td></td>";
                        } else {
                            echo "<td>" . date("h:i:a",strtotime($row["start_time"])) . " - " . date("h:i:a",strtotime($row["end_time"])) ."</td>";
                        }
                        echo "</tr>";
                    }?>
                    </tbody>
                    </table>
                    <?php
                }

            }
            else if($user["usertype"] == "Student") {
                $sql = "SELECT * FROM student JOIN training_provider ON 
                        student.provider_username = training_provider.username 
                        where student.username='" . $username ."';";

                $result = mysqli_query($connect,$sql);
                $row = mysqli_fetch_assoc($result);

                echo "<br><b>First Name</b><br>";
                echo $row["first_name"]; 
                echo "<br><b>Last Name</b><br>";
                echo $row["last_name"]; 
                echo "<br><b>Date of Birth</b><br>";
                echo $row["date_of_birth"]; 
                echo "<br><b>Academic Program</b><br>";
                echo $row["academic_program"]; 
                echo "<br><b>Training Provider Username</b><br>";
                echo $row["provider_username"];
                echo "<br><b>Training Provider Name</b><br>";
                echo $row["provider_name"];  
                echo "<br><b>Contact Number</b><br>";

                $sql = "SELECT * FROM student where student.username='" . $username ."';";

                $result = mysqli_query($connect,$sql);
                $row = mysqli_fetch_assoc($result);

                echo $row["contact_number"]; 
                echo "<br><b>Email</b><br>";
                echo $row["email"];
                echo "<br>"; 
                ?>
                <h3>Registered Course</h3>
                <?php 
                $sql = "SELECT * FROM student JOIN training_provider ON 
                        student.provider_username = training_provider.username 
                        JOIN course_student ON course_student.username = student.username
                        JOIN course_section ON course_student.course_section_id = course_section.course_section_id
                        JOIN course ON course_section.course_id = course.course_id
                        where student.username='" . $username ."';";

                $result = mysqli_query($connect,$sql);
                $numOfRows = mysqli_num_rows($result);
                if ($numOfRows == 0) {
                    echo "<p>No course found</p>";
                }
                else { ?>
                    <table id="course-section-table">
                    <thead>
                        <tr>
                        <th>Course Title</th>
                        <th>Course Section Name</th>
                        <th>Day</th>
                        <th>Time</th>
                        <th>Completed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row["course_title"] . "</td>";
                            echo "<td>" . $row["course_section_name"] . "</td>";
                            echo "<td>" . $row["day"] . "</td>";
                            if ($row["start_time"] == null) {
                                echo "<td></td>";
                            } else {
                                echo "<td>" . date("h:i:a",strtotime($row["start_time"])) . " - " . date("h:i:a",strtotime($row["end_time"])) ."</td>";
                            }
                            echo "<td>" . $row["course_completed_date"] . "</td>";
                            echo "</tr>";
                        } ?>
                    </tbody>
                    </table>
                    <?php
                }
            }
            else if($user["usertype"] == "Provider") {
                $sql = "SELECT * FROM training_provider where username='$username';";
                $result = mysqli_query($connect,$sql);
                $row = mysqli_fetch_assoc($result);

                echo "<br><b>Training Provider Name</b><br>";
                echo $row["provider_name"]; 
                echo "<br><b>Contact Number</b><br>";
                echo $row["contact_number"]; 
                echo "<br><b>Email</b><br>";
                echo $row["email"];
                echo "<br>";
                echo "<br><b>Courses</b><br>";

                $sql = "SELECT * FROM course where provider_username = '" . $username . "';";
                $result = mysqli_query($connect,$sql);
                $numOfRows = mysqli_num_rows($result);
                if($numOfRows == 0) {
                    echo "No Course Created";
                }
                else {
                    ?>
                    <table>
                    <thead>
                        <tr>
                            <th>Course ID</th>
                            <th>Course Title</th>
                            <th>Course Description</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row["course_id"]. "</td>";
                            echo "<td>" . $row["course_title"]. "</td>";
                            echo "<td>" . $row["course_description"] . "</td>";
                            echo "<td>" . $row["start_date"] . "</td>";
                            echo "<td>" . $row["end_date"] . "</td>";
                            echo "</tr>";
                        }?>
                    </tbody>
                    </table>
                    <?php
                }
            }
            echo "</dialog>";
        }?>

        <!-- Display numbers of each usertype -->
        <?php
            $sql = "SELECT * FROM user where usertype = 'Admin';";
            $result = mysqli_query($connect,$sql);
            $countAdmin = mysqli_num_rows($result);

            if ($_SESSION['usertype'] == "Admin") {
                $sql = "SELECT * FROM instructor;";
                $result = mysqli_query($connect,$sql);
                $countInstructor = mysqli_num_rows($result);
    
                $sql = "SELECT * FROM student;";
                $result = mysqli_query($connect,$sql);
                $countStudent = mysqli_num_rows($result);
            }
            else if ($_SESSION['usertype'] == "Provider") {
                $sql = "SELECT * FROM instructor WHERE instructor.provider_username='".$_SESSION['username']."';";
                $result = mysqli_query($connect,$sql);
                $countInstructor = mysqli_num_rows($result);
    
                $sql = "SELECT * FROM student WHERE student.provider_username='".$_SESSION['username']."';";
                $result = mysqli_query($connect,$sql);
                $countStudent = mysqli_num_rows($result);
            }

            $sql = "SELECT * FROM training_provider;";
            $result = mysqli_query($connect,$sql);
            $countProvider = mysqli_num_rows($result);

            ?>
            <div class="bottomPanel">
            <?php
            if ($_SESSION['usertype'] == 'Admin') {
                $sql = "SELECT * FROM user;";
            
                $result = mysqli_query($connect,$sql);
                $count = mysqli_num_rows($result);
            ?>
            <div class="num-count-card">
                <div class="userColor"></div>
                <p class="title">Number of Users:</p>
                <p class="data"><?php echo $count; ?></p>
            </div>
            <div class="num-count-card">
                <div class="adminColor"></div>
                <p class="title">Number of Admins:</p>
                <p class="data"><?php echo $countAdmin; ?></p>
            </div>
            <div class="num-count-card">
                <div class="providerColor"></div>
                <p class="title">Number of Providers:</p>
                <p class="data"><?php echo $countProvider; ?></p>
            </div>

            <?php 
        } ?>

        
            <div class="num-count-card">
                <div class="instructorColor"></div>
                <p class="title">Number of instructor:</p>
                <p class="data"><?php echo $countInstructor; ?></p>
            </div>

            <div class="num-count-card">
                <div class="studentColor"></div>
                <p class="title">Number of student:</p>
                <p class="data"><?php echo $countStudent; ?></p>
            </div>
        </div>

        <?php
        // User table
        if ($_SESSION["usertype"] == "Admin") {
            $sql = "SELECT * FROM user;";
            
            $result = mysqli_query($connect,$sql);
            $count = mysqli_num_rows($result);
            ?>
            <!-- All user in a table -->
            <div class="table-wrapper table-wrapper-full-height">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Usertype</th>
                        <th>Joined Date</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <td><?php echo $row["username"]; ?></td>
                            <td><?php echo $row["usertype"]; ?></td>
                            <td><?php echo $row["joined_date"]; ?></td>
                            <!-- <td><a href="accountDetail.php?view&username=<?php echo $row["username"];?>">Details</a></td> -->
                            <td><a href="dashboard.php?view&username=<?php echo $row["username"];?>"><button class="btn-view-details">Details</button></a></td>

                            <!-- <td><a href="editAccount.php?edit&username=<?php echo $row["username"];?>">Edit</a></td> -->
                            <td><a href="dashboard.php?edit&username=<?php echo $row["username"];?>"><button class="btn-edit-details">Edit</button></a></td>
                        </tr>
                    <?php 
                    } ?>
                </tbody>
            </table>
            </div>
            <?php
        }
        // Instructor and Student table
        else if($_SESSION["usertype"] == "Provider") {
            ?>
            <!-- Instructor Table -->
            <h4>Instructors</h4>
            <div class='table-wrapper table-wrapper-half-height'>
            <table>
                <!-- <caption>Instructors</caption> -->
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Joined Date</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM user INNER JOIN instructor on user.username = instructor.username WHERE provider_username = '". $_SESSION["username"] . "';";
                    $result = mysqli_query($connect,$sql);
                    $countInstructor = mysqli_num_rows($result);

                    while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row["username"]; ?></td>
                        <td><?php echo $row["first_name"]; ?></td>
                        <td><?php echo $row["last_name"]; ?></td>
                        <td><?php echo $row["contact_number"]; ?></td>
                        <td><?php echo $row["email"]; ?></td>
                        <td><?php echo $row["joined_date"]; ?></td>
                        <td><a href="dashboard.php?view&username=<?php echo $row["username"];?>"><button style="background-color:lightgreen;">Details</button></a></td>
                        <td><a href="dashboard.php?edit&username=<?php echo $row["username"];?>"><button style="background-color:#3498db;">Edit</button></a></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            </div>

            <!-- Student Table -->
            <h4>Students</h4>
            <div class='table-wrapper table-wrapper-half-height'>
            <table>
                <!-- <caption>Students</caption> -->
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Date Of Birth</th>
                        <th>Academic Program</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Joined Date</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT * FROM user INNER JOIN student on user.username = student.username WHERE provider_username = '". $_SESSION["username"] . "';";
                    $result = mysqli_query($connect,$sql);
                    $countStudent = mysqli_num_rows($result);

                    while($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td><?php echo $row["username"]; ?></td>
                        <td><?php echo $row["first_name"]; ?></td>
                        <td><?php echo $row["last_name"]; ?></td>
                        <td><?php echo $row["date_of_birth"]; ?></td>
                        <td><?php echo $row["academic_program"]; ?></td>
                        <td><?php echo $row["contact_number"]; ?></td>
                        <td><?php echo $row["email"]; ?></td>
                        <td><?php echo $row["joined_date"]; ?></td>
                        <td><a href="dashboard.php?view&username=<?php echo $row["username"];?>"><button style="background-color:lightgreen;">Details</button></a></td>
                        <td><a href="dashboard.php?edit&username=<?php echo $row["username"];?>"><button style="background-color:#3498db;">Edit</button></a></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            </div>
        <?php
        }
        ?>
      

        
    </div>
</body>

<script>
    $(document).ready(function() {
        $(".sidebar").hover(
          function() {
            $(".content").addClass("shifted");
          },
          function() {
            $(".content").removeClass("shifted");
          }
        );
      }
    );

    const createAccountButton = document.querySelector("[create-account-button]");
    const viewDetailsButton = document.getElementsByClassName('btn-view-details');
    const editDetailsButton = document.getElementsByClassName("btn-edit-details");
    const closeViewDetailsBtn = document.getElementById("close-view");
    
    const createAccountModal = document.querySelector("[create-account]");
    const detailsModal = document.querySelector("[view-details-modal]");
    const editModal = document.querySelector("[edit-account-modal]");

    if (closeViewDetailsBtn != null) {
        closeViewDetailsBtn.addEventListener('click', () => {
            detailsModal.close();
            window.location.replace('dashboard.php');
        });
    }

    window.addEventListener('load', e => {
        if ("view" == "<?php if (isset($_GET['view'])) echo "view"; else echo "";?>") 
        {
            detailsModal.showModal();
        }
        else if ("edit" == "<?php if (isset($_GET['edit'])) echo "edit"; else echo "";?>") 
        {
            editModal.showModal();
        }
    });

    window.addEventListener('click', e => {
        if (e.target === detailsModal) {
            detailsModal.close();
            window.location.replace('dashboard.php');
        }
    });

    window.addEventListener('click', e => {
        if (e.target === editModal){
            editModal.close();
            window.location.replace('dashboard.php');
        }
    });

    createAccountModal.addEventListener('click', e => {
        if (e.target === createAccountModal) {
            createAccountModal.close();
            window.location.replace('dashboard.php');
        } 
    });

    createAccountButton.addEventListener("click", () =>{
        createAccountModal.showModal();
    });

    function updateForm() {
        const usertypeSelected = document.getElementById("usertype").value;
        
        const additionalForm = document.getElementById("additionalFields");
        additionalForm.innerHTML = "";
        
        const selectProviderHtml = 
            `<div class="input-box">
            <select name="provider" id="provider">
            <option disabled selected value>Select the training provider</option>
            <?php 
                    $sql = "SELECT * FROM training_provider;";
                    $result = mysqli_query($connect,$sql);
                    $count = mysqli_num_rows($result);
                    if ($count == 0) {
                        echo $count;
                        ?>
                        <option disabled selected value>No Training Provider Found</option>

                    <?php
                    }
                    while($row = mysqli_fetch_array($result)) {
                        ?>
                        <option value="<?php echo $row["username"] ?>"><?php echo $row["username"] . " - " 
                        . $row["provider_name"]?></option> 
                    <?php } 
                ?>
            </select>
            <div class="errorMessageBox" id="provider-messageBox"></div><br>
            </div>`;

        const nameInputHtml = `<div class="input-box">
            <img src="../files/name.png" alt="UserIcon">
            <input type="text" id="firstName" name="firstName" placeholder="First Name">
            <div class="errorMessageBox" id="firstName-messageBox"></div><br>
            </div>
            <div class="input-box">
            <img src="../files/name.png" alt="UserIcon">
            <input type="text" id="lastName" name="lastName" placeholder="Last Name">
            <div class="errorMessageBox" id="lastName-messageBox"></div><br>
            </div>`;
            
        const contactInputHtml = `<div class="input-box">
            <img src="../files/call.png" alt="UserIcon">
            <input type="tel" id="contactNumber" name="contactNumber" placeholder="Contact Number">
            <div class="errorMessageBox" id="contactNumber-messageBox"></div><br>
            </div>
            <div class="input-box">
            <img src="../files/email.png" alt="UserIcon">
            <input type="email" id="email" name="email" placeholder="Contact Email">
            <div class="errorMessageBox" id="email-messageBox"></div><br>
            </div>`;

        if (usertypeSelected == "Provider") {
            const html = 
            `<div class="input-box">
            <img src="../files/name.png" alt="UserIcon">
            <input type="text" id="providerName" name="providerName" placeholder="Provider Name">
            <div class="errorMessageBox" id="providerName-messageBox"></div><br>
            </div>`;
            additionalForm.innerHTML = html + contactInputHtml;
        }
        else if (usertypeSelected == "Instructor") {
            additionalForm.innerHTML = selectProviderHtml + nameInputHtml + contactInputHtml;
        }
        else if (usertypeSelected == "Student") {
            const html = 
            `<div class="input-box">
            <img src="../files/calendar.png" alt="UserIcon">
            <input type="date" id="dateOfBirth" name="dateOfBirth">
            <div class="errorMessageBox" id="dateOfBirth-messageBox"></div><br>
            </div>
            <div class="input-box">
            <img src="../files/course.png" alt="UserIcon">
            <input type="text" id="academicProgram" name="academicProgram" placeholder="Academic Program">
            <div class="errorMessageBox" id="academicProgram-messageBox"></div><br>
            </div>
            `;
            additionalForm.innerHTML = selectProviderHtml + nameInputHtml + html + contactInputHtml;
        }
        else if (usertypeSelected == "Admin") {
            // Do nothing
        } 
    }

    function showErrorMessage(message, type){
        const errorMessageBox = document.getElementById(type+"-messageBox");
        const errorHtml = `${message}`;
        errorMessageBox.innerHTML = errorHtml;
    }
    
    function mySubmitFunction(event) {
        event.preventDefault();

        // Add Account
        if(document.getElementById("createAccount").value!=null){
            var usernameElement = document.getElementById("username");
            var passwordElement = document.getElementById("password");
            var usertypeElement = document.getElementById("usertype");

            var username = usernameElement ? usernameElement.value : "";
            var password = passwordElement ? passwordElement.value : "";
            var usertype = usertypeElement ? usertypeElement.value : "";

            var allowSubmission = true;
            if (username == "") {
                showErrorMessage("Username is required", "username");
                allowSubmission = false;
            }
            else{
                showErrorMessage("", "username");
            }

            var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)/;
            if (password.length < 8 || !passwordPattern.test(password)) {
                if(password.length < 8) showErrorMessage("Password must be at least 8 characters", "password");
                else showErrorMessage("Password must include at least one letter and one number", "password");
                allowSubmission = false;
            }
            else{
                showErrorMessage("", "password");
            }

            if (usertype == "") {
                showErrorMessage("Usertype is required", "usertype");
                allowSubmission = false;
            }
            else{
                showErrorMessage("", "usertype");
            }

            if (usertype == "Instructor" || usertype == "Student") {
                var firstNameElement = document.getElementById("firstName");
                var lastNameElement = document.getElementById("lastName");
                var providerElement = document.getElementById("provider");



                var firstName = firstNameElement ? firstNameElement.value : "";
                var lastName = lastNameElement ? lastNameElement.value : "";
                var provider = providerElement ? providerElement.value : "";


                if (firstName == "") {
                    showErrorMessage("First name is required", "firstName");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "firstName");
                }

                if (lastName == "") {
                    showErrorMessage("Last name is required", "lastName");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "lastName");
                }

                if(provider == ""){
                    showErrorMessage("Provider is required", "provider")
                    allowSubmission=false;
                }
                else{
                    showErrorMessage("","provider")
                }

                if (usertype == "Student") {
                    var dateOfBirthElement = document.getElementById("dateOfBirth");
                    var academicProgramElement = document.getElementById("academicProgram");

                    var dateOfBirth = dateOfBirthElement ? dateOfBirthElement.value : "";
                    var academicProgram = academicProgramElement ? academicProgramElement.value : "";

                    if (dateOfBirth == "") {
                        showErrorMessage("Date of birth is required", "dateOfBirth");
                        allowSubmission = false;
                    }
                    else{
                        showErrorMessage("", "dateOfBirth");
                    }

                    if (academicProgram == "") {
                        showErrorMessage("Academic program is required", "academicProgram");
                        allowSubmission = false;
                    }
                    else{
                        showErrorMessage("", "academicProgram");
                    }
                }

                var contactElement = document.getElementById("contactNumber");
                var emailElement = document.getElementById("email");

                var contact = contactElement ? contactElement.value : "";
                var email = emailElement ? emailElement.value : "";

                if(contact == ""){
                    showErrorMessage("Contact is required", "contactNumber");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "contactNumber");
                }

                if(email == ""){
                    showErrorMessage("E-mail is required", "email");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "email");
                }

            }

            if(usertype == "Provider"){

                var providerNameElement = document.getElementById("providerName");
                var contactNumberElement = document.getElementById("contactNumber");
                var emailElement = document.getElementById("email");

                var providerName = providerNameElement ? providerNameElement.value : "";
                var contactNumber = contactNumberElement ? contactNumberElement.value : "";
                var email = emailElement ? emailElement.value : "";

                if(providerName == ""){
                    showErrorMessage("Provider Name is required", "providerName");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "providerName");
                }

                if(contactNumber == ""){
                    showErrorMessage("Contact Number is required", "contactNumber");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "contactNumber");
                }

                if(email == ""){
                    showErrorMessage("E-mail is required", "email");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "email");
                }
            }

            if(allowSubmission) {
                document.getElementById("createAccount").value="true";
                document.getElementById('form').submit();
            }
            else{
                document.getElementById("createAccount").value="false";
            }
        }

        if(document.getElementById("editAccount").value!=null){

            var passwordElement = document.getElementById("newPassword");
            var password = passwordElement ? passwordElement.value : "";

            var allowSubmission = true;

            var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)/;
            if (password.length > 0) {
                if (password.length < 8 || !passwordPattern.test(password)) {
                    if(password.length < 8) showErrorMessage("Password must be at least 8 characters", "newPassword");
                    else showErrorMessage("Password must include at least one letter and one number", "newPassword");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "newPassword");
                }
            }

            // For other usertype may have other editable
            var usertype = document.getElementById("formType").value;

            if(usertype=="Instructor" || usertype=="Student"){
                var firstNameElement = document.getElementById("firstName");
                var lastNameElement = document.getElementById("lastName");

                var firstName = firstNameElement ? firstNameElement.value : "";
                var lastName = lastNameElement ? lastNameElement.value : "";


                if (firstName == "") {
                    showErrorMessage("First name is required", "firstName");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "firstName");
                }

                if (lastName == "") {
                    showErrorMessage("Last name is required", "lastName");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "lastName");
                }
            }

            if(usertype=="Student"){
                var dateOfBirthElement = document.getElementById("dateOfBirth");
                var academicProgramElement = document.getElementById("academicProgram");

                var dateOfBirth = dateOfBirthElement ? dateOfBirthElement.value : "";
                var academicProgram = academicProgramElement ? academicProgramElement.value : "";

                if (dateOfBirth == "") {
                    showErrorMessage("Date of birth is required", "dateOfBirth");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "dateOfBirth");
                }

                if (academicProgram == "") {
                    showErrorMessage("Academic program is required", "academicProgram");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "academicProgram");
                }
            }

            if(usertype=="Provider"){
                var providerNameElement = document.getElementById("providerName");

                var providerName = providerNameElement ? providerNameElement.value : "";

                if(providerName == ""){
                    showErrorMessage("Provider Name is required", "providerName");
                    allowSubmission = false;
                }
                else{
                    showErrorMessage("", "providerName");
                }
            }

            if(allowSubmission) {
                document.getElementById("editAccount").value="true";
                document.getElementById('resetPassword').submit();
            }
            else{
                document.getElementById("editAccount").value="false";
            }
        }

    }


</script>
</html>




