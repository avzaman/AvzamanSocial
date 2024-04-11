<?php
require_once __DIR__ . '../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';
$col = 'Posts';

$username = $_COOKIE["user"];
$content = $_POST["content"];
if (strlen($content) > 0 && strlen($content) <= 250) {
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

    } catch (Exception $e) {
        echo "Failed to connect to MongoDB: " . $e->getMessage();
    }
}
header("Location: ../social-feed.php");
exit();