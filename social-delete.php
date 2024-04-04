<?php
require_once __DIR__ . '/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$db = 'CPS4881';
$col = 'Posts';

$username = $_COOKIE["user"];
$pid = $_POST["postid"];

try {
    $client = new MongoDB\Client($uri);

    $postsCollection = $client->$db->Posts;
    $archiveCollection = $client->$db->Archive;

    // first get the post to transfer
    $document = $postsCollection->findOne(['_id' => new MongoDB\BSON\ObjectID($pid)]);

    if ($document) {
        // Insert the document into the Archive collection
        $archiveCollection->insertOne($document);

        // Delete the document from the Posts collection
        $result = $postsCollection->deleteOne(['_id' => new MongoDB\BSON\ObjectID($pid)]);

        // Output the result of deletion
        if ($result->getDeletedCount() > 0) {
            //echo "Document with _id: $pid deleted from Posts collection.";
        } else {
            echo "Document with _id: $pid not found in Posts collection.";
            exit();
        }
    } else {
        echo "Document with _id: $pid not found in Posts collection.";
        exit();
    }

    header("Location: social-profile.php?profile=" . $username);
    exit();

    //printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
    //var_dump($insertOneResult->getInsertedId());

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}