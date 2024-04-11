<?php
//if login failed, link back to login screen with popup
//if successfull, show feedrequire_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';
$col = 'Users';

$username = $_POST["username"];
$userpassword = $_POST["password"];

try {
    $client = new MongoDB\Client($uri);

    //echo "Connected to MongoDB successfully!<br>";

    $collection = $client->$db->$col;

    $document = $collection->findOne(['username' => $username]);

    if($document){
        if($userpassword == $document['password']){
            setcookie("user",$username, time()+3600);
            header("Location: social-feed.php");
            exit();

            //echo "<br>Username: " . $document['username'];
            //echo "<br>Email: " . $document['email'];
        }else{
            echo "Password incorrect!";
        }
        
    }else{
        echo "<br>No user found.";
    }



} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}