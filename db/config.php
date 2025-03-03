<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/Models/Model.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/Entities/Page.php';
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


$route = new Route($_SERVER['SCRIPT_FILENAME']);
$route->addAllPages([
    'root' => new Page('Root', '-', 'index.php', null),
    'login' => new Page('Login', 'Sign In', 'Pages/login.php', true, false, true),
    'register' => new Page('Register', 'Sign Up', 'Pages/register.php', true, false, true),
    'home' => new Page('Home', 'Welcome to Zeflix', 'Pages/home.php', false, true, false),
    'about' => new Page('About', 'About Us', 'Pages/about.php', false, true, false),
]);

$session = new Session($db_conn, $route);

if(!$route->isCurrentPagePublic()) {
    $session->noTokenRedirect('Login');
}