<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Feed</title>
    <link rel="stylesheet" href="css/social-profile.css?v=<?php echo time(); ?>">
</head>
<body>
<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$db = 'CPS4881';
$col = 'Posts';

echo "<a href='social-feed.php'>Back to main feed</a><br><br>";

$profileToLoad = $_GET["profile"];
$userIsGuest = false;
$userHomePage = false;

if (isset($_COOKIE["user"])) {
    $username = $_COOKIE["user"];
    echo "<div class='logged-in'>Logged in as <span style='color: green;'>" . $username . "</span></div>";
    if ($username == $profileToLoad) {
        $userHomePage = true;
        echo "<div class='user-home-page'>Visiting your own home page!</div><br>";
    } else {
        echo "<div class='user-profile'>Visiting <span style='color: green;'>" . $profileToLoad . "'s</span> home page!</div><br>";
    }
} else {
    $userIsGuest = true;
    echo "<div class='logged-in'>Logged in as <span style='color: orange;'>GUEST</span></div><br>";
    echo "<div class='user-profile'>Visiting <span style='color: green;'>" . $profileToLoad . "'s</span> home page!</div><br>";
}

$postsPerPage = 10;
$pageNum = 0;
if (isset($_GET['pageNum'])) {
    $pageNum = $_GET['pageNum'];
}

try {
    $client = new MongoDB\Client($uri);
    $collection = $client->$db->$col;

    $cursor = $collection->find(
        ['creator' => $profileToLoad],
        ['sort' => ['_id' => -1],
            'skip' => $pageNum * $postsPerPage,
            'limit' => 10]
    );

    if(!isset($cursor)){
        header("Location: social-feed.php");
        exit();
    }

    foreach ($cursor as $document) {
        $creator = $document['creator'];
        $content = $document['content'];
        $likes = $document['likes'];
        $likescnt = count($likes);
        $usflag = true;
        echo "<div class='post'>";
        if ($userHomePage) {
            echo "<form action='social-delete.php' method='post'>";
        } else {
            echo "<form action='social-like.php?profile=".$profileToLoad."' method='post'>";
        }
        echo "<div class='post-info'>Post by: <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a></div>";
        echo "<div class='post-content'><span style='color: blue;'>" . $content . "</span></div>";
        echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
        echo "<div class='likes'>Likes: " . $likescnt . "</div>";
        if ($userIsGuest) {
            echo "<div class='guest-message'>Guests cannot like or delete posts.</div>";
        } else if ($userHomePage) {
            $usflag = false;
            echo "<input type='submit' value='DELETE' class='delete-button'>";
        } else {
            foreach ($likes as $us) {
                if ($username == $us) {
                    echo "<div class='liked-message'>Liked!</div>";
                    $usflag = false;
                }
            }
        }
        if ($usflag && !$userHomePage) {
            echo "<input type='submit' value='Like!' class='like-button'>";
        }
        echo "</form>";
        echo "</div>"; // Close post div
    }

    $totalDocs = $collection->countDocuments(['creator' => $profileToLoad]);
    echo "<div class='pagination'>";
    if ($pageNum > 0) {
        $i = $pageNum - 1;
    } else {
        $i = 0;
    }
    echo "Pages: ";
    for (; $i < 5 && $i < $totalDocs / 10; $i++) {
        echo "<a href='social-profile.php?profile=" . $profileToLoad . "&pageNum=" . $i . "'>" . $i . "</a>";
    }
    echo "</div>";

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}
/*
// to implement later
// at top of page show username, email, num posts all from one query
try{
    $client = new MongoDB\Client($uri);
    $collection = $client->$db->Users;

    $pipeline = array(
        [
            '$match' => [
                'username' => $profileToLoad // Match the specific creator
            ]
        ],
        [
            '$lookup' => [
                'from' => 'Posts',
                'loaclField' => 'username',
                'foreignField' => 'creator',
                'as' => 'posts'
            ]
        ],
        [
            '$addFields' => array(
                'postCount' => array('$size' => '$posts')
            )
        ],
        [
            '$project' => array(
                'username' => 1,
                'email' => 1,
                'postCount' => 1
            )
        ]
    );
    foreach ($cursor as $document) {
        $email = $document['email'];
        $cnt = $document['postCount'];

        echo "Users email: " . $email . "<br>";
        echo "Number of posts: " . $cnt . "<br><br>";
    }

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}
*/
?>
</body>
</html>