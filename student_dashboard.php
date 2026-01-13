<?php


if ($_SESSION['role'] !== 'student') {
    echo "Forbidden Action";
    exit;
}
?>

<h2>Student Dashboard</h2>

<h3>Available Courses</h3>

<ul>
<?php
require 'includes/db.php';

$result = $conn->query(
    "SELECT courses.id, courses.name, courses.description, users.username AS professor
     FROM courses
     JOIN users ON courses.professor_id = users.id"
);

while ($course = $result->fetch_assoc()) {
    echo "<li>
        <strong>{$course['name']}</strong><br>
        {$course['description']}<br>
        Professor: {$course['professor']}
    </li>";
}
?>
</ul>
<h3>Available Courses</h3>

<ul>
<?php
require 'includes/db.php';
$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT courses.id, courses.name, courses.description, users.username AS professor
     FROM courses
     JOIN users ON courses.professor_id = users.id"
);
$stmt->execute();
$result = $stmt->get_result();

while ($course = $result->fetch_assoc()) {
    echo "<li>
        <strong>{$course['name']}</strong><br>
        {$course['description']}<br>
        Professor: {$course['professor']}
        <form method='post' style='margin-top:5px;'>
            <input type='hidden' name='course_id' value='{$course['id']}'>
            <button type='submit' name='enroll'>Enroll</button>
        </form>
    </li>";
}
?>
</ul>
<?php
if (isset($_POST['enroll'])) {
    $course_id = $_POST['course_id'];

    $stmt = $conn->prepare(
        "INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)"
    );
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();

    echo "<p>Enrolled successfully.</p>";
}
?>
<h3>My Courses</h3>

<ul>
<?php
$stmt = $conn->prepare(
    "SELECT courses.name
     FROM courses
     JOIN enrollments ON courses.id = enrollments.course_id
     WHERE enrollments.student_id = ?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

while ($course = $result->fetch_assoc()) {
    echo "<li>{$course['name']}</li>";
}
?>
</ul>
<h3>My Assignments</h3>

<?php
$stmt = $conn->prepare(
    "SELECT assignments.id, assignments.title, assignments.description, assignments.due_date, courses.name AS course
     FROM assignments
     JOIN courses ON assignments.course_id = courses.id
     JOIN enrollments ON courses.id = enrollments.course_id
     WHERE enrollments.student_id = ?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

while ($assignment = $result->fetch_assoc()) {
    echo "<div>
        <strong>{$assignment['title']}</strong><br>
        Course: {$assignment['course']}<br>
        Due: {$assignment['due_date']}<br>
        {$assignment['description']}
    </div><hr>";
}
?>
<form method="post">
    <input type="hidden" name="assignment_id" value="{$assignment['id']}">
    <textarea name="content" placeholder="Your submission" required></textarea><br>
    <button type="submit" name="submit_assignment">Submit</button>
</form>
<?php
if (isset($_POST['submit_assignment'])) {
    $assignment_id = $_POST['assignment_id'];
    $content = $_POST['content'];

    $stmt = $conn->prepare(
        "INSERT INTO submissions (assignment_id, student_id, content)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iis", $assignment_id, $student_id, $content);
    $stmt->execute();

    echo "<p>Assignment submitted.</p>";
}
?>
<h3>My Grades</h3>

<?php
$stmt = $conn->prepare(
    "SELECT assignments.title,
            grades.grade,
            grades.feedback
     FROM grades
     JOIN submissions ON grades.submission_id = submissions.id
     JOIN assignments ON submissions.assignment_id = assignments.id
     WHERE submissions.student_id = ?"
);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<p>
        <strong>{$row['title']}</strong><br>
        Grade: {$row['grade']}<br>
        Feedback: {$row['feedback']}
    </p>";
}
?>

