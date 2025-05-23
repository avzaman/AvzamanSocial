# "V" Social Media Clone of "X" (formerly Twitter)
This project serves as an exercise in a practical application of a webapp interaction with a NoSql database.<br>

[**You can explore the live demo here**](https://d173s6iimxj6sw.cloudfront.net/AvzamanSocial/social-feed.php)

### Summary
All functionality, frontend and backend, are built with PHP. Database interaction is accomplished with the MongoDB PHP library.<br>
Users are able to register, login, post text and images, comment on posts, like posts, unlike posts, delete their own posts, and explore via pagination or clicking on user profile names.<br>
The test user base was about 15 people. I reached out to directly.<br>
[Declan Blanchard](https://github.com/declanblanc) assisted in styling and reviewing code logic.

### Setup
Install mongo php libraries to this project directory. The compser files have been added to the .gitignore.

dbconfig template:

<?php
// for locally hosted db use "mongodb://localhost:27017", port 27017 is default mongo
$uri = ""; 
$db = '';
?>