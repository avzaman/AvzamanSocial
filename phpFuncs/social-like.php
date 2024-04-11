<?php
require_once __DIR__ . '/../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';

$username = $_COOKIE["user"];
$pid = $_POST["postid"];


try {
    $client = new MongoDB\Client($uri);

    $collection = $client->CPS4881->Posts;

    $update = $collection->updateOne(
        ['_id' => new MongoDB\BSON\ObjectID($pid)],
        ['$push' => ['likes' => $username]]
    );

    if(isset($_GET["profile"])){
        header("Location: ../social-profile.php?profile=".$_GET["profile"]);
        exit();
    }

    header("Location: ../social-feed.php");
    exit();

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}