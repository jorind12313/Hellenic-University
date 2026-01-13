<?php session_start(); ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hellenic University</title>
    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>

<header>
   <nav> 
    <h1><a href="index.php">Hellenic University</a></h1>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<section class="info">
    <h2>About Our Campus</h2>
    <p>
        Hellenic University is a prestigious institution located in the heart of Athens, Greece. 
        We offer a wide range of undergraduate and postgraduate programs across various disciplines. 
        Our campus is equipped with state-of-the-art facilities, including modern lecture halls, 
        research labs, and recreational areas to enhance the student experience.
    </p>
</section>

<section class="courses">
    <h2>Available courses</h2>
    <ul>
        <li>Computer Science</li>
        <li>Business Administration</li>
        <li>Engineering</li>
        <li>Medicine</li>
        <li>Law</li>
        <li>Philosophy</li>
        <li>Physics</li>
        <li>Chemistry</li>

    </ul>
</section>

<section class="map-container">
    <h2>Campus Location</h2>
    <div id="map"></div>
</section>

<script src="js/map.js"></script>
</body>
</html>
