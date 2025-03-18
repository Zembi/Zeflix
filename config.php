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
    exit();
}


$route = new Route($_SERVER['SCRIPT_FILENAME']);
$route->handleAllPages([
    new Page('Root', '-', 'index.php', 'root',null),
    new Page('404', 'Page not found', 'Pages/404.php', '404',null),
    new Page('Login', 'Sign In', 'Pages/login.php', 'login', true, false, true),
    new Page('Register', 'Sign Up', 'Pages/register.php', 'register', true, false, true),
    new Page('Home', 'Welcome to Zeflix', 'Pages/home.php', 'home',false, true, false),
    new Page('About', 'About Us', 'Pages/about.php', 'about',false, true, false),
]);


$user = new User($db_conn);
$session = new Session($db_conn, $route);
$session->setUser($user);

$routeName = isset($_GET['view']) ? strtolower(trim($_GET['view'], '/')) : 'root';

$page = $route->findPageByMaskName($routeName);

if(!$page) {
    $page = $route->getErrorPage();
}

//include $_SERVER['DOCUMENT_ROOT'] . '/' . $page->getFilePath();


if(!isset($_POST['submitLogin']) && !isset($_POST['submitRegister'])) {
    $target_page = 'home';
    $is_user_logged_in = $session->isUserLoggedInInSession();
    if(!$route->isCurrentPagePublic()) {
        if(!$is_user_logged_in) {
            $page = $route->findPageByMaskName('login');
            include $_SERVER['DOCUMENT_ROOT'] . '/' . $page->getFilePath();
//            $route->redirectToPage('login');
        }
        else if($route->getCurrentPageName() == 'Root') {
//            $page = $route->findPageByMaskName($target_page);
//            include $_SERVER['DOCUMENT_ROOT'] . '/' . $page->getFilePath();
//            $route->redirectToPage($target_page);
        }
    }
    else {
        if($is_user_logged_in) {
//            include $_SERVER['DOCUMENT_ROOT'] . '/' . $page->getFilePath();
//            $route->redirectToPage($target_page);
        }
    }
}