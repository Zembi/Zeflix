<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Models/Model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Entities/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Entities/Page.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Entities/Session.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Templates/NotificationMsg.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Route/Route.php';

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
    new Page('Root', '-', 'index.php', null),
    new Page('Login', 'Sign In', 'Pages/login.php', true, false, true),
    new Page('Register', 'Sign Up', 'Pages/register.php', true, false, true),
    new Page('Home', 'Welcome to Zeflix', 'Pages/home.php', false, true, false),
    new Page('About', 'About Us', 'Pages/about.php', false, true, false),
]);

$user = new User($db_conn);
$session = new Session($db_conn, $route);
$session->setUser($user);

if(!isset($_POST['submitLogin']) && !isset($_POST['submitRegister'])) {
    $target_page = 'About';
    $is_user_logged_in = $session->isUserLoggedInInSession();
    if(!$route->isCurrentPagePublic()) {
        if(!$is_user_logged_in) {
            $route->redirectToPage('Login');
        }

        if($route->getCurrentPageName() == 'Root') {
            $route->redirectToPage($target_page);
        }
    }
    else {
        if($is_user_logged_in) {
            $route->redirectToPage($target_page);
        }
    }
}