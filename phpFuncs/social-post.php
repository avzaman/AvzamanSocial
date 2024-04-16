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
        if (isset($_FILES["image"])) {
            // check if type is in dict of accepted types and less than 2mb
            echo "<img src='" . $_FILES["image"]["tmp_name"] . "' alt='image in a post'>";
            $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            if (in_array($fileExtension, $imgTypes)) {
                if ($fileSize = $_FILES["image"]["size"] < $maxFileSize) {
                    // Specify the destination directory
                    $destinationDirectory = __DIR__ . "/../img/posts/";

                    // Generate a unique filename
                    $imagePath = $username . $currentDateTime . '_' . $_FILES["image"]["name"];

                    if(!is_writable($_FILES["image"]["tmp_name"])){
                        echo "image is set but tmp is not writeable";
                        exit();
                    }

                    if(!isset($_FILES["image"]["tmp_name"])){
                        echo "image is set tmp_name isn't";
                        exit();
                    }

                    if(!rename($_FILES["image"]["tmp_name"], $destinationDirectory . $imagePath)){
                        echo "image is set but not moved\n";
                        echo $fileExtension . "\n";
                        var_dump($_FILES['image']['error']);
                        exit();
                    }

                    chmod($destinationDirectory . $imagePath, 777);

                }else{
                    echo "image is set but too large";
                    exit();
                }
            } else {
                echo "image is set but type not accepted";
                exit();
            }
        } else {
            echo "image is not set";
            exit();
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