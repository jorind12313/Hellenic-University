<?php
session_start();
require "includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

if (password_verify($password, $user["password"])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];

    if ((int)$user["role_id"] === 2) {
        $_SESSION["role"] = "professor";
    } else {
        $_SESSION["role"] = "student";
    }

    header("Location: dashboard.php");
    exit;

} else {
    $message = "Incorrect password.";
}
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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
<h2>Login</h2>
<p class="message"><?php echo $message; ?></p>

<form method="POST">
    <label>Email</label>
    <input type="email" name="email" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
</form>

<a href="register.php">Need an account?</a>

</body>
</html>
