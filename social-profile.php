<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Feed</title>
    <link rel="stylesheet" href="css/social-profile.css?v=<?php echo time(); ?>">
</head>

<body>
    <p>
        Welcome to the Profile Page!
        This page shows all posts in the database 10 posts at a time for a specific user's profile.
        Here, if the the current logged in user is viewing their own profile they may delete posts.
        However, posts are never deleted permanently, they are archived and not displayed in the feed or profile pages anymore.
    </p>
    <?php
    require_once __DIR__ . '/vendor/autoload.php';

    //config.php holds the uri and db name and any login info needed for db operations
    include 'phpFuncs/dbconfig.php';

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
            [
                'sort' => ['_id' => -1],
                'skip' => $pageNum * $postsPerPage,
                'limit' => 10
            ]
        );

        if (!isset($cursor)) {
            header("Location: social-feed.php");
            exit();
        }

        foreach ($cursor as $document) {
            $creator = $document['creator'];
            $content = $document['content'];
            $likes = $document['likes'];
            $likescnt = count($likes);
            $usflag = true; //checks if user has liked message

            echo "<div class='post'>";
            if ($userHomePage) {
                echo "<form action='phpFuncs/social-delete.php' method='post'>";
            } else {
                echo "<form action='phpFuncs/social-like.php?profile=" . $profileToLoad . "' method='post'>";
            }
            echo "<div class='post-info'>Post by: <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a></div>";
            echo "<div class='post-content'><span style='color: blue;'>" . $content . "</span></div>";
            echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
            echo "<div class='likes'>Likes: " . $likescnt . "</div>";
            if ($userIsGuest) {
                echo "<div class='guest-message'>Guests cannot like or delete posts.</div>";
                $usflag = false;
            } else if ($userHomePage) {
                $usflag = false;
                echo "<input type='submit' value='DELETE' class='delete-button'>";
            } else {
                foreach ($likes as $us) {
                    if ($username == $us) {
                        echo "<div class='liked-message'>Liked!</div>";
                        $usflag = false;
                        break;
                    }
                }
            }
            if ($usflag && !$userHomePage) {
                echo "<input type='submit' value='Like!' class='like-button'>";
            }
            echo "</form>";

            //if the post has replies print them
            if (isset($document['replies'])) {
                echo "<div class='replies'>";
                foreach ($document['replies'] as $reply) {
                    $replyCreator = $reply['reply-creator'];
                    $replyContent = $reply['reply-content'];

                    echo "<br>user <a href='social-profile.php?profile=" . $replyCreator . "'>" . $replyCreator . "</a> replied:<br>";
                    echo $replyContent . "<br>";
                }
                echo "</div>";

            }

            //form to reply, logged in user from cookie will be used
            //no reply allowed if user is guest
            if (!$userIsGuest) {
                echo "<form action='phpFuncs/social-reply.php?profile=" . $profileToLoad .  "' method='post' class='reply-form'>";
                echo "<input type='text' name='reply-content' required><br>";
                echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
                echo "<input type='submit' value='Reply' class='reply-button'>";
                echo "</form>";
            }
            echo "</div>";
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
    ?>
</body>

</html>