<?php

if ($_SESSION['role'] !== 'professor') { 
    echo "Forbidden Action";
    exit;
}
?>

<h2>Professor Dashboard</h2>

<h3>Create Course</h3>

<form method="post">
    <input type="text" name="course_name" placeholder="Course name" required>
    <textarea name="description" placeholder="Course description"></textarea>
    <button type="submit">Create Course</button>
</form>

<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['course_name'];
    $description = $_POST['description'];
    $professor_id = $_SESSION['user_id'];

    $stmt = $conn->prepare(
        "INSERT INTO courses (name, description, professor_id) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("ssi", $name, $description, $professor_id);
    $stmt->execute();

    echo "<p>Course created successfully.</p>";
}
?>
<h3>My Courses</h3>

<ul>
<?php
$professor_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT id, name, description FROM courses WHERE professor_id = ?"
);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($course = $result->fetch_assoc()) {
    echo "<li><strong>{$course['name']}</strong> – {$course['description']}</li>";
}
?>
</ul>
<h3>Enrolled Students</h3>

<?php
$stmt = $conn->prepare(
    "SELECT courses.name AS course, users.username AS student
     FROM enrollments
     JOIN courses ON enrollments.course_id = courses.id
     JOIN users ON enrollments.student_id = users.id
     WHERE courses.professor_id = ?"
);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

$current_course = null;

while ($row = $result->fetch_assoc()) {
    if ($current_course !== $row['course']) {
        if ($current_course !== null) {
            echo "</ul>";
        }
        $current_course = $row['course'];
        echo "<h4>{$current_course}</h4><ul>";
    }
    echo "<li>{$row['student']}</li>";
}
if ($current_course !== null) {
    echo "</ul>";
}
?>
<h3>Create Assignment</h3>

<form method="post">
    <select name="course_id" required>
        <option value="">Select course</option>
        <?php
        $stmt = $conn->prepare(
            "SELECT id, name FROM courses WHERE professor_id = ?"
        );
        $stmt->bind_param("i", $professor_id);
        $stmt->execute();
        $courses = $stmt->get_result();

        while ($course = $courses->fetch_assoc()) {
            echo "<option value='{$course['id']}'>{$course['name']}</option>";
        }
        ?>
    </select><br><br>

    <input type="text" name="title" placeholder="Assignment title" required><br><br>
    <textarea name="description" placeholder="Assignment description"></textarea><br><br>
    <input type="date" name="due_date"><br><br>

    <button type="submit" name="create_assignment">Create Assignment</button>
</form>
<?php
if (isset($_POST['create_assignment'])) {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare(
        "INSERT INTO assignments (course_id, title, description, due_date)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("isss", $course_id, $title, $description, $due_date);
    $stmt->execute();

    echo "<p>Assignment created successfully.</p>";
}
?>
<h3>My Assignments</h3>

<?php
$stmt = $conn->prepare(
    "SELECT assignments.title, assignments.due_date, courses.name AS course
     FROM assignments
     JOIN courses ON assignments.course_id = courses.id
     WHERE courses.professor_id = ?"
);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<p>
        <strong>{$row['title']}</strong><br>
        Course: {$row['course']}<br>
        Due: {$row['due_date']}
    </p>";
}
?>
<h3>Student Submissions</h3>

<?php
$stmt = $conn->prepare(
    "SELECT submissions.id AS submission_id,
            assignments.title AS assignment,
            users.username AS student,
            submissions.content
     FROM submissions
     JOIN assignments ON submissions.assignment_id = assignments.id
     JOIN courses ON assignments.course_id = courses.id
     JOIN users ON submissions.student_id = users.id
     WHERE courses.professor_id = ?"
);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div>
        <strong>Assignment:</strong> {$row['assignment']}<br>
        <strong>Student:</strong> {$row['student']}<br>
        <strong>Submission:</strong><br>
        <pre>{$row['content']}</pre>

        <form method='post'>
            <input type='hidden' name='submission_id' value='{$row['submission_id']}'>
            <input type='text' name='grade' placeholder='Grade (e.g. A, 85)' required>
            <textarea name='feedback' placeholder='Feedback'></textarea><br>
            <button type='submit' name='grade_submission'>Submit Grade</button>
        </form>
        <hr>
    </div>";
}
?>
<?php
if (isset($_POST['grade_submission'])) {
    $submission_id = $_POST['submission_id'];
    $grade = $_POST['grade'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare(
        "INSERT INTO grades (submission_id, grade, feedback)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iss", $submission_id, $grade, $feedback);
    $stmt->execute();

    echo "<p>Grade submitted.</p>";
}
?>
