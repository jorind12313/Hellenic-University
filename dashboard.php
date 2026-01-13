<?php
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$role = $_SESSION['role'];



if ($role === 'student') {
    include 'student_dashboard.php';
} elseif ($role === 'professor') {
    include 'professor_dashboard.php';
} else {
    echo "Forbidden Action";
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>University Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Dashboard</h1>

<nav>
    <?php if ($role === 'student'): ?>
        <a href="dashboard.php?page=courses">My Courses</a>
        <a href="dashboard.php?page=assignments">Assignments</a>
        <a href="dashboard.php?page=grades">Grades</a>
    <?php elseif ($role === 'professor'): ?>
        <a href="dashboard.php?page=courses">Manage Courses</a>
        <a href="dashboard.php?page=assignments">Manage Assignments</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
</nav>

<hr>

<div class="dashboard-content">
<?php
$page = $_GET['page'] ?? 'home';

if ($role === 'student') {
    include 'student_dashboard.php';
} elseif ($role === 'professor') {
    include 'professor_dashboard.php';
} else {
    echo "Forbidden Action";
}
?>
</div>

</body>
</html>
