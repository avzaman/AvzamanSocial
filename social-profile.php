<?php
    include_once 'social-header.php';
?>
    
    <?php
    require_once __DIR__ . '/vendor/autoload.php';

    //config.php holds the uri and db name and any login info needed for db operations
    include 'phpFuncs/dbconfig.php';

    $col = 'Posts';

    $profileToLoad = $_GET["profile"];
    $userHomePage = false;

    if ($logged_in && $username == $profileToLoad) {
        $userHomePage = true;
    }
    
    echo "<div class='user-profile'><span style='color: green;'>" . $profileToLoad . "'s</span> home page!</div><br>";


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
            $user_liked = false; //checks if user has liked message, false by default

            echo "<div class='post'>";
            if ($userHomePage) {
                echo "<form action='phpFuncs/social-delete.php' method='post'>";
            } else {
                echo "<form action='phpFuncs/social-like.php?profile=" . $profileToLoad . "' method='post'>";
            }
            echo "<div class='post-info'>Post by: <a href='social-profile.php?profile=" . $creator . "'>" . $creator . "</a></div>";
            echo "<div class='post-content'><span style='color: blue;'>" . $content . "</span></div>";

            if(isset($document['image'])){
                echo "<img src='img/posts/" . $document['image'] . "' width='240' height='427' alt='image in a post'>";
            }

            echo "<input type='hidden' name='postid' value='" . $document['_id'] . "'>";
            echo "<div class='likes'>Likes: " . $likescnt . "</div>";
            if ($userIsGuest) {
                echo "<div class='guest-message'>Guests cannot like or delete posts.</div>";
                $user_liked = true;
            } else if ($userHomePage) {
                $user_liked = true;
                echo "<input type='submit' value='DELETE' class='delete-button'>";
            } else {
                foreach ($likes as $us) {
                    if ($username == $us) {
                        echo "<div class='liked-message'>Liked!</div>";
                        $user_liked = true;
                        break;
                    }
                }
            }
            if (!$user_liked && !$userHomePage) {
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