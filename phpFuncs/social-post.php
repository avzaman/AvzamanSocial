<?php
require_once __DIR__ . '/../vendor/autoload.php';

//config.php holds the uri and db name and any login info needed for db operations
include 'dbconfig.php';
$col = 'Posts';

$username = $_COOKIE["user"];
$content = $_POST["content"];
$imagePath = null;
$imgTypes = array(
    "jpg",
    "jpeg",
    "png",
    "gif",
    "bmp",
    "heic"
);
$maxFileSize = 2 * 1024 * 1024; // 2 MB * 1024 KB/MB * 1024 bytes/KB
if (strlen($content) > 0 && strlen($content) <= 250) {
    try {
        $client = new MongoDB\Client($uri);

        $collection = $client->$db->$col;

        

        $currentDateTime = date('Y-m-d H:i:s');

        // if there is an image save it to images/posts
        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){
            // check if type is in dict of accepted types and less than 2mb
            if(in_array($fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION), $imgTypes) && $fileSize = $_FILES["fileToUpload"]["size"] < $maxFileSize){
                // Specify the destination directory
                $destinationDirectory = "../img/posts/";
        
                // Generate a unique filename
                $imagePath = $username . $currentDateTime . '_' . $_FILES["fileToUpload"]["name"];

                move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $destinationDirectory . $imagePath);
            }
        }

        $insertOneResult = $collection->insertOne([
            'creator' => $username,
            'content' => $content,
            'likes' => [],
            'image' => $imagePath,
            'datetime' => $currentDateTime,
        ]);

    } catch (Exception $e) {
        echo "Failed to connect to MongoDB: " . $e->getMessage();
    }
}
header("Location: ../social-feed.php");
exit();