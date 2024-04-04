<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = "mongodb://localhost:27017";

$username = $_COOKIE["user"];
$pid = $_POST["postid"];

try {
    $client = new MongoDB\Client($uri);

    $collection = $client->CPS4881->Posts;

    $update = $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectID($pid)],
        ['$push' => ['likes' => $username]]
    );

    header("Location: social-feed.php");
    exit();

    //printf("Matched %d document(s)\n", $update->getMatchedCount());
    //printf("Modified %d document(s)\n", $update->getModifiedCount());

    //printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
    //var_dump($insertOneResult->getInsertedId());

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}