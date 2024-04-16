<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
</head>

<body>

    <div class="login-container">

        <h3>Welcome to Amir Zaman's NoSql project</h3>
        where I experiment with MongoDB using a social media website!
        <p>
            Please visit the <a href="https://github.com/avzaman/AvzamanSocial">github page</a> if you would like a
            guide to set up your own simple social media platform with your friends.
            This page allows new users to register, existing users to login, and guests to view the main pages.
            Registration was closed off to just my peers as to ovoid overcrowding and content moderation.
            Each user info is stored in mongodb as a json object.
        </p>
        <h2>Login</h2>
        <form action="phpFuncs/social-login.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <div class="register-link">
            <a href="social-register.html">Register</a>
        </div>
        <div class="guest-login">
            <a href="social-feed.php">Login as Guest</a>
        </div>
    </div>
    <div class="chart-container">
        <?php
        require_once __DIR__ . '/vendor/autoload.php';

        if (!extension_loaded('gd')) {
            echo 'GD is not enabled.';
        } else {
            echo 'GD is enabled.';
        }


        include 'phpFuncs/dbconfig.php';
        // Connect to MongoDB
        $mongoClient = new MongoDB\Client($uri);

        // Select database and collections
        $database = $mongoClient->$db;
        $usersCollection = $database->Users;
        $postsCollection = $database->Posts;

        // Count the number of users
        $numUsers = $usersCollection->countDocuments();

        // Count the number of posts
        $numPosts = $postsCollection->countDocuments();

        // Count the number of posts where the "image" field is not null
        $numPostsWithImages = $postsCollection->countDocuments(["image" => ['$exists' => true]]);

        // Calculate the total number of replies
        $totalReplies = 0;
        $cursor = $postsCollection->find([], ["projection" => ["replies" => 1]]);
        foreach ($cursor as $post) {
            if (isset($post['replies'])) {
                $totalReplies += count($post['replies']);
            }
        }

        $data = array(
            "Users" => $numUsers,
            "Posts" => $numPosts,
            "Image" => $numPostsWithImages,
            "Replies" => $totalReplies
        );

        // Set the dimensions of the image
        $imageWidth = 600;
        $imageHeight = 400;

        // Create a blank image with white background
        $image = imagecreatetruecolor($imageWidth, $imageHeight);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, $imageWidth, $imageHeight, $backgroundColor);

        // Define colors
        $barColor = imagecolorallocate($image, 50, 100, 200);
        $fontColor = imagecolorallocate($image, 0, 0, 0);

        // Find the maximum value in the data
        $maxValue = max($data);

        // Define the dimensions of the bars
        $barWidth = 50;
        $spacing = 20;
        $startingX = 50;
        $startingY = 300;
        $barHeightRatio = ($imageHeight / $maxValue)-20;

        // Draw the bars and labels
        $index = 0;
        foreach ($data as $label => $value) {
            $x1 = $startingX + ($barWidth + $spacing) * $index;
            $y1 = $startingY - $value * $barHeightRatio;
            $x2 = $x1 + $barWidth;
            $y2 = $startingY;

            // Draw the bar
            imagefilledrectangle($image, $x1, $y1, $x2, $y2, $barColor);

            // Draw the label
            $labelX = $x1 + ($barWidth / 2) - 10;
            $labelY = $y2 + 20;
            imagestring($image, 4, $labelX, $labelY, $label, $fontColor);

            // Draw the value above the bar
            $valueX = $x1 + ($barWidth / 2) - 10;
            $valueY = $y1 - 20;
            imagestring($image, 4, $valueX, $valueY, $value, $fontColor);

            $index++;
        }

        // Output the image
        // Save the image to a file
        $imageFileName = 'img/bar_chart.png';
        imagepng($image, $imageFileName);

        // Free up memory
        imagedestroy($image);

        ?>
        <img src='img/bar_chart.png' alt="Bar Chart">
    </div>

</body>

</html>