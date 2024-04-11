<?php
require_once __DIR__ . '../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';
$col = 'Posts';

$replyCreator = $_COOKIE["user"];
$pid = $_POST["postid"];
$replyContent = $_POST["reply-content"];
$currentDateTime = date('Y-m-d H:i:s');

if (strlen($replyContent) > 0 && strlen($replyContent) <= 250) {
    try {
        $client = new MongoDB\Client($uri);

        $collection = $client->$db->Posts;

        $update = $collection->updateOne(
            ['_id' => new MongoDB\BSON\ObjectID($pid)],
            ['$push' => ['replies' => ["reply-creator" => $replyCreator, "reply-content" => $replyContent, "datetime" => $currentDateTime]]]
        );

        if (isset($_GET["profile"])) {
            header("Location: ../social-profile.php?profile=" . $_GET["profile"]);
            exit();
        }

        header("Location: ../social-feed.php");
        exit();

    } catch (Exception $e) {
        echo "Failed to connect to MongoDB: " . $e->getMessage();
    }
}