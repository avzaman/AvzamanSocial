<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$db = 'CPS4881';
$col = 'Posts';

$username = $_COOKIE["user"];
$content = $_POST["content"];

try {
    $client = new MongoDB\Client($uri);

    $collection = $client->$db->$col;

    $currentDateTime = date('Y-m-d H:i:s');

    $insertOneResult = $collection->insertOne([
        'creator' => $username,
        'content' => $content,
        'likes' => [],
        'datetime' => $currentDateTime,
    ]);

    header("Location: social-feed.php");
    exit();

    //printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
    //var_dump($insertOneResult->getInsertedId());

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}