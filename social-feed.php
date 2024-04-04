<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$db = 'CPS4881';
$col = 'Posts';

// most users will be guests observing the functions
$userIsGuest = false;
if (isset($_COOKIE['user'])) {
    $username = $_COOKIE["user"];
    echo "Logged in as <span style='color: green;'>
        <a href='social-profile.php?profile=" . $username . "'>" . $username . "</a></span><br><br>";
    echo "<a href='social-logout.php'>Logout</a><br><br>";

    // if a user is logged in allow them to create a post
    echo "<form action='social-post.php' method='post'>";
    echo "<label for='textbox'>Enter post text:</label>";
    echo "<input type='text' name='content' required><br><br>";
    echo "<input type='submit' value='Post!'></form>";
} else {
    $userIsGuest = true;
    echo "Logged in as <span style='color: orange;'> GUEST </span><br><br>";
}

// next is the actual feed
// check what page we're on to offset query
$postsPerPage = 10;
$pageNum = 0;
if(isset($_GET['pageNum'])){
    $pageNum = $_GET['pageNum'];
}

try {
    

    $client = new MongoDB\Client($uri);

    $collection = $client->$db->$col;

    // query for specific page num we're on, order needs to be reversed
    // if not reversed then gets oldest posts first :(
    $cursor = $collection->find(
        [], 
        ['sort' => ['_id' => -1],
        'skip' => $pageNum*$postsPerPage,
        'limit' => 10]
    );


    foreach ($cursor as $document) {
        $creator = $document['creator'];
        $content = $document['content'];
        $likes = $document['likes'];
        $likescnt = count($likes);
        $usflag = true;

        echo "<br><form action='social-like.php' method='post'>";
        echo "<br>Post by: <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a>";
        echo "<br><span style='color: blue;'>" . $content . "</span>";
        echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
        echo "<br>Likes: " . $likescnt;
        if($userIsGuest){
            echo "<br>Guests cannot like.";
            $usflag = false;
        }else{
            foreach ($likes as $us) {
                if ($username == $us) {
                    echo "<br>Liked!";
                    $usflag = false;
                }
            }
        }
        if ($usflag) {
            echo "<input type='submit' value='Like!'>";
            
        }
        echo "</form>";
    }

    // now loop to show other avaiable pages
    $totalDocs = $collection->countDocuments();
    echo "<br>";

    // this allows for one page back to be clicked
    if($pageNum > 0){
        $i = $pageNum - 1;
    }else{
        $i = 0;
    }

    // this allows for the next 5 pages to be selected up to the total possible pages
    echo "Pages: ";
    for(; $i < 5 && $i < $totalDocs/10; $i++){
        echo " <a href='social-feed.php?pageNum=" . $i ."'>" . $i . "</a> ";
    }

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}
