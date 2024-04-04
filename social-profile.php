<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$db = 'CPS4881';
$col = 'Posts';

echo "<a href='social-feed.php'>Back to main feed</a><br><br>";

// need to have user profile in the get method
$profileToLoad = $_GET["profile"];
$userIsGuest = false;
$userHomePage = false;

if(isset($_COOKIE["user"])){
    $username = $_COOKIE["user"];
    echo "Logged in as <span style='color: green;'>" . $username . "</span><br>";
    // if this is the logged in user's page, allow them to delete posts
    if($username == $profileToLoad){
        $userHomePage = true;
        echo "Visiting <span style='color: green;'> your own </span> home page!<br><br>";
    }else{
        echo "Visiting <span style='color: green;'>" . $profileToLoad . "'s</span> home page!<br><br>";
    }
}else{
    $userIsGuest = true;
    echo "Logged in as <span style='color: orange;'> GUEST </span><br>";
    echo "Visiting <span style='color: green;'>" . $profileToLoad . "'s</span> home page!<br><br>";
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

// next do the feed function but with just this user's posts
// check what page we're on to offset query
$postsPerPage = 10;
$pageNum = 0;
if(isset($_GET['pageNum'])){
    $pageNum = $_GET['pageNum'];
}

try {
    $client = new MongoDB\Client($uri);

    $collection = $client->$db->Posts;

    // query for specific page num we're on, order needs to be reversed
    // if not reversed then gets oldest posts first :(
    $cursor = $collection->find(
        ['creator' => $profileToLoad], 
        [
            'sort' => ['_id' => -1],
            'skip' => $pageNum*$postsPerPage,
            'limit' => 10
        ]
    );

    foreach ($cursor as $document) {
        $creator = $document['creator'];
        $content = $document['content'];
        $likes = $document['likes'];
        $likescnt = count($likes);
        $usflag = true;

        if($userHomePage){
            echo "<br><form action='social-delete.php' method='post'>";
        }else{
            echo "<br><form action='social-like.php' method='post'>";
        }
        echo "<br>Post by: <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a>";
        echo "<br><span style='color: blue;'>" . $content . "</span>";
        echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
        echo "<br>Likes: " . $likescnt;
        if($userIsGuest){
            echo "<br>Guests cannot like.";
            $usflag = false;
        }else if($userHomePage){
            $usflag = false;
            echo "<input type='submit' value='DELETE'>";
        }
        else{
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
    $totalDocs = $collection->countDocuments(['creator' => $profileToLoad]);
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
        echo " <a href='social-profile.php?profile=".$profileToLoad."&&?pageNum=" . $i ."'>" . $i . "</a> ";
    }

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}

// if user deletes a post, insert post to archived collection and delete from posts collection