<?php
//if regsiter successful, show "new user created <link to login page">
//if failed, show email already has account, or username already used

require_once __DIR__ . '/../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';
$col = 'Users';

$username = $_POST["username"];
$userpassword = $_POST["password"];
$useremail = $_POST["email"];

//first query for username, then query for email, if both empty -> make new entry using username email password

try {
    $unameFlag = false;
    $emailFlag = false;
    $client = new MongoDB\Client($uri);

    //echo "Connected to MongoDB successfully!<br>";

    $collection = $client->$db->$col;
    //check if uname free
    $document = $collection->findOne(['username' => $username]);

    if ($document || strlen($username) > 30) {
        $unameFlag = true;
    }

    //check if email free
    $document = $collection->findOne(['email' => $useremail]);

    if ($document || strlen($useremail) > 30) {
        $emailFlag = true;
    }

    if (strlen($userpassword) > 30) {
        echo "Password too long";
        exit();
    }

    //if both free insert
    if ($unameFlag) {
        echo "Username is taken or is too long!";
    } else if ($emailFlag) {
        echo "Email is already in use or is too long.";
    } else {
        $currentDateTime = date('Y-m-d H:i:s');
        $insertOneResult = $collection->insertOne([
            'username' => $username,
            'email' => $useremail,
            'password' => $userpassword,
            'datetime' => $currentDateTime,
        ]);
        header("Location: ../index.php");
    }

} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}