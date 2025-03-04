<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

    require_once $_SERVER['DOCUMENT_ROOT'].'/Entities/User.php';

    global $db_conn;
    global $route;
    global $session;

    $user = new User($db_conn);

    if(isset($_POST['submitLogin'])) {
        $form_data = [
            'username' => $_POST['username'],
            'password' => $_POST['password'],
        ];
        $login_response = $user->login($form_data);
        $session->collectLogInResponseIntoSession($login_response);
    }

    $response = $session->handleLogInResponseFromSession();

    $errors = $response['errors'];

    $last_inputs = $response['last_inputs'];
    $username = htmlspecialchars($last_inputs['username'] ?? '');

    $success_message = htmlspecialchars($response['success']);
?>

<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Welcome to Zeflix</title>
        <link rel="stylesheet" type="text/css" href="../assets/style/style.css" />
    </head>
    <body>
        <?php
            if(!empty($success_message)) {
                NotificationMsg::successfulNotify(htmlspecialchars($success_message));
                $session->successfulSingIn($user);
            }
            else if(count($errors) > 0) {
                NotificationMsg::errorNotify('One or more errors are found in the log in form');
            }
        ?>
        <div class="sing-in-container">
            <div class="column">
                <img class="zeflix-logo" src="../assets/images/zeflix_image.png" title="Zeflix" alt="zeflix logo" />
                <h2 class="title">Sign In</h2>
                <span class="subtitle simple-text">to continue to VideoTube</span>
                <form class="sing-in-form account-entry-form" method="POST" action="">
                    <label class="form-label-field">
                        <input type="text" name="username" class="simple-text<?= isset($errors['username']) ? ' error_field' : '' ?>" placeholder="Username" value="<?= $username ?>" required/>
                        <?php if(isset($errors['username'])) { ?>
                            <span class="error-form-msg"><?= $errors['username'] ?></span>
                        <?php } ?>
                    </label>
                    <label class="form-label-field">
                        <input type="password" name="password" class="simple-text<?= isset($errors['password']) ? ' error_field' : '' ?>"  placeholder="Password" required/>
                        <?php if(isset($errors['password'])) { ?>
                            <span class="error-form-msg"><?= $errors['password'] ?></span>
                        <?php } ?>
                    </label>

                    <input type="submit" name="submitLogin" class="simple-text" value="Log in"/>
                </form>
                <span>Don't have an account? <a href="<?= $route->getPageLink('Register') ?>">Sign Up here!</a></span>
            </div>
        </div>
    </body>
</html>