<?php

	include '../phpFunction/function.php';
	function instructorViewFeedback() {
		global $connect;
		$sql= "SELECT * FROM course_feedback JOIN course_section on
				course_feedback.course_section_id = course_section.course_section_id
				JOIN course on course_section.course_id = course.course_id
				WHERE course_section.username ='". $_SESSION['username'] ."';";
		$result = mysqli_query($connect,$sql);
		?>
		<link rel="stylesheet" href="feedback.css"/>
		<div class="title"><h2>View Feedback</h2></div>
		<div class="course-feedback">
				<div class="header"><h2>Course Feedback</h2></div>
				<table>
				<thead>
						<tr>
						<th>Feedback</th>
						<th>Course</th>
						<th>Rating</th>
						<th>Date</th>
						</tr>
				</thead>
				<?php
				while ($row = mysqli_fetch_assoc($result)) {
						echo "<tr>";
						echo "<td>".$row["feedback"]."</td>";
						echo "<td>".$row["course_title"]."</td>";
						echo "<td>".$row["rating"];
						for($i=0; $i<$row["rating"]; $i++)
								echo "<img src='../files/yellow-star.png' alt='star' width='15'>";
						for($i=0; $i<(5-$row["rating"]); $i++)
								echo "<img src='../files/blank-star.png' alt='star' width='15'>";
						echo "</td>";
						echo "<td>".date("j/n/Y",strtotime($row["date"]))."</td>";
						echo "</tr>";
				} ?>
				</table>
		</div>

		<?php
		$sql= "SELECT * FROM instructor_feedback JOIN instructor on instructor_feedback.instructor_username = instructor.username WHERE instructor.username ='". $_SESSION['username'] ."';";
		$result = mysqli_query($connect,$sql);
		?>
		<hr>
		<div class="instructor-feedback">
				<div class="header"><h2>Instructor Feedback</h2></div>
				<table>
						<thead>
						<tr>
								<th>Feedback</th>
								<th>Instructor</th>
								<th>Rating</th>
								<th>Date</th>
						</tr>
						</thead>
						<?php
						while ($row = mysqli_fetch_assoc($result)) {
						echo "<tr>";
						echo "<td>".$row["feedback"]."</td>";
						echo "<td>".$row["instructor_username"]."</td>";
						echo "<td>".$row["rating"];
						for($i=0; $i<$row["rating"]; $i++)
								echo "<img src='../files/yellow-star.png' alt='star' width='15'>";
						for($i=0; $i<(5-$row["rating"]); $i++)
								echo "<img src='../files/blank-star.png' alt='star' width='15'>";
						echo "</td>";
						echo "<td>".date("j/n/Y",strtotime($row["date"]))."</td>";
						echo "</tr>";
						}
						?>
				</table>
		</div>
		<?php
}
	generatePage("Feedback",'instructorViewFeedback','<link rel="stylesheet" type="text/css" href="../instructor/feedback.css">','');
?>