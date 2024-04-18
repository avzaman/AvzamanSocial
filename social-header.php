<!-- This is a header document that can be called from any page
    it allows us to easily created a uniform look across the website
by having a consistent header on each page. -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Feed</title>

    <!-- Google Fonts import -->
    <!-- Teko -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Teko:wght@300..700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <!-- Ubuntu -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400;1,500;1,700&display=swap" rel="stylesheet">

    <!-- Style Sheet Links -->
    <link rel="stylesheet" href="css/social-feed.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/reset.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/nav.css?v=<?php echo time(); ?>">

</head>

<body>
    <nav>
        <h1 class="site-title"><a href="social-feed.php">V</a></h1>
        <ul class="nav-links">


            <?php
            if (isset($_COOKIE['user'])) {
                $username = $_COOKIE["user"];
                echo "<li><a href='social-profile.php?profile=" . $username . "'>My Profile</a></li>";

                echo "<li><a href='phpFuncs/social-logout.php'>Logout</a></li>";
            } else {
                echo "<li><a href='index.php'>Login</a></li>
                    <li><a href='social-register.html'>Register</a></li>";
            }
            ?>
        </ul>
    </nav>