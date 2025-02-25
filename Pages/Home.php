<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/db/config.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Entities/User.php';

global $db_conn;
global $route;
global $session;

$user = new User($db_conn);

?>
<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Home</title>
    </head>
    <body>

    </body>
</html>
