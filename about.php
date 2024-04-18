<?php
include_once 'social-header.php';
?>

<p class="intro">
    Welcome to V!<br>
    The feed shows all posts in the database 10 posts at a time.<br>
    Each post is stored in mongodb as a json object with several fields including unseen info such as datetime.<br>
    The replies are kept in the posts collection as subdocuments.<br>
    The images are saved on the server with a unique name, that name is stored as a string in the post's JSON to retrieve when loading posts. <br>
    Pipelining arguments using the MongoDB PHP library is used in the php functions for reply appending.
    Likes are stored as an array in individual post documents to track what users have liked what post.
</p>

<p class="intro">
        The Profile Page shows all posts in the database 10 posts at a time for a specific user's profile.
        There, if the the current logged in user is viewing their own profile they may delete posts.
        However, posts are never deleted permanently, they are archived and not displayed in the feed or profile pages anymore.
    </p>