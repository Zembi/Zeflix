<?php

use Items\User;
use Items\Page\Page;
use Items\Page\Public_Page;
use Items\Page\Private_Page;
use Items\Session;
use Route\Route;

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
// IMPORTANT METHOD AS IT REGISTERS ALL AVAILABLE PAGES
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

$page = $target_page;
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $page->getTitle() ?></title>
    <link rel="stylesheet" type="text/css" href="../assets/style/style.css" />

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/' . $page->getFilePath(); ?>

</html>