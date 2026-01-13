<?php
session_start();
require "includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $email    = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role     = $_POST["role"];
    $code     = $_POST["code"];

    // role codes
    $valid = [
        "student" => "STUD2025",
        "professor" => "PROF2025"
    ];

    if (!isset($valid[$role]) || $code !== $valid[$role]) {
        $message = "Invalid registration code.";
    } else {
        $role_id = ($role === "student") ? 1 : 2;

        $query = $conn->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
        $query->bind_param("sssi", $username, $email, $password, $role_id);

        if ($query->execute()) {
            $message = "Registration successful!";
        } else {
            $message = "Error: Email already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
   <nav> 
    <h1><a href="index.php">Hellenic University</a></h1>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<h2>User Registration</h2>
<p class="message"><?php echo $message; ?></p>

<form method="POST">
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <label>Select Role</label>
    <select name="role">
        <option value="student">Student</option>
        <option value="professor">Professor</option>
    </select>

    <label>Registration Code</label>
    <input type="text" name="code" required>

    <button type="submit">Register</button>
</form>

<a href="login.php">Already registered? Login here</a>

</body>
</html>
