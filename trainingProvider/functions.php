<?php
    include "../db_connect.php";

    if(!$connect) {
        die("Error");
    }

    function getCourse($courseID,$connect) {
        $sql = "SELECT * FROM course where course_id=" . $courseID . ";";
        $result = mysqli_query($connect,$sql);
        $row = mysqli_fetch_assoc($result);
        $count = mysqli_num_rows($result);

        if ($count == 0) {
            return null;
        }
        return $row;
    }

    function getCourseSectionsResult($courseID,$connect) {
        $sql = "SELECT * FROM course_section INNER JOIN instructor on course_section.username = instructor.username where course_id = " . $courseID . ";";
        $result = mysqli_query($connect,$sql);
        return $result;
    }

?>