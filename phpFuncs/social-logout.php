<?php
setcookie("user", "", time() - 3600,"/");
unset($_COOKIE['user']);
header("Location: ../index.php");
exit();
