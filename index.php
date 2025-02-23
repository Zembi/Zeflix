<?php

require_once './db/config.php';

require_once './Entities/User.php';

global $db_conn;

$session = new Session($db_conn);
$user = new User($db_conn);
var_dump($_SESSION);
//if()
echo 'hello';