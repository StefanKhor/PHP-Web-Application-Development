<?php
    $connect= mysqli_connect("localhost","root","","tpms");
    if(!$connect)
        die('Could not connect to database:'.mysqli_error($connect));
?>