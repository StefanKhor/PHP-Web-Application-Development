<?php 
    include "../db_connect.php";
    session_start();
    if(!($_SESSION["usertype"] == "Admin" || $_SESSION["usertype"] == "Provider")) {
        header("Location: login.php");
    }

    
?>
    <!DOCTYPE html>
    <html>
    <head> 
        <title>Course Details</title>
        <link rel="stylesheet" href="../navBar/navBarStyle.css"/>
        <link rel="stylesheet" href="../admin/dashboard.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src ="../js/navbar.js"></script></head>
        

    <body>
    <div class="Container">
        <div class="sidebar">
            <?php include '../NavBar/NavBar.php'?>
        </div>
        <div class="content" id="content"> 
            <header>
                <h1>Student Enrollment List</h1>
                <button onclick='history.back()'>Back</button>
            </header>
            <?php
            if(isset($_GET["view"]) && isset($_GET["courseSection"])) {
                $sql = "SELECT * FROM course_student JOIN student 
                ON course_student.username = student.username
                WHERE course_section_id =" .$_GET['courseSection'] . ";";
                $result = mysqli_query($connect,$sql);
                $count = mysqli_num_rows($result);
                if(!$result) {
                    echo mysqli_error($connect);
                }
                if($count == 0) {
                    echo "<h4>No student enrolled in this course</h4><br>";
                }   
                ?>
                <div class="table-wrapper table-wrapper-full-height">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Program</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Completed Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            $count = 1;
                            
                            while($row=mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $count; $count+=1?></td>
                                    <td><?php echo $row["username"]?></td>
                                    <td><?php echo $row["first_name"] . " " . $row['last_name']?></td>
                                    <td><?php echo $row["academic_program"]?></td>
                                    <td><?php echo $row["contact_number"]?></td>
                                    <td><?php echo $row["email"]?></td>
                                    <td><?php 
                                        if ($row["course_completed"] == 1) {
                                            echo "Completed";
                                        } else {
                                            echo "Incomplete";
                                        } ?></td>
                                    <td><?php echo $row["course_completed_date"]?></td>
                                </tr>
                        <?php }
                        ?>
                        </tbody>
                    </table>
                </div>
                <?php
            }
            ?>
        </div> 
    </div>
    </body>
    </html>
