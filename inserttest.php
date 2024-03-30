<?php
require_once __DIR__ . '/~avzaman/public_html/vendor/autoload.php';

$uri = "mongodb://localhost:27017";
$dbName = "testdb";
$collectionName = "testcollection";

$fname = $_GET["fname"];
$lname = $_GET["lname"];
$email = $_GET["email"];
$age = $_GET["age"];

try {
    $client = new MongoDB\Client($uri);

    echo "Connected to MongoDB successfully!<br>";

    $collection = $client->testdb->testcollection;

    $insertOneResult = $collection->insertOne([
        'fname' => $fname,
        'lname' => $lname,
        'email' => $email,
        'age' => $age,
    ]);

    printf("Inserted %d document(s)\n", $insertOneResult->getInsertedCount());
    var_dump($insertOneResult->getInsertedId());

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}
