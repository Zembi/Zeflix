<?php

global $db_conn;
global $route;
global $session;
global $user;
global $page;

?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $page->getTitle() ?></title>
</head>
<body>
<?php
    http_response_code(403);
    echo "<h1>403 Forbidden</h1>";
    echo "<p>You don't have permission to access this page.</p>";
?>
</body>
</html>
