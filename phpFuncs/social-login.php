<?php
//if login failed, link back to login screen with popup
//if successfull, show feedrequire_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';
$col = 'Users';

$username = $_POST["username"];
$userpassword = $_POST["password"];
if($_POST['stay-logged']){
    $cookieLength = 2592000;
}else{
    $cookieLength = 3600;
}


try {
    $client = new MongoDB\Client($uri);

    //echo "Connected to MongoDB successfully!<br>";

    $collection = $client->$db->$col;

    $document = $collection->findOne(['username' => $username]);

    if($document){
        if($userpassword == $document['password']){
            setcookie("user",$username, time()+$cookieLength, "/");
            header("Location: ../social-feed.php");
            exit();
        }else{
            echo "Password incorrect!";
        }
        
    }else{
        echo "<br>No user found.";
    }



} catch (Exception $e) {
    echo "Failed to connect to MongoDB: " . $e->getMessage();
}