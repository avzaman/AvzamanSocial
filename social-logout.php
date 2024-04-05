<?php
setcookie("user", "", time() - 3600,"/", "example.com");
unset($_COOKIE['user']);
header("Location: index.html");
exit();
