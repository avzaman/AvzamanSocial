<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Feed</title>
    <link rel="stylesheet" href="css/social-feed.css?v=<?php echo time(); ?>">
</head>

<body>
    <?php
    require_once __DIR__ . '/vendor/autoload.php';

    //config.php holds the uri and db name and any login info needed for db operations
    include 'phpFuncs/dbconfig.php';

    $col = 'Posts';

    // most users will be guests observing the functions
    $userIsGuest = false;
    if (isset($_COOKIE['user'])) {
        $username = $_COOKIE["user"];
        echo "<p class='logged-in'>Logged in as <span>
        <a href='social-profile.php?profile=" . $username . "'>" . $username . "</a></span><br>";
        echo "<a href='social-logout.php' class='logout'>Logout</a><br></p>";

        // if a user is logged in allow them to create a post
        echo "<form action='phpFuncs/social-post.php' method='post' class='post-form'>";
        echo "<label for='textbox'>Enter post text:</label>";
        echo "<input type='text' name='content' required><br>";
        echo "<input type='submit' value='Post!'>";
        echo "</form>";
    } else {
        $userIsGuest = true;
        echo "<p class='guest'>Logged in as <span>GUEST</span></p>";
    }

    // next is the actual feed
    // check what page we're on to offset query
    $postsPerPage = 10;
    $pageNum = 0;
    if (isset($_GET['pageNum'])) {
        $pageNum = $_GET['pageNum'];
    }

    try {


        $client = new MongoDB\Client($uri);

        $collection = $client->$db->$col;

        // query for specific page num we're on, order needs to be reversed
        // if not reversed then gets oldest posts first :(
        $cursor = $collection->find(
            [],
            [
                'sort' => ['_id' => -1],
                'skip' => $pageNum * $postsPerPage,
                'limit' => 10
            ]
        );


        foreach ($cursor as $document) {
            if (isset($document['creator'])) {
                $creator = $document['creator'];
                $content = $document['content'];
                $likes = $document['likes'];
                $likescnt = count($likes);
                $usflag = true; //this flag checks if logged in user has liked a messege
    
                echo "<div class='post'>";
                echo "<p class='post-header'>Post by: <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a></p>";
                echo "<p class='post-content'>" . $content . "</p>";

                echo "<p class='likes'>Likes: " . $likescnt . "</p>";
                if ($userIsGuest) {//if user is guest then no like allowed
                    echo "<p class='likes'>Guests cannot like.</p>";
                    $usflag = false;
                } else {
                    foreach ($likes as $us) {//if the logged in user has liked the post switch the flag
                        if ($username == $us) {
                            echo "<p class='liked-message'>Liked!</p>";
                            $usflag = false;
                        }
                    }
                }
                if ($usflag) {//if the logged in user hasn't liked the post then print like buttoon
                    echo "<form action='phpFuncs/social-like.php' method='post' class='like-form'>";
                    echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
                    echo "<input type='submit' value='Like!' class='like-button'>";
                    echo "</form>";
                }

                //here is where I will put replies, first dropdown to open all replies, followed by reply post
    
                //if the post has replies make the dropdown
                if (isset($document['replies'])) {
                    foreach ($document['replies'] as $reply) {
                        $replyCreator = $reply['reply-creator'];
                        $replyContent = $reply['reply-content'];

                        echo "<br>user <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a> replied:<br>";
                        echo $replyContent . "<br>";
                    }
                    echo "</div>";

                }

                //form to reply, logged in user from cookie will be used
                //no reply allowed if user is guest
                if (!$userIsGuest) {
                    echo "<form action='phpFuncs/social-reply.php' method='post' class='reply-form'>";
                    echo "<input type='text' name='reply-content' required><br>";
                    echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
                    echo "<input type='submit' value='Reply' class='reply-button'>";
                    echo "</form>";
                }
                echo "</div>";
            }
        }

        // now loop to show other available pages
        $totalDocs = $collection->countDocuments();
        echo "<div class='pagination'>";

        // this allows for one page back to be clicked
        if ($pageNum > 0) {
            $i = $pageNum - 1;
        } else {
            $i = 0;
        }

        // this allows for the next 5 pages to be selected up to the total possible pages
        echo "Pages: ";
        for (; $i < 5 && $i < $totalDocs / 10; $i++) {
            echo " <a href='social-feed.php?pageNum=" . $i . "'>" . $i . "</a> ";
        }

        echo "</div>";

    } catch (Exception $e) {
        echo "<p>Failed to connect to MongoDB: " . $e->getMessage() . "</p>";
    }
    ?>
</body>

</html>