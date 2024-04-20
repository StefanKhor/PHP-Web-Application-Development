<?php 
    include 'db_connect.php';
    include 'phpFunction/function2.php';
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" type="text/css" href = "css/login.css">
    </head>

    <body>
        <div class="container">

        <!-- left part div -->
            <div class="leftdiv">
                <img src="files/tpms.png" alt="MMU Logo" >
            </div>

        <!-- right part div -->
            <form action="" method="POST">
                <div class = "rightdiv">            
                    <h2>Sign In</h2>
                    <input type="text" name="username" placeholder = "Username" required>

                    <input type="password" name="password" placeholder = "Password" required>

                    <input type="submit" name="LoginBtn" value="Login">
                </div>
            </form>    
        </div>  
    </body>
</html>

<?php
    if (isset($_POST["LoginBtn"])) {
        $usernameInput = $_POST["username"];
        $passwordInput = $_POST["password"];

        $sql = "select * from user where username='$usernameInput'";
        $result = mysqli_query($connect,$sql); 

        $row = mysqli_fetch_assoc($result);
        if(isset($row['password_hash'])){
            if(password_verify($passwordInput,$row["password_hash"])) {

                $_SESSION["username"] = $row["username"];
                $_SESSION["usertype"] = $row["usertype"];
                if($row["usertype"] == "Admin") { // Redirect user to admin page
                    header("Location: admin/dashboard.php");
                }
                else if ($row["usertype"] == "Student"){
                    header("Location: student/dashboard.php");
                }
                else if ($row["usertype"] == "Instructor"){
                    header("Location: instructor/dashboard.php");
                }
                else if ($row["usertype"] == "Provider"){
                    header("Location: trainingProvider/dashboard.php");
                }
            }
            else{
                generateJavaScriptAlert("Login Failed");
            }
        }
        else {
            generateJavaScriptAlert("Login Failed");
        }
    }    
?>