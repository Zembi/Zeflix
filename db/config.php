<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/Models/Model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Entities/Session.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Templates/NotificationMsg.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Route/Route.php';

ob_start();

date_default_timezone_set("Europe/Athens");

$dbhost = getenv('DB_HOST');
$dbport = getenv('DB_PORT');
$dbcharset = getenv('DB_CHARSET');
$dbname = getenv('DB_NAME');
$dbuser = getenv('DB_USER');
$dbpassword = getenv('DB_PASSWORD');

try {
    $db_conn = new PDO("mysql:host=$dbhost;port=$dbport;charset=$dbcharset;dbname=$dbname;", $dbuser, $dbpassword);
    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    echo "<strong>Connection with database failed:</strong> ".$e->getMessage();
}


$route = new Route(basename($_SERVER['SCRIPT_FILENAME']));
$route->addPages([
    'Root' => 'index.php',
    'Login' => 'Login.php',
    'Register' => 'Register.php',
    'Home' => 'Home.php',
    'About' => 'About.php',
]);


$session = new Session($db_conn, $route);
$session->noTokenRedirect('login.php');