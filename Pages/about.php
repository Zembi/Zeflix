<?php

global $db_conn;
global $route;
global $session;
global $user;
global $page;

//$session->successfulSingOut($user);
?>
</head>
<body>
    <?= $user->getLastName(); ?>
</body>
