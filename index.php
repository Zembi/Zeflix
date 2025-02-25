<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/db/config.php';

require_once $_SERVER['DOCUMENT_ROOT'].'/Entities/User.php';

global $db_conn;
global $route;
global $session;

$route->redirectToPage($_SESSION['last_visited_page']);

$user = new User($db_conn);
