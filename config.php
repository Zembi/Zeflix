<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/Models/Model.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Items/User.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Items/Page/Page.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Items/Page/Public_Page.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Items/Page/Private_Page.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/Items/Session.php';
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
    exit();
}

$maskName = isset($_GET['view']) ? strtolower(trim($_GET['view'], '/')) : 'root';

$route = new Route($maskName);
$route->handleAllPages([
    new Page('Root', '-', 'index.php', 'root'),

    new Page('404', 'Page not found', 'Pages/404.php', '404'),
    new Page('403', 'Forbidden Access', 'Pages/403.php', '403'),

    new Public_Page('Login', 'Sign In', 'Pages/login.php', 'login'),
    new Public_Page('Register', 'Sign Up', 'Pages/register.php', 'register'),
    
    new Private_Page('Home', 'Welcome to Zeflix', 'Pages/home.php', 'home'),
    new Private_Page('About', 'About Us', 'Pages/about.php', 'about'),
]);


$user = new User($db_conn);
$session = new Session($db_conn, $route);
$session->setUser($user);

// HANDLE 404 PAGE
if(!$route->currPageExists()) {
    $target_page = $route->getErrorPage();
}
// IF PAGE EXISTS IN ROUTE
else {
    $target_page = $route->getCurrentPage();

    $is_user_logged_in = $session->isUserLoggedInInSession();

    if(!isset($_POST['submitLogin']) && !isset($_POST['submitRegister'])) {
        if($target_page->getName() == 'Root') {
            if($is_user_logged_in) {
                $target_page = $route->findPageByMaskName('home');
            }
            else {
                $target_page = $route->findPageByMaskName('login');
            }
            $route->redirectToPage($target_page);
            exit;
        }

        if($target_page->isOnlyForLoggedInUsers() && !$is_user_logged_in) {
            $target_page = $route->findPageByMaskName('login');
            $route->redirectToPage($target_page);
            exit;
        }

        else if($target_page->isOnlyForVisitors() && $is_user_logged_in) {
            $target_page = $route->findPageByMaskName('home');
            $route->redirectToPage($target_page);
            exit;
        }
    }
}

$page = $target_page;
include $_SERVER['DOCUMENT_ROOT'] . '/' . $page->getFilePath();