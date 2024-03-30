<?php
require_once '/home/avzaman/public_html/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$dbName = "testdb";
$collectionName = "testcollection";

$fname = $_GET["fname"];

try {
    $client = new MongoDB\Client($uri);

    echo "Connected to MongoDB successfully!<br>";

    $collection = $client->testdb->testcollection;

    $document = $collection->findOne(['fname' => $fname]);

    if($document){
        echo "<br>First Name: " . $document['fname'];
        echo "<br>Last Name: " . $document['lname'];
        echo "<br>Email: " . $document['email'];
        echo "<br>Age: " . $document['age'];
    }else{
        echo "<br>No record found.";
    }

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}